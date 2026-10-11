"""
FastAPI service for ICS OJT report entity extraction.

Runs both:
1. The trained spaCy/hybrid model (scripts.pipeline.HybridJournalPipeline)
2. The MySQL predefined-entity matching pipeline from extract_entities.py

Database matches take precedence when the model finds the same term. The response
keeps the fields used by the PHP portal and includes source/metadata for each entity.
"""

import logging
import os
import re
import sys
import tempfile
from contextlib import asynccontextmanager
from collections import defaultdict
from typing import Any, Dict, Optional, Tuple

import mysql.connector
from fastapi import FastAPI, File, HTTPException, UploadFile
from fastapi.responses import JSONResponse
from pydantic import BaseModel, field_validator

PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

from scripts.pipeline import HybridJournalPipeline  # noqa: E402

logger = logging.getLogger("ojt_pipeline.api")

MAX_TEXT_LENGTH = 100_000
MAX_PDF_BYTES = 25 * 1024 * 1024
_WS_RE = re.compile(r"\s+")

_pipeline: Optional[HybridJournalPipeline] = None
_pdf_module = None


def _normalise(value: Any) -> str:
    return _WS_RE.sub(" ", str(value or "").strip().lower())


def _normalise_activity_type(value: Any, fallback: str = "Other") -> str:
    """Map model/database labels to the activity values used by the portal."""
    normalized = _normalise(value).replace("-", "_")
    if normalized in {"software", "software_tool", "software_tools", "software_term", "it_software", "technical_software", "it_term", "it", "technical", "technology", "tech"}:
        return "Software"
    if normalized in {"hardware", "hardware_tool", "hardware_tools", "hardware_term", "it_hardware", "technical_hardware"}:
        return "Hardware"
    if normalized in {"clerical", "clerical_task", "clerical_tasks", "clerical_term", "administrative", "admin", "non_it", "non_it_task"}:
        return "Clerical"
    if normalized in {"other", "unknown", "unclassified", ""}:
        return fallback
    return fallback


def _infer_activity_type(item: Dict[str, Any]) -> str:
    """Infer a portal activity type from fields emitted by either pipeline."""
    for field in ("activity_type", "category", "label", "entity_label", "type"):
        value = _normalise_activity_type(item.get(field), fallback="")
        if value:
            return value
    return "Other"


def _normalise_it_related(value: Any, activity_type: str) -> str:
    """Return the portal's yes/no/unknown flag, deriving it from activity when safe."""
    normalized = _normalise(value)
    if normalized in {"yes", "y", "true", "1", "it", "it_related", "technical"}:
        return "yes"
    if normalized in {"no", "n", "false", "0", "non_it", "non-it", "clerical"}:
        return "no"
    if activity_type in {"Software", "Hardware"}:
        return "yes"
    if activity_type == "Clerical":
        return "no"
    return "unknown"


@asynccontextmanager
async def lifespan(app: FastAPI):
    global _pipeline
    model_path = os.environ.get("MODEL_PATH", "models/ner_trf/model-best")
    logger.info("Loading HybridJournalPipeline from %r (CPU mode)", model_path)
    _pipeline = HybridJournalPipeline(model_path=model_path, use_gpu=False)
    logger.info("Trained extraction pipeline ready.")
    yield
    _pipeline = None
    logger.info("Extraction API stopped.")


app = FastAPI(
    title="OJT Journal Entity Extraction API",
    description="Trained model plus live MySQL predefined-entity matching.",
    version="2.0.0",
    lifespan=lifespan,
)


class ExtractionRequest(BaseModel):
    text: str

    @field_validator("text")
    @classmethod
    def text_must_not_be_empty(cls, value: str) -> str:
        if not value or not value.strip():
            raise ValueError("Text must not be empty or whitespace-only.")
        return value


def _load_pdf_module():
    """Load the existing PDF/WAR-column helpers only when needed."""
    global _pdf_module
    if _pdf_module is None:
        import extract_entities
        _pdf_module = extract_entities
    return _pdf_module


