"""
NLP Entity Extraction module wrapping HybridJournalPipeline.

Provides `extract_from_text(text: str)` for direct in-process Python calls.
"""

from __future__ import absolute_import
import os
import sys
import re
import logging
from collections import defaultdict
from typing import Dict, Any, Optional

# ---------------------------------------------------------------------------
# Path bootstrap — ensure project root (python/) is importable regardless of cwd
# ---------------------------------------------------------------------------
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
_script_dir = os.path.dirname(os.path.abspath(__file__))
while _script_dir in sys.path:
    sys.path.remove(_script_dir)
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

# ---------------------------------------------------------------------------
# Environment configuration — load .env from project root if available
# ---------------------------------------------------------------------------
try:
    from dotenv import load_dotenv
    _root_env = os.path.abspath(os.path.join(PROJECT_ROOT, "..", ".env"))
    _local_env = os.path.join(PROJECT_ROOT, ".env")
    if os.path.exists(_root_env):
        load_dotenv(_root_env)
    elif os.path.exists(_local_env):
        load_dotenv(_local_env)
except ImportError:
    pass

from scripts.pipeline import HybridJournalPipeline

logger = logging.getLogger("ojt_pipeline.spacy")

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------
MAX_TEXT_LENGTH = 100_000

# Whitespace normalisation regex
_WS_RE = re.compile(r"\s+")


def _normalise(text: str) -> str:
    """Lowercase and collapse internal whitespace for dedup key generation."""
    return _WS_RE.sub(" ", text.strip().lower())


# ---------------------------------------------------------------------------
# Global pipeline reference (populated at startup or on first call)
# ---------------------------------------------------------------------------
_pipeline: Optional[HybridJournalPipeline] = None


def get_pipeline() -> HybridJournalPipeline:
    """Retrieve or lazily initialize the shared HybridJournalPipeline."""
    global _pipeline
    if _pipeline is None:
        model_path = os.environ.get("MODEL_PATH")
        if not model_path:
            candidate = os.path.join(PROJECT_ROOT, "models", "ner_trf", "model-best")
            model_path = candidate if os.path.exists(candidate) else "models/ner_trf/model-best"

        terms_path = os.environ.get("TERMS_CSV_PATH")
        if not terms_path:
            candidate_terms = os.path.join(PROJECT_ROOT, "data", "terms.csv")
            terms_path = candidate_terms if os.path.exists(candidate_terms) else "data/terms.csv"

        logger.info(f"Loading HybridJournalPipeline from '{model_path}' (CPU-only mode) …")
        _pipeline = HybridJournalPipeline(
            model_path=model_path,
            terms_csv_path=terms_path,
            use_gpu=False
        )
        logger.info("Pipeline ready.")
    return _pipeline

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------


def _deduplicate_entities(entities: list[Dict[str, Any]]) -> list[Dict[str, Any]]:
    """Group raw entity records by normalised (term, category), count frequency."""
    groups: dict[tuple[str, str], dict] = {}
    freq: dict[tuple[str, str], int] = defaultdict(int)

    for ent in entities:
        key = (_normalise(ent["term"]), ent["category"])
        freq[key] += 1
        if key not in groups:
            groups[key] = dict(ent)

    deduped = []
    for key, record in groups.items():
        record["frequency"] = freq[key]
        deduped.append(record)

    # Sort by start offset of first occurrence for a stable, readable order
    deduped.sort(key=lambda e: e["start"])
    return deduped


def _build_summary(entities: list[Dict[str, Any]]) -> Dict[str, Any]:
    """Build category breakdown summary matching the PHP contract."""
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
# Direct callable
# ---------------------------------------------------------------------------

def extract_from_text(text: str) -> Dict[str, Any]:
    """Run hybrid entity extraction directly on input text (callable in-process)."""
    if not text or not str(text).strip():
        return {
            "success": False,
            "error": "Text must not be empty or whitespace-only.",
        }

    # --- Guard: text length ---
    if len(text) > MAX_TEXT_LENGTH:
        return {
            "success": False,
            "error": "Text exceeds maximum allowed length.",
            "details": f"Received {len(text)} characters; limit is {MAX_TEXT_LENGTH}.",
        }

    # --- Run inference ---
    try:
        pipeline = get_pipeline()
        raw_result = pipeline.predict(text, mode="hybrid")
    except Exception as exc:
        logger.exception("Pipeline inference failed.")
        return {
            "success": False,
            "error": "Entity extraction failed.",
            "details": str(exc),
        }

    # --- Deduplicate & summarise ---
    deduped = _deduplicate_entities(raw_result["entities"])
    summary = _build_summary(deduped)

    return {
        "success": True,
        "content": text,
        "entities": deduped,
        "entity_count": len(deduped),
        "summary": summary,
    }