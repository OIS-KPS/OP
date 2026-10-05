# OJT Journal Entity Extraction — Python Service

A standalone **FastAPI** microservice that extracts IT and clerical skill entities from OJT journal text using a hybrid pipeline (fine-tuned spaCy Transformer NER + dictionary matching). The PHP portal calls this service over HTTP — no Python runs inside the PHP process.

---

## Directory Structure

```text
python/
├── api/
│   ├── __init__.py          # Package marker
│   ├── main.py              # FastAPI application (endpoints, lifespan, dedup logic)
│   └── README.md            # Endpoint-specific API reference (request/response shapes)
├── scripts/
│   ├── __init__.py          # Package init — GPU bootstrap & init_gpu() helper
│   ├── pipeline.py          # HybridJournalPipeline class (ML + dictionary + conflict resolution)
│   ├── annotation.py        # Terms dictionary loader, weak-labeling, JSONL/DocBin I/O
│   └── labels.py            # Label taxonomy constants & normalization functions
├── data/
│   └── terms.csv            # Exact-match entity dictionary (term,label CSV — ~344 rows)
├── models/
│   └── ner_trf/
│       └── model-best/      # Fine-tuned spaCy transformer model checkpoint (weights, vocab, config)
├── extract_entities.py      # Legacy standalone script (DB + PDF extraction — NOT used by the API)
├── requirements.txt         # Pinned pip dependencies (full freeze)
└── .venv/                   # Python virtual environment (not committed)
```

---

## File Reference

### `api/main.py`

The FastAPI application entry point.

| Concern | Detail |
| --- | --- |
| **Lifespan** | Loads `HybridJournalPipeline` once at startup (CPU-only mode, `use_gpu=False`). Model loading takes **5–10 s** per worker. |
| **`GET /health`** | Readiness probe. Returns `503` until the pipeline finishes loading, then `{"status":"ok"}`. |
| **`POST /extract`** | Accepts `{"text": "..."}`, runs hybrid inference, deduplicates entities by `(term, category)` with frequency counts, and returns a JSON response shaped for the PHP frontend. |
| **Guards** | Rejects empty/whitespace-only text (`400`), text exceeding 100 000 characters (`400`), and returns `503` if the pipeline has not finished loading. |
| **Deduplication** | Entities appearing multiple times are collapsed into a single record with `"frequency": N`. |
| **Validation errors** | Pydantic validation failures return `{"success": false, "error": "Invalid request.", "details": "..."}` with status `400`. |

### `api/__init__.py`

Empty package marker — no logic.

### `api/README.md`

Detailed endpoint reference with full request/response JSON examples, latency benchmarks, and a PHP integration snippet. Read this file for the exact response contract.

---

### `scripts/__init__.py`

Package initializer. Provides:

- **`init_gpu(gpu_id=0) → bool`** — Thread-safe GPU initialization via spaCy/CuPy/PyTorch. The API deliberately does **not** call this (CPU-only), but the function exists for notebook/training use.
- **CUDA bootstrap** — Preloads NVIDIA shared libraries and sets `LD_LIBRARY_PATH` when running on GPU hardware.

### `scripts/pipeline.py`

Core inference engine — `HybridJournalPipeline` class.

| Method | Purpose |
| --- | --- |
| `__init__(model_path, terms_csv_path, confidence_threshold, use_gpu)` | Loads the spaCy transformer model and initializes a standalone `EntityRuler` dictionary matcher. |
| `predict(text, mode)` | Runs inference on a single text. `mode` can be `"hybrid"` (default), `"transformer_only"`, or `"entity_ruler_only"`. |
| `predict_batch(texts, mode)` | Batch wrapper around `predict()`. |
| `save(output_dir)` / `load(pipeline_dir, ...)` | Serialize/deserialize the pipeline to/from disk. |

**Conflict resolution rules** (implemented in `resolve_span_conflicts()`):

1. **Longest span wins** — a shorter dictionary match never truncates a longer ML span.
2. **ML label authority** — on label disagreement, the ML classification takes precedence.
3. **Abstention fallback** — dictionary entities are accepted only where ML abstained (no overlap).

Entities below the `confidence_threshold` (default `0.80`) are flagged `"status": "NEEDS_REVIEW"` instead of `"ACCEPTED"`.

### `scripts/annotation.py`

Dataset tooling (used at training time). At **inference time**, only one function is imported:

- **`load_terms_dictionary(terms_csv_path)`** — Reads `data/terms.csv`, normalizes legacy labels via `labels.py`, and returns a `{term: label}` dict sorted longest-first.

The remaining functions (`weak_label_corpus`, `save_jsonl`, `load_jsonl`, `convert_records_to_docbin`, `split_and_convert_dataset`, etc.) are training/data-prep utilities and are **not exercised** by the API.

### `scripts/labels.py`

Label taxonomy constants and normalization.

