from typing import List, Dict, Any, Optional, Tuple
import os
import sys

# Ensure project root is in sys.path
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

import re
import unicodedata

try:
    from scripts.pipeline import (
        HybridJournalPipeline,
        resolve_span_conflicts,
    )
    from scripts.annotation import load_terms_dictionary
except Exception:
    from pipeline import (
        HybridJournalPipeline,
        resolve_span_conflicts,
    )
    from annotation import load_terms_dictionary


# ---------------------------------------------------------------------------
# Normalization helpers (shared)
# ---------------------------------------------------------------------------

IGNORED_ENTITY_TERMS = {"my"}

_WS_RE = re.compile(r"\s+")


def normalize(text: Any) -> str:
    """Normalize text for matching."""
    text = unicodedata.normalize("NFKC", str(text or "")).lower()
    text = re.sub(r"[-–—‑]", " ", text)
    text = re.sub(r"[^\w\s+#.]", " ", text, flags=re.UNICODE)
    return re.sub(r"\s+", " ", text).strip()