def _read_pdf_text(pdf_path: str) -> Tuple[str, str, Dict[str, Any]]:
    """Return full PDF text, WAR-scoped text, and scoping details."""
    import pdfplumber

    module = _load_pdf_module()
    with pdfplumber.open(pdf_path) as pdf:
        full_text, text_error = module.extract_pdf_text(pdf)
        if text_error:
            raise RuntimeError(text_error)

        try:
            scope_result = module.build_war_column_scope(pdf)
            scope = dict(scope_result[0])
            scoped_text = module.extract_war_column_text(scope_result)
        except Exception as exc:
            logger.warning("WAR scoping failed; using full PDF text: %s", exc)
            scope = {"status": "scoping_failed", "message": str(exc)}
            scoped_text = ""

    return full_text or "", scoped_text or "", scope


def _connect_database():
    """Connect to MySQL using environment variables or the local XAMPP defaults."""
    return mysql.connector.connect(
        host=os.environ.get("DB_HOST", "127.0.0.1"),
        port=int(os.environ.get("DB_PORT", "3306")),
        database=os.environ.get("DB_NAME", "nbsc_ojt"),
        user=os.environ.get("DB_USER", "root"),
        password=os.environ.get("DB_PASSWORD", ""),
        connection_timeout=8,
    )


def _database_matches(text: str) -> list[Dict[str, Any]]:
    """
    Invoke the existing database matching functions in extract_entities.py.
    The predefined_entities table is queried on each request so coordinator
    changes are reflected without restarting FastAPI.
    """
    module = _load_pdf_module()
    connection = None
    cursor = None
    try:
        connection = _connect_database()
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            """
            SELECT id, entity_name, aliases, category, activity_type,
                   it_related, description
            FROM predefined_entities
            WHERE LOWER(TRIM(entity_name)) <> 'my'
              AND is_archived = 0
            ORDER BY CHAR_LENGTH(entity_name) DESC, entity_name ASC
            """
        )
        rows = cursor.fetchall()

        # Use the same matching/alias/overlap rules as the existing Python script.
        matches = module.find_predefined_matches(text, rows)
        matches = module.resolve_entity_overlaps(text, matches)

        normalized = []
        for match in matches:
            term = str(
                match.get("matched_term")
                or match.get("entity_name")
                or match.get("entity")
                or ""
            ).strip()
            if not term:
                continue

            # Locate the first literal occurrence for the PHP highlight/offset fields.
            found = re.search(re.escape(term), text, flags=re.IGNORECASE)
            start = found.start() if found else 0
            end = found.end() if found else len(term)

            activity_type = _normalise_activity_type(
                match.get("activity_type"),
                fallback=_infer_activity_type(match),
            )
            normalized.append({
                "term": term,
                "entity": match.get("entity") or match.get("entity_name") or term,
                "entity_name": match.get("entity_name") or match.get("entity") or term,
                "canonical_name": match.get("canonical_name") or match.get("entity_name") or term,
                "category": str(match.get("category") or "Other"),
                "activity_type": activity_type,
                "it_related": _normalise_it_related(match.get("it_related"), activity_type),
                "predefined_id": match.get("predefined_id"),
                "description": match.get("description"),
                "matched_term": term,
                "start": int(start),
                "end": int(end),
                "frequency": int(match.get("frequency") or 1),
                "confidence": 1.0,
                "confidence_score": 1.0,
                "source": "predefined",
            })
        return normalized
    except Exception:
        logger.exception("MySQL predefined-entity matching failed.")
        raise
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


def _normalise_model_entities(raw_entities: list[Dict[str, Any]]) -> list[Dict[str, Any]]:
    """Standardize model output without discarding model confidence/category."""
    normalized = []
    for item in raw_entities or []:
        term = str(item.get("term") or item.get("text") or item.get("entity") or "").strip()
        if not term:
            continue
        activity_type = _infer_activity_type(item)
        normalized.append({
            **item,
            "term": term,
            "category": str(item.get("category") or item.get("label") or activity_type),
            "activity_type": activity_type,
            "it_related": _normalise_it_related(item.get("it_related"), activity_type),
            "start": int(item.get("start") or 0),
            "end": int(item.get("end") or (int(item.get("start") or 0) + len(term))),
            "frequency": int(item.get("frequency") or 1),
            "source": str(item.get("source") or "spacy"),
        })
    return normalized