| Constant / Function | Purpose |
| --- | --- |
| `CANONICAL_NER_LABELS` | `{"IT_TERM", "CLERICAL_TERM"}` — the two entity categories the model emits. |
| `LABEL_MAPPING` | Maps legacy labels (`IT_TASK`, `CLERICAL`) → canonical labels. |
| `normalize_to_ner_label(raw)` | Defensive normalization for any recognized label string. |
| `is_valid_label(label)` | Quick check against known taxonomy. |

---

### `data/terms.csv`

Flat CSV with columns `term,label`. Contains ~344 curated terms (technologies, tools, clerical activities) used by the dictionary matcher. Example rows:

```csv
term,label
Java,IT_TERM
Microsoft Excel,CLERICAL_TERM
data entry,CLERICAL_TERM
FastAPI,IT_TERM
```

To add new dictionary terms, append rows to this file and restart the service.

### `models/ner_trf/model-best/`

A trained spaCy transformer model directory. Contains `config.cfg`, `meta.json`, vocabulary files, and PyTorch transformer weights. This is loaded by `HybridJournalPipeline` at startup.

> **Important**: This directory contains large binary weights and is excluded from git (`python/models/`). You need to download the model folder and see the video explanation from this Google Drive link:
> 🔗 [Google Drive: Model Folder & Video Explanation](https://drive.google.com/drive/folders/1NFow1Lgch4Wz6lV0aIONfouaGSthgLgk?usp=sharing)
>
> Once downloaded, place/extract the contents so that the directory structure is `python/models/ner_trf/model-best/`.

### `extract_entities.py`

**Legacy script** — a standalone, monolithic entity extractor that connects directly to MySQL (`nbsc_ojt`) and processes PDF files via `pdfplumber`. Its CLI entry point and database matching are **not used by the API**, but its PDF parsing routines (`extract_pdf_text()`, `build_war_column_scope()`, `extract_war_column_text()`) **are** imported by `POST /extract-pdf` to read the uploaded report and scope it to the `ACTIVITIES` / `REFLECTIONS` columns.

### `requirements.txt`

Full `pip freeze` output. For a minimal runtime install, only these packages are required:

```text
fastapi>=0.100.0
uvicorn[standard]>=0.23.0
pydantic>=2.0.0
spacy>=3.7.0
spacy-transformers>=1.3.0
torch
pandas
transformers
sentencepiece
```

---

## Setup & Running

### Prerequisites

- Python 3.10+
- The trained model checkpoint at `models/ner_trf/model-best/`:
  - Download the `models` folder and see the video explanation at: [Google Drive Link](https://drive.google.com/drive/folders/1NFow1Lgch4Wz6lV0aIONfouaGSthgLgk?usp=sharing)
  - Extract/place it inside `python/` so the path `python/models/ner_trf/model-best/` exists.

### 1. Create & activate the virtual environment

```bash
cd /opt/lampp/htdocs/ICS-PORTAL/python

python3 -m venv .venv
source .venv/bin/activate
```

### 2. Install dependencies

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

### 3. Start the server

**Development (with auto-reload):**

```bash
uvicorn api.main:app --host 0.0.0.0 --port 8000 --reload
```

**Production:**

```bash
uvicorn api.main:app --host 0.0.0.0 --port 8000 --workers 2
```

Wait until you see `Pipeline ready.` in the console before sending requests.

---

## Verifying the Service

### Health check

```bash
curl http://localhost:8000/health
```

Expected response (once ready):

```json
{
  "status": "ok",
  "model_path": "models/ner_trf/model-best"
}
```

> During startup (5–10 s), `/health` returns `503` with `"Pipeline not loaded yet."` — use this as a readiness probe.

### Entity extraction test

```bash
curl -X POST http://localhost:8000/extract \
  -H "Content-Type: application/json" \
  -d '{"text": "Assisted with data entry and developed microservices using FastAPI."}'
```

Expected response:

```json
{
  "success": true,
  "content": "Assisted with data entry and developed microservices using FastAPI.",
  "entities": [
    {
      "term": "data entry",
      "category": "CLERICAL_TERM",
      "start": 14,
      "end": 24,
      "confidence": 1.0,
      "source": "dictionary",
      "status": "ACCEPTED",
      "frequency": 1
    },
    {
      "term": "FastAPI",
      "category": "IT_TERM",
      "start": 59,
      "end": 66,
      "confidence": 0.88,
      "source": "ML",
      "status": "ACCEPTED",
      "frequency": 1
    }
  ],
  "entity_count": 2,
  "summary": {
    "total_occurrences": 2,
    "unique_entities": 2,
    "IT_TERM": 1,
    "CLERICAL_TERM": 1,
    "IT_TERM_percentage": 50.0,
    "CLERICAL_TERM_percentage": 50.0
  }
}
```

---

## Environment Variables

| Variable | Default | Description |
| --- | --- | --- |
| `MODEL_PATH` | `models/ner_trf/model-best` | Path to the trained spaCy transformer model directory. |
| `LOG_LEVEL` | `INFO` | Logging verbosity (`DEBUG`, `INFO`, `WARNING`, `ERROR`). |

To use an alternative model:

```bash
export MODEL_PATH="models/ner_trf_trstr_llm/model-best"
uvicorn api.main:app --host 0.0.0.0 --port 8000
```

---

## API Reference (Summary)

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/health` | Readiness probe — `200` when pipeline is loaded, `503` during startup. |
| `POST` | `/extract` | Entity extraction — accepts `{"text": "..."}`, returns entities with dedup + summary. |
| `POST` | `/extract-pdf` | Entity extraction from a report PDF — accepts `multipart/form-data` with a `file` field. Called by the PHP portal (`entityApiExtractPdf()`). |

### `POST /extract` — Request Body

```json
{
  "text": "string (required, non-empty, max 100,000 characters)"
}
```

### `POST /extract` — Response Body

```json
{
  "success": true,
  "content": "original input text",
  "entities": [
    {
      "term": "entity surface form",
      "category": "IT_TERM | CLERICAL_TERM",
      "start": 0,
      "end": 10,
      "confidence": 0.95,
      "source": "ML | dictionary",
      "status": "ACCEPTED | NEEDS_REVIEW",
      "frequency": 1
    }
  ],
  "entity_count": 1,
  "summary": {
    "total_occurrences": 1,
    "unique_entities": 1,
    "IT_TERM": 0,
    "CLERICAL_TERM": 1,
    "IT_TERM_percentage": 0.0,
    "CLERICAL_TERM_percentage": 100.0
  }
}
```

### Error Responses

| Status | Condition | Shape |
| --- | --- | --- |
| `400` | Empty/whitespace text, text too long, or malformed JSON | `{"success": false, "error": "...", "details": "..."}` |
| `500` | Pipeline inference exception | `{"success": false, "error": "Entity extraction failed.", "details": "..."}` |
| `503` | Pipeline not yet loaded (startup) | `{"success": false, "error": "Pipeline is not loaded. Try again shortly."}` |

---

## Integrating from PHP

From the PHP portal, call the API using `file_get_contents` or cURL:

```php
$response = file_get_contents('http://localhost:8000/extract', false,
    stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode(['text' => $journalText]),
        ]
    ])
);
$result = json_decode($response, true);

