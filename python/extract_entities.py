#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ICS-PORTAL Entity Extractor (Cross-Platform: Windows & Linux)

Extracts WAR (Weekly Accomplishment Report) activities and reflections
from submitted PDFs using modular pdf_to_text and runs entity extraction
directly via scripts.spacy.
"""

from __future__ import absolute_import
import sys
import os
import json
import logging

# ---------------------------------------------------------------------------
# Cross-Platform UTF-8 Streams Configuration
# ---------------------------------------------------------------------------
# On Windows, standard pipes default to OEM codepages (e.g., cp1252/cp437)
# which crash with UnicodeEncodeError when printing bullet points or unicode.
for _stream in (sys.stdout, sys.stderr):
    if hasattr(_stream, "reconfigure"):
        try:
            _stream.reconfigure(encoding="utf-8", errors="replace")
        except Exception:
            pass

# Suppress pipeline info logging so stdout contains strictly valid JSON
logging.getLogger("ojt_pipeline").setLevel(logging.WARNING)
logging.getLogger("ojt_pipeline.spacy").setLevel(logging.WARNING)

# ---------------------------------------------------------------------------
# Path Bootstrap
# ---------------------------------------------------------------------------
PROJECT_ROOT = os.path.dirname(os.path.abspath(__file__))
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


def fail(message: str, details=None) -> None:
    """Print structured error JSON to stdout and exit with code 1."""
    result = {
        "success": False,
        "error": message
    }
    if details is not None:
        result["details"] = str(details)
    print(json.dumps(result, ensure_ascii=False))
    sys.exit(1)


# ---------------------------------------------------------------------------
# Modular Imports
# ---------------------------------------------------------------------------
missing_dependencies = []

try:
    from scripts.pdf_to_text import extract_text_from_pdf
except ImportError as e:
    extract_text_from_pdf = None
    missing_dependencies.append(f"pdf_to_text ({e})")

try:
    from scripts.spacy import extract_from_text
except ImportError as e:
    extract_from_text = None
    missing_dependencies.append(f"scripts.spacy ({e})")


def format_entities_for_php(raw_entities: list) -> list:
    """Format entity dictionaries to match the schema expected by PHP reviewers."""
    formatted = []
    for ent in raw_entities:
        term = str(ent.get("term") or ent.get("entity_name") or ent.get("entity") or "").strip()
        if not term:
            continue
        cat = str(ent.get("category", "")).strip()

        # Classify activity_type and it_related from category
        if cat in ("IT_TERM", "Software"):
            activity_type = "Software"
            it_related = "yes"
        elif cat == "Hardware":
            activity_type = "Hardware"
            it_related = "yes"
        elif cat in ("CLERICAL_TERM", "Clerical"):
            activity_type = "Clerical"
            it_related = "no"
        else:
            activity_type = "Other"
            it_related = "unknown"

        conf = ent.get("confidence", 1.0)
        try:
            conf_score = round(float(conf) * 100, 2)
        except (ValueError, TypeError):
            conf_score = 100.00

        formatted.append({
            "term": term,
            "entity": term,
            "entity_name": term,
            "canonical_name": term,
            "matched_term": term,
            "category": cat or "Other",
            "activity_type": activity_type,
            "it_related": it_related,
            "confidence_score": conf_score,
            "source": ent.get("source", "spacy")
        })
    return formatted


def main():
    # Verify critical dependencies before executing
    if missing_dependencies:
        fail(
            f"Missing required Python dependencies: {', '.join(missing_dependencies)}.",
            f"Please run: pip install {' '.join(missing_dependencies)}"
        )

    # 1. Parse & normalize PDF path (handles Windows '\' and Linux '/')
    if len(sys.argv) < 2:
        fail("No PDF path was provided.")

    raw_path = sys.argv[1].strip('\'"')
    if os.name != "nt" and "\\" in raw_path:
        raw_path = raw_path.replace("\\", "/")

    pdf_path = os.path.abspath(os.path.normpath(raw_path))

    if not os.path.isfile(pdf_path):
        fail("PDF file not found.", pdf_path)

    # 2. Extract text and scoped WAR columns using modular pdf_to_text
    full_text, scope_text, scope, pdf_error = extract_text_from_pdf(pdf_path)
    if pdf_error:
        fail("Failed to read PDF.", pdf_error)

    if not full_text:
        fail("No extractable text was found in the PDF.")

    # Prioritize scoped WAR column text, falling back to full PDF text
    payload_text = scope_text.strip() if (scope_text and scope_text.strip()) else full_text.strip()
    if not payload_text:
        fail("No text could be extracted from the PDF.")

    # 3. Direct function call to NLP extraction pipeline
    try:
        api_result = extract_from_text(payload_text)
    except Exception as e:
        fail("Direct entity extraction failed.", str(e))

    if not isinstance(api_result, dict) or not api_result.get("success", False):
        err_msg = api_result.get("error", "Entity extraction returned an error.") if isinstance(api_result, dict) else "Invalid result from extractor."
        err_det = api_result.get("details", "") if isinstance(api_result, dict) else str(api_result)
        fail(err_msg, err_det)

    # 4. Format entities and summary for PHP reviewers
    formatted_entities = format_entities_for_php(api_result.get("entities", []))

    result = {
        "success": True,
        "content": full_text,
        "scoped_content": scope_text,
        "extraction_scope": scope,
        "entities": formatted_entities,
        "entity_count": len(formatted_entities),
        "summary": api_result.get("summary", {})
    }

    print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    main()