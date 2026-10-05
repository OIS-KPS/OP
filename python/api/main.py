"""
FastAPI service wrapping HybridJournalPipeline for HTTP-based entity extraction.

Startup loads the model once; each POST /extract request runs inference,
deduplicates entities by (term, category) with frequency counts, and returns
a response shaped for the PHP frontend (or any caller).
"""

import os
import sys
import re
import logging
import tempfile
from contextlib import asynccontextmanager
from collections import defaultdict
from typing import Dict, Any, Optional, Tuple

from fastapi import FastAPI, HTTPException, UploadFile, File
from fastapi.responses import JSONResponse
from pydantic import BaseModel, field_validator

# ---------------------------------------------------------------------------
# Path bootstrap — ensure project root is importable regardless of cwd
# ---------------------------------------------------------------------------
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

from scripts.pipeline import HybridJournalPipeline

logger = logging.getLogger("ojt_pipeline.api")

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------
# 100 000 characters ≈ 40 pages of dense text — generous enough for real
# journal entries, small enough to prevent the endpoint from hanging on
# pathological multi-MB payloads.  Transformer tokenisers also have their
# own internal limits (512 tokens for most BERT-family models), so extremely
# long text wouldn't improve results anyway.
MAX_TEXT_LENGTH = 100_000

# Upload ceiling for POST /extract-pdf. A scanned weekly report exported from
# Word lands far below this; the guard exists so a mistyped upload cannot fill
# the temp directory.
MAX_PDF_BYTES = 25 * 1024 * 1024

# Whitespace normalisation regex — mirrors the lowercase + collapse-whitespace
# approach already used by the pipeline's EntityRuler (phrase_matcher_attr=LOWER)
# and the _lower_terms_set dictionary lookup.
_WS_RE = re.compile(r"\s+")


def _normalise(text: str) -> str:
    """Lowercase and collapse internal whitespace for dedup key generation."""
    return _WS_RE.sub(" ", text.strip().lower())


# ---------------------------------------------------------------------------
# Global pipeline reference (populated at startup)
# ---------------------------------------------------------------------------
_pipeline: Optional[HybridJournalPipeline] = None