if ($result['success']) {
    foreach ($result['entities'] as $entity) {
        // $entity['term'], $entity['category'], $entity['confidence'], etc.
    }
}
```

Or with cURL:

```php
$ch = curl_init('http://localhost:8000/extract');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode(['text' => $journalText]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
```

---

## Performance Notes

- **CPU-only by design** — `use_gpu=False` eliminates GPU driver/CUDA dependencies.
- **Typical latency** — 90–165 ms p50 for journal entries up to ~220 characters (well within interactive UI tolerances of < 200 ms).
- **Model loading** — 5–10 seconds per uvicorn worker at startup.
- **Max payload** — 100 000 characters (enforced server-side).

---

## Relationship & Integration with `extract_entities.py`

The legacy `extract_entities.py` script is a self-contained tool that:

- Connects directly to MySQL to load predefined entities
- Parses PDF files using `pdfplumber` to extract text from WAR report columns
- Runs spaCy NER on extracted text
- Writes results back to the database

Only its **PDF parsing** side is used by the API: `POST /extract-pdf` calls `extract_pdf_text()`, `build_war_column_scope()` and `extract_war_column_text()` to turn an uploaded report into text before handing it to `HybridJournalPipeline`. Its CLI entry point, MySQL connection, and database matching are not part of the API runtime — the PHP portal owns persistence.

### Text extraction routines used by `/extract-pdf`

1. **WAR Column Extraction**: `build_war_column_scope(pdf)` locates the `ACTIVITIES` / `REFLECTIONS` table across pages; `extract_war_column_text()` renders that scope into the text the model is matched against.
2. **Full PDF Text**: `extract_pdf_text(pdf)` reads every page via `pdfplumber` and becomes the `content` field, so the reviewer page still renders the whole submission.
3. **Entity Pipeline**: the scoped text (or the full document when scoping fails) goes to `HybridJournalPipeline.predict(mode="hybrid")`, deduplicated and summarised exactly like `/extract`.

The functions are importable on their own if you need the same text outside the API:

```python
import pdfplumber
from extract_entities import (
    build_war_column_scope,
    extract_pdf_text,
    extract_war_column_text,
)

with pdfplumber.open("path/to/weekly_accomplishment_report.pdf") as pdf:
    full_text, err = extract_pdf_text(pdf)
    scope_result = build_war_column_scope(pdf)
    scoped_text = extract_war_column_text(scope_result) or full_text
```

To test a PDF straight against the service without going through PHP:

```bash
curl -X POST http://localhost:8000/extract-pdf \
  -F "file=@uploads/reports/WAR_Week_1_Student_111_1790603603.pdf;type=application/pdf"
```