def _merge_entities(database_entities: list[Dict[str, Any]],
                    model_entities: list[Dict[str, Any]]) -> list[Dict[str, Any]]:
    """
    Merge both pipelines. A database-defined term wins over an overlapping
    model term, because its category/activity_type/it_related were configured
    by the coordinator. Model-only terms remain in the results.
    """
    merged: dict[str, Dict[str, Any]] = {}

    for entity in database_entities:
        key = _normalise(entity.get("term"))
        if key:
            merged[key] = dict(entity)

    for entity in model_entities:
        key = _normalise(entity.get("term"))
        if not key:
            continue
        if key not in merged:
            entity = dict(entity)
            activity_type = _normalise_activity_type(
                entity.get("activity_type"),
                fallback=_infer_activity_type(entity),
            )
            entity["activity_type"] = activity_type
            entity["it_related"] = _normalise_it_related(entity.get("it_related"), activity_type)
            merged[key] = entity
        else:
            # Keep the DB classification, but retain useful model confidence if available.
            if merged[key].get("confidence") is None and entity.get("confidence") is not None:
                merged[key]["confidence"] = entity["confidence"]

    result = list(merged.values())
    result.sort(key=lambda item: (int(item.get("start") or 0), str(item.get("term") or "").lower()))
    return result


def _deduplicate_entities(entities: list[Dict[str, Any]]) -> list[Dict[str, Any]]:
    """Group repeated terms and sum their frequencies while preserving first offsets."""
    groups: dict[tuple[str, str], dict] = {}
    counts: dict[tuple[str, str], int] = defaultdict(int)

    for entity in entities:
        term = str(entity.get("term") or "").strip()
        category = str(entity.get("category") or "Other")
        if not term:
            continue
        key = (_normalise(term), category.lower())
        counts[key] += max(1, int(entity.get("frequency") or 1))
        if key not in groups:
            groups[key] = dict(entity)

    output = []
    for key, record in groups.items():
        record["frequency"] = counts[key]
        output.append(record)
    output.sort(key=lambda item: int(item.get("start") or 0))
    return output


def _build_summary(entities: list[Dict[str, Any]]) -> Dict[str, Any]:
    total_occurrences = sum(int(entity.get("frequency") or 1) for entity in entities)
    activity_counts: dict[str, int] = defaultdict(int)
    it_related_count = 0
    clerical_count = 0

    for entity in entities:
        activity = str(entity.get("activity_type") or "Other").strip().title()
        if activity not in {"Software", "Hardware", "Clerical", "Other"}:
            activity = "Other"
        activity_counts[activity] += 1
        related = str(entity.get("it_related") or "").lower()
        if related == "yes":
            it_related_count += 1
        if activity == "Clerical":
            clerical_count += 1

    unique = len(entities)
    return {
        "total_occurrences": total_occurrences,
        "unique_entities": unique,
        "IT_TERM": it_related_count,
        "CLERICAL_TERM": clerical_count,
        "IT_TERM_percentage": round(it_related_count / unique * 100, 1) if unique else 0.0,
        "CLERICAL_TERM_percentage": round(clerical_count / unique * 100, 1) if unique else 0.0,
        "activity_type_counts": dict(activity_counts),
    }


def _run_both_pipelines(text: str) -> tuple[list[Dict[str, Any]], list[Dict[str, Any]], list[Dict[str, Any]]]:
    """Run model and live database matching, then merge their results."""
    if _pipeline is None:
        raise RuntimeError("Trained extraction pipeline is not loaded.")

    model_result = _pipeline.predict(text, mode="hybrid")
    model_entities = _normalise_model_entities(model_result.get("entities", []))
    database_entities = _database_matches(text)
    merged = _merge_entities(database_entities, model_entities)
    return _deduplicate_entities(merged), database_entities, model_entities


@app.get("/health")
async def health():
    if _pipeline is None:
        return JSONResponse(
            status_code=503,
            content={"status": "unavailable", "detail": "Pipeline not loaded yet."},
        )
    return {
        "status": "ok",
        "model_path": _pipeline.model_path,
        "database_matching": "enabled",
    }