# Lazily-imported ``extract_entities`` module (PDF/WAR column helpers).
_pdf_module = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Load the heavy pipeline once when the server starts."""
    model_path = os.environ.get("MODEL_PATH", "models/ner_trf/model-best")
    logger.info(f"Loading HybridJournalPipeline from '{model_path}' (CPU-only mode) at startup …")
    global _pipeline
    _pipeline = HybridJournalPipeline(model_path=model_path, use_gpu=False)
    logger.info("Pipeline ready.")
    yield
    logger.info("Shutting down — releasing pipeline resources.")
    _pipeline = None


app = FastAPI(
    title="OJT Journal Entity Extraction API",
    description="HTTP wrapper around HybridJournalPipeline for entity extraction.",
    version="1.0.0",
    lifespan=lifespan,
)

# ---------------------------------------------------------------------------
# Request / response models
# ---------------------------------------------------------------------------


class ExtractionRequest(BaseModel):
    """JSON body for POST /extract."""
    text: str

    @field_validator("text")
    @classmethod
    def text_must_not_be_empty(cls, v: str) -> str:
        if not v or not v.strip():
            raise ValueError("Text must not be empty or whitespace-only.")
        return v


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------


def _deduplicate_entities(entities: list[Dict[str, Any]]) -> list[Dict[str, Any]]:
    """Group raw entity records by normalised (term, category), count frequency.

    For each group the record with the earliest ``start`` offset is kept as the
    representative.  The ``start`` and ``end`` offsets in the output refer to the
    *first* occurrence of the entity in the text so callers can still highlight
    it if desired.
    """
    groups: dict[tuple[str, str], dict] = {}
    freq: dict[tuple[str, str], int] = defaultdict(int)

    for ent in entities:
        key = (_normalise(ent["term"]), ent["category"])
        freq[key] += 1
        if key not in groups:
            groups[key] = dict(ent)  # shallow copy of first occurrence

    deduped = []
    for key, record in groups.items():
        record["frequency"] = freq[key]
        deduped.append(record)

    # Sort by start offset of first occurrence for a stable, readable order
    deduped.sort(key=lambda e: e["start"])
    return deduped


def _build_summary(entities: list[Dict[str, Any]]) -> Dict[str, Any]:
    """Build category breakdown summary matching the old PHP contract."""
    unique = len(entities)
    total_occurrences = sum(e.get("frequency", 1) for e in entities)

    category_counts: dict[str, int] = defaultdict(int)
    for e in entities:
        category_counts[e["category"]] += 1

    it_count = category_counts.get("IT_TERM", 0)
    clerical_count = category_counts.get("CLERICAL_TERM", 0)

    summary: Dict[str, Any] = {
        "total_occurrences": total_occurrences,
        "unique_entities": unique,
        "IT_TERM": it_count,
        "CLERICAL_TERM": clerical_count,
    }

    if unique > 0:
        summary["IT_TERM_percentage"] = round(it_count / unique * 100, 1)
        summary["CLERICAL_TERM_percentage"] = round(clerical_count / unique * 100, 1)
    else:
        summary["IT_TERM_percentage"] = 0.0
        summary["CLERICAL_TERM_percentage"] = 0.0

    return summary


# ---------------------------------------------------------------------------
# PDF helpers
# ---------------------------------------------------------------------------


def _load_pdf_module():
    """Import the legacy PDF/WAR column helpers lazily.

    ``extract_entities`` pulls in pdfplumber and mysql.connector at module
    level. Neither is needed to serve ``/extract``, so the import is deferred
    until a PDF is actually uploaded and the module is cached afterwards.
    """
    global _pdf_module
    if _pdf_module is None:
        import extract_entities  # noqa: F401  (module-level import is intentional)

        _pdf_module = extract_entities
    return _pdf_module


def _read_pdf_text(pdf_path: str) -> Tuple[str, str, Dict[str, Any]]:
    """Read a PDF into ``(full_text, scoped_text, scope)``.

    The scoped text is limited to the ACTIVITIES / REFLECTIONS columns when
    the WAR table is found, which is what entities should be matched against.
    Falls back to the whole document when the table cannot be located.
    """
    import pdfplumber

    module = _load_pdf_module()

    with pdfplumber.open(pdf_path) as pdf:
        full_text, text_error = module.extract_pdf_text(pdf)

        if text_error is not None:
            raise RuntimeError(text_error)

        try:
            scope_result = module.build_war_column_scope(pdf)
            scope = dict(scope_result[0])
            scoped_text = module.extract_war_column_text(scope_result)
        except Exception as exc:  # noqa: BLE001 — never fail the upload on scoping
            logger.warning("WAR column scoping failed, using full document text: %s", exc)
            scope = {"status": "scoping_failed", "message": str(exc)}
            scoped_text = ""

    return full_text, scoped_text, scope


# ---------------------------------------------------------------------------
# Endpoints
# ---------------------------------------------------------------------------


@app.get("/health")
async def health():
    """Readiness probe — confirms the model is loaded and ready to serve."""
    if _pipeline is None:
        return JSONResponse(
            status_code=503,
            content={"status": "unavailable", "detail": "Pipeline not loaded yet."},
        )
    return {"status": "ok", "model_path": _pipeline.model_path}


@app.post("/extract")
async def extract_entities(req: ExtractionRequest):
    """Run hybrid entity extraction on the submitted text.

    Returns deduplicated entities with frequency counts, category breakdown
    summary, and the original input text — shaped to match the PHP frontend's
    expected JSON contract.
    """
    # --- Guard: pipeline must be loaded ---
    if _pipeline is None:
        return JSONResponse(
            status_code=503,
            content={
                "success": False,
                "error": "Pipeline is not loaded. Try again shortly.",
            },
        )

    # --- Guard: text length ---
    if len(req.text) > MAX_TEXT_LENGTH:
        raise HTTPException(
            status_code=400,
            detail={
                "success": False,
                "error": "Text exceeds maximum allowed length.",
                "details": f"Received {len(req.text)} characters; limit is {MAX_TEXT_LENGTH}.",
            },
        )

    # --- Run inference ---
    try:
        raw_result = _pipeline.predict(req.text, mode="hybrid")
    except Exception as exc:
        logger.exception("Pipeline inference failed.")
        return JSONResponse(
            status_code=500,
            content={
                "success": False,
                "error": "Entity extraction failed.",
                "details": str(exc),
            },
        )

    # --- Deduplicate & summarise ---
    deduped = _deduplicate_entities(raw_result["entities"])
    summary = _build_summary(deduped)

    return {
        "success": True,
        "content": req.text,
        "entities": deduped,
        "entity_count": len(deduped),
        "summary": summary,
    }


@app.post("/extract-pdf")
async def extract_pdf(file: UploadFile = File(...)):
    """Run hybrid entity extraction on an uploaded weekly report PDF.

    This is the endpoint the PHP portal calls (``entityApiExtractPdf()``): the
    PDF arrives as ``multipart/form-data`` under the ``file`` field, so the
    response mirrors ``POST /extract`` — ``success``, ``content``, ``entities``,
    ``entity_count`` and ``summary`` — with the full document text as
    ``content``.

    Entities are matched against the ACTIVITIES / REFLECTIONS columns when the
    WAR table can be located; otherwise the whole document is used and
    ``extraction_scope`` reports what happened.
    """
    # --- Guard: pipeline must be loaded ---
    if _pipeline is None:
        return JSONResponse(
            status_code=503,
            content={
                "success": False,
                "error": "Pipeline is not loaded. Try again shortly.",
                "content": "",
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    # --- Guard: content type ---
    if file.content_type not in ("application/pdf", "application/octet-stream"):
        return JSONResponse(
            status_code=400,
            content={
                "success": False,
                "error": "Uploaded file is not a PDF.",
                "details": f"Received content type '{file.content_type}'.",
                "content": "",
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    payload = await file.read()

    if not payload:
        return JSONResponse(
            status_code=400,
            content={
                "success": False,
                "error": "Uploaded PDF was empty.",
                "content": "",
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    if len(payload) > MAX_PDF_BYTES:
        return JSONResponse(
            status_code=400,
            content={
                "success": False,
                "error": "Uploaded PDF is too large.",
                "details": f"Received {len(payload)} bytes; limit is {MAX_PDF_BYTES}.",
                "content": "",
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    # --- Read the PDF ---
    # pdfplumber needs a real file, so the upload is spooled to a temp file and
    # removed whatever happens.
    temp_path = None
    try:
        with tempfile.NamedTemporaryFile(
            suffix=".pdf", delete=False
        ) as handle:
            handle.write(payload)
            temp_path = handle.name

        try:
            full_text, scoped_text, scope = _read_pdf_text(temp_path)
        except Exception as exc:  # noqa: BLE001 — surfaced as a 422 to the caller
            logger.exception("Failed to read the uploaded PDF.")
            return JSONResponse(
                status_code=422,
                content={
                    "success": False,
                    "error": "Failed to read PDF.",
                    "details": str(exc),
                    "content": "",
                    "entities": [],
                    "entity_count": 0,
                    "summary": {},
                },
            )
    finally:
        if temp_path and os.path.exists(temp_path):
            try:
                os.unlink(temp_path)
            except OSError:
                logger.warning("Could not remove temp PDF %s", temp_path)

    if not full_text:
        return JSONResponse(
            status_code=422,
            content={
                "success": False,
                "error": "No extractable text was found in the PDF.",
                "details": "The file may be a scanned image rather than a text PDF.",
                "content": "",
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    # The WAR columns are the trainee's own writing, so they win; the full
    # document is only used when the table could not be located.
    text = scoped_text or full_text

    if len(text) > MAX_TEXT_LENGTH:
        return JSONResponse(
            status_code=400,
            content={
                "success": False,
                "error": "Extracted text exceeds maximum allowed length.",
                "details": f"Received {len(text)} characters; limit is {MAX_TEXT_LENGTH}.",
                "content": full_text,
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    # --- Run inference ---
    try:
        raw_result = _pipeline.predict(text, mode="hybrid")
    except Exception as exc:  # noqa: BLE001
        logger.exception("Pipeline inference failed.")
        return JSONResponse(
            status_code=500,
            content={
                "success": False,
                "error": "Entity extraction failed.",
                "details": str(exc),
                "content": full_text,
                "entities": [],
                "entity_count": 0,
                "summary": {},
            },
        )

    # --- Deduplicate & summarise ---
    deduped = _deduplicate_entities(raw_result["entities"])
    summary = _build_summary(deduped)

    return {
        "success": True,
        "content": full_text,
        "scoped_content": scoped_text,
        "extraction_scope": scope,
        "entities": deduped,
        "entity_count": len(deduped),
        "summary": summary,
    }


# ---------------------------------------------------------------------------
# Pydantic validation error handler — returns 400 matching the old fail() shape
# ---------------------------------------------------------------------------
from fastapi.exceptions import RequestValidationError


@app.exception_handler(RequestValidationError)
async def validation_exception_handler(request, exc):
    return JSONResponse(
        status_code=400,
        content={
            "success": False,
            "error": "Invalid request.",
            "details": str(exc),
        },
    )
