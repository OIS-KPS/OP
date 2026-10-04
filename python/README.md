# OJT Journal Entity Extraction — Python Pipeline

A modular, cross-platform entity extraction pipeline that extracts IT and clerical skills from On-the-Job Training (OJT) Weekly Accomplishment Reports (WAR) and journal submissions. It combines a fine-tuned spaCy Transformer NER model with dictionary-based matching and table column extraction.

The pipeline executes directly in-process via CLI or Python imports without requiring an HTTP server or external microservice.

---

## Directory Structure

```text
python/
├── scripts/
│   ├── __init__.py          # Package init — GPU bootstrap & init_gpu() helper
│   ├── spacy.py             # In-process entity extraction (wraps HybridJournalPipeline)
│   ├── pdf_to_text.py       # Modular PDF text and WAR table column extraction
│   ├── pipeline.py          # HybridJournalPipeline class (Transformer + dictionary + conflict resolution)
│   ├── annotation.py        # Terms dictionary loader, weak-labeling, JSONL/DocBin I/O
│   └── labels.py            # Label taxonomy constants & normalization functions
├── data/
│   └── terms.csv            # Curated entity dictionary (term,label CSV — ~343 rows)
├── models/
│   └── ner_trf/
│       └── model-best/      # Fine-tuned spaCy transformer model checkpoint (weights, config, vocab)
├── extract_entities.py      # Main CLI entry point invoked by PHP (Windows & Linux compatible)
├── requirements.txt         # Pinned pip dependencies
└── .venv/                   # Python virtual environment (gitignored)
```

---

## Module & File Reference

### 1. `extract_entities.py`

The primary CLI script executed by PHP reviewers (`review_report.php`, `coordinator/view_report.php`, `supervisor/review_reports.php`).