@app.post("/extract")
async def extract_text(req: ExtractionRequest):
    if _pipeline is None:
        return JSONResponse(status_code=503, content={
            "success": False, "error": "Pipeline is not loaded. Try again shortly."
        })
    if len(req.text) > MAX_TEXT_LENGTH:
        raise HTTPException(status_code=400, detail={
            "success": False,
            "error": "Text exceeds maximum allowed length.",
            "details": f"Received {len(req.text)} characters; limit is {MAX_TEXT_LENGTH}.",
        })

    try:
        entities, database_entities, model_entities = _run_both_pipelines(req.text)
        return {
            "success": True,
            "content": req.text,
            "entities": entities,
            "entity_count": len(entities),
            "summary": _build_summary(entities),
            "predefined_matches": database_entities,
            "spacy_entities": model_entities,
        }
    except Exception as exc:
        logger.exception("Text extraction failed.")
        return JSONResponse(status_code=500, content={
            "success": False, "error": "Entity extraction failed.", "details": str(exc)
        })


@app.post("/extract-pdf")
async def extract_pdf(file: UploadFile = File(...)):
    if _pipeline is None:
        return JSONResponse(status_code=503, content={
            "success": False, "error": "Pipeline is not loaded. Try again shortly.",
            "content": "", "entities": [], "entity_count": 0, "summary": {},
        })

    if file.content_type not in ("application/pdf", "application/octet-stream"):
        return JSONResponse(status_code=400, content={
            "success": False, "error": "Uploaded file is not a PDF.",
            "details": f"Received content type '{file.content_type}'.",
            "content": "", "entities": [], "entity_count": 0, "summary": {},
        })

    payload = await file.read()
    if not payload:
        return JSONResponse(status_code=400, content={
            "success": False, "error": "Uploaded PDF was empty.",
            "content": "", "entities": [], "entity_count": 0, "summary": {},
        })
    if len(payload) > MAX_PDF_BYTES:
        return JSONResponse(status_code=400, content={
            "success": False, "error": "Uploaded PDF is too large.",
            "details": f"Received {len(payload)} bytes; limit is {MAX_PDF_BYTES}.",
            "content": "", "entities": [], "entity_count": 0, "summary": {},
        })

    temp_path = None
    try:
        with tempfile.NamedTemporaryFile(suffix=".pdf", delete=False) as handle:
            handle.write(payload)
            temp_path = handle.name

        full_text, scoped_text, scope = _read_pdf_text(temp_path)
        if not full_text.strip():
            return JSONResponse(status_code=422, content={
                "success": False,
                "error": "No extractable text was found in the PDF.",
                "details": "The file may be a scanned image rather than a text PDF.",
                "content": "", "entities": [], "entity_count": 0, "summary": {},
            })
    except Exception as exc:
        logger.exception("Failed to read uploaded PDF.")
        return JSONResponse(status_code=422, content={
            "success": False, "error": "Failed to read PDF.", "details": str(exc),
            "content": "", "entities": [], "entity_count": 0, "summary": {},
        })
    finally:
        if temp_path and os.path.exists(temp_path):
            try:
                os.unlink(temp_path)
            except OSError:
                logger.warning("Could not remove temporary PDF %s", temp_path)

    text = scoped_text or full_text
    if len(text) > MAX_TEXT_LENGTH:
        return JSONResponse(status_code=400, content={
            "success": False,
            "error": "Extracted text exceeds maximum allowed length.",
            "details": f"Received {len(text)} characters; limit is {MAX_TEXT_LENGTH}.",
            "content": full_text, "entities": [], "entity_count": 0, "summary": {},
        })

    try:
        entities, database_entities, model_entities = _run_both_pipelines(text)
        return {
            "success": True,
            "content": full_text,
            "scoped_content": scoped_text,
            "extraction_scope": scope,
            "entities": entities,
            "entity_count": len(entities),
            "summary": _build_summary(entities),
            "predefined_matches": database_entities,
            "spacy_entities": model_entities,
        }
    except Exception as exc:
        logger.exception("PDF entity extraction failed.")
        return JSONResponse(status_code=500, content={
            "success": False, "error": "Entity extraction failed.", "details": str(exc),
            "content": full_text, "entities": [], "entity_count": 0, "summary": {},
        })


@app.exception_handler(Exception)
async def unhandled_exception_handler(request, exc):
    logger.exception("Unhandled API error.")
    return JSONResponse(status_code=500, content={
        "success": False, "error": "Internal server error.", "details": str(exc)
    })