- **Cross-platform**: Configures standard streams (`stdout`, `stderr`) to UTF-8 on Windows and Linux to prevent encoding errors on quotes, bullet points, or accented characters.
- **Path normalization**: Automatically handles both forward slashes (`/`) and backslashes (`\`).
- **Pipeline Orchestration**:
  1. Calls `scripts.pdf_to_text.extract_text_from_pdf()` to extract full document text and isolate scoped WAR columns (`ACTIVITIES` & `REFLECTIONS`).
  2. Falls back to full PDF text if the document does not follow the WAR table structure.
  3. Passes text directly to `scripts.spacy.extract_from_text()` for in-process inference.
  4. Formats entity records (`entity_name`, `canonical_name`, `category`, `activity_type`, `it_related`, `confidence_score`, `source`).
  5. Outputs clean JSON to `sys.stdout`.

### 2. `scripts/spacy.py`

In-process entity extraction interface wrapping `HybridJournalPipeline`.

| Function | Purpose |
| --- | --- |
| `get_pipeline()` | Lazily loads and caches the `HybridJournalPipeline` singleton using CPU-only inference. |
| `extract_from_text(text: str)` | Runs hybrid inference on input text, deduplicates entity spans by `(term, category)`, computes occurrence frequencies, and calculates summary metrics. |
| `_deduplicate_entities(entities)` | Groups duplicate mentions of the same term and attaches a `frequency` count. |
| `_build_summary(entities)` | Generates totals and category breakdowns (`IT_TERM`, `CLERICAL_TERM`, and percentages). |

### 3. `scripts/pdf_to_text.py`

Self-contained PDF text extraction module tailored for Weekly Accomplishment Report (WAR) documents.

| Function | Purpose |
| --- | --- |
| `extract_text_from_pdf(pdf_path)` | High-level entry point returning `(full_text, scope_text, scope_info, error)`. |
| `build_war_column_scope(pdf)` | Analyzes table border rules and column alignments to identify `ACTIVITIES` and `REFLECTIONS` sections across single or multi-page reports. |
| `extract_war_column_text(scope_tuple)` | Concatenates text extracted from inside the scoped WAR table columns. |
| `extract_pdf_text(pdf_source)` | Fallback full-document text extractor across all pages. |

### 4. `scripts/pipeline.py`

Core hybrid inference engine (`HybridJournalPipeline`).

- **Transformer NER**: Uses fine-tuned transformer weights (`models/ner_trf/model-best`) for contextual token classification.
- **Dictionary Matcher**: Uses an exact-match `EntityRuler` seeded from `data/terms.csv`.
- **Conflict Resolution Rules**:
  1. *Longest span wins* — shorter matches never truncate longer entity spans.
  2. *ML label authority* — on classification disagreement, the transformer prediction takes precedence.
  3. *Abstention fallback* — dictionary entities are accepted only where ML did not predict an entity.

### 5. `data/terms.csv`

Curated terminology dictionary with columns `term,label` (~343 rows).

```csv
term,label
Java,IT_TERM
MySQL,IT_TERM
Microsoft Excel,CLERICAL_TERM
data entry,CLERICAL_TERM
```

To add or update recognized terms, edit this CSV file.

### 6. `models/ner_trf/model-best/`

Trained spaCy transformer model directory containing `config.cfg`, `meta.json`, vocab files, and PyTorch transformer weights.

> **Note**: This directory contains binary weights and is excluded from git.
> Download the model directory from Google Drive:
> 🔗 [Google Drive: Model Folder & Video Explanation](https://drive.google.com/drive/folders/1NFow1Lgch4Wz6lV0aIONfouaGSthgLgk?usp=sharing)
> Place the contents into `python/models/ner_trf/model-best/`.

---

## Setup & Installation

### Prerequisites

- Python 3.10+ (tested on Python 3.10 – 3.14)
- Model checkpoint placed in `models/ner_trf/model-best/`

### 1. Create Virtual Environment

```bash
cd /opt/lampp/htdocs/ICS-PORTAL/python

python3 -m venv .venv

# On Linux / macOS:
source .venv/bin/activate

# On Windows:
.venv\Scripts\activate
```

### 2. Install Dependencies

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

---

## Running & Verification

### Run CLI Extractor on a PDF Report

```bash
# From the python/ directory:
python extract_entities.py ../uploads/reports/sample_text.pdf

# Or using the virtual environment interpreter directly:
.venv/bin/python3 extract_entities.py ../uploads/reports/sample_text.pdf
```

Example JSON output:

```json
{
  "success": true,
  "content": "--- PAGE 1 ---\nAssisted with database migration and records encoding...",
  "scoped_content": "ACTIVITIES:\nMigrated MySQL database to new server.",
  "extraction_scope": {
    "status": "columns",
    "header_page": 1,
    "pages_with_text": [1],
    "message": ""
  },
  "entities": [
    {
      "term": "MySQL",
      "entity": "MySQL",
      "entity_name": "MySQL",
      "canonical_name": "MySQL",
      "matched_term": "MySQL",
      "category": "IT_TERM",
      "activity_type": "Software",
      "it_related": "yes",
      "confidence_score": 98.13,
      "source": "ML"
    }
  ],
  "entity_count": 1,
  "summary": {
    "total_occurrences": 1,
    "unique_entities": 1,
    "IT_TERM": 1,
    "CLERICAL_TERM": 0,
    "IT_TERM_percentage": 100.0,
    "CLERICAL_TERM_percentage": 0.0
  }
}
```

### Direct Python Invocation (In-Process)

You can call the extraction functions directly inside any Python script without CLI overhead:

```python
from scripts.spacy import extract_from_text

result = extract_from_text("Installed Apache HTTP Server and configured MySQL databases.")
if result["success"]:
    print(f"Found {result['entity_count']} entities:")
    for ent in result["entities"]:
        print(f" - {ent['term']} ({ent['category']}) [confidence: {ent['confidence']}]")
```

### Test PDF Text & Scope Extraction Only

```bash
python scripts/pdf_to_text.py ../uploads/reports/sample_text.pdf
```

---

## PHP Integration

The PHP portal invokes `python/extract_entities.py` via `proc_open`. The standard pattern used across `review_report.php`, `coordinator/view_report.php`, and `supervisor/review_reports.php` is:

```php
$script = __DIR__ . '/python/extract_entities.py';
$pdfPath = '/absolute/path/to/report.pdf';
$pythonBinary = getenv('PYTHON_BINARY') ?: (__DIR__ . '/python/.venv/bin/python3');

$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$cmd = [$pythonBinary, $script, $pdfPath];
$process = proc_open($cmd, $descriptors, $pipes);

if (is_resource($process)) {
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode === 0) {
        $result = json_decode($stdout, true);
        // $result['entities'], $result['summary'], $result['scoped_content']
    } else {
        // Handle error: $stderr or parsed JSON error from $stdout
    }
}
```

---

## Environment Variables (Optional)

The application works out-of-the-box without custom configuration. If needed, the following optional variables can be defined in your `.env` or system environment:

| Variable | Default | Purpose |
| --- | --- | --- |
| `PYTHON_BINARY` | Auto-detected (`python/.venv/bin/python3` or `Scripts/python.exe`) | Overrides the Python executable path used by PHP. |
| `MODEL_PATH` | `python/models/ner_trf/model-best` | Path to the trained transformer model directory. |
| `TERMS_CSV_PATH` | `python/data/terms.csv` | Path to the entity terminology dictionary CSV. |

---

## Performance & Optimization Notes

- **CPU-only by default**: The pipeline runs on CPU with multi-threading, requiring no CUDA drivers or GPU hardware.
- **In-process caching**: Model weights and vocabulary are loaded once per process and held in memory for subsequent inference calls.
- **Clean output guarantee**: Pipeline logging is suppressed to `WARNING` level so that `sys.stdout` contains strictly valid JSON parseable by `json_decode()`.
