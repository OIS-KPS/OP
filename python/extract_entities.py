import sys
import os
import json
import re
import unicodedata
import pdfplumber
import spacy
import mysql.connector
from mysql.connector import Error


# ============================================================
# DATABASE CONFIGURATION
# ============================================================

DB_HOST = "127.0.0.1"
DB_PORT = 3306
DB_NAME = "nbsc_ojt"
DB_USER = "root"
DB_PASSWORD = ""

# Generic words should never become predefined entities.
IGNORED_ENTITY_TERMS = {"my"}


# ============================================================
# ERROR HANDLER
# ============================================================

def fail(message, details=None):
    result = {
        "success": False,
        "error": message
    }

    if details:
        result["details"] = details

    print(
        json.dumps(
            result,
            ensure_ascii=False
        )
    )

    sys.exit(1)


# ============================================================
# NORMALIZE TEXT
# ============================================================

def normalize(text):
    """
    Normalize text for matching.

    Example:
        "PHP Programming"
        "php programming"
        "PHP   Programming"
        "PHP-programming"

    become easier to compare.
    """

    text = unicodedata.normalize("NFKC", str(text or "")).lower()
    text = re.sub(r"[-–—‑]", " ", text)
    text = re.sub(r"[^\w\s+#.]", " ", text, flags=re.UNICODE)
    return re.sub(r"\s+", " ", text).strip()


# ============================================================
# DATABASE CONNECTION
# ============================================================

def connect_database():

    try:

        connection = mysql.connector.connect(
            host=DB_HOST,
            port=DB_PORT,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASSWORD
        )

        if connection.is_connected():
            return connection

        return None

    except Error as e:

        fail(
            "Unable to connect to MySQL.",
            str(e)
        )


# ============================================================
# LOAD PREDEFINED ENTITIES
# ============================================================

def load_predefined_entities(connection):

    cursor = None

    try:

        cursor = connection.cursor(
            dictionary=True
        )

        query = """
            SELECT
                id,
                entity_name,
                aliases,
                category,
                activity_type,
                it_related,
                description
            FROM predefined_entities
            WHERE LOWER(TRIM(entity_name)) <> 'my'
              AND is_archived = 0
            ORDER BY
                CHAR_LENGTH(entity_name) DESC,
                entity_name ASC
        """

        cursor.execute(query)

        entities = cursor.fetchall()

        return entities

    finally:

        if cursor is not None:
            cursor.close()


# ============================================================
# EXTRACT TEXT FROM PDF
# ============================================================

def pdf_pages_text(pdf):
    """
    Join the text of every page, keeping the page markers the
    reviewer already sees.
    """

    full_text = []

    for page_number, page in enumerate(
        pdf.pages,
        start=1
    ):

        page_text = (
            page.extract_text()
            or ""
        )

        page_text = page_text.strip()

        if page_text:

            full_text.append(
                f"--- PAGE {page_number} ---\n"
                + page_text
            )

    return "\n\n".join(full_text).strip()


def extract_pdf_text(pdf_source):
    """
    Extract the full document text.

    Accepts either a filesystem path or an already opened
    pdfplumber PDF, so the caller can read the document text and
    locate the WAR columns from a single open.
    """

    try:

        if isinstance(pdf_source, str):

            with pdfplumber.open(pdf_source) as pdf:

                return (
                    pdf_pages_text(pdf),
                    None
                )

        return (
            pdf_pages_text(pdf_source),
            None
        )

    except Exception as e:

        return None, str(e)



# ============================================================
# LOCATE THE ICS_WAR ACTIVITIES / REFLECTIONS COLUMNS
# ============================================================

# The ICS_WAR template is a two column table. Only what a trainee writes
# inside ACTIVITIES and REFLECTIONS describes their actual work, so entity
# extraction is anchored to those two columns instead of the whole page.
# Positions come from the PDF, never from the DOCX, because Word re-flows
# the table as soon as the trainee types.
WAR_ACTIVITY_HEADER_PATTERN = re.compile(r"^ACTIVIT", re.IGNORECASE)
WAR_REFLECTION_HEADER_PATTERN = re.compile(r"^REFLECT", re.IGNORECASE)

# The two columns, named the way the JSON result names them.
WAR_COLUMNS = ("activities", "reflections")

WAR_LABEL_PATTERNS = {
    "activities": WAR_ACTIVITY_HEADER_PATTERN,
    "reflections": WAR_REFLECTION_HEADER_PATTERN
}

# A line that is nothing but a column label is the template's own wording,
# not something the trainee wrote, so it is dropped wherever it turns up.
WAR_LABEL_LINE_PATTERNS = {
    "activities": re.compile(
        r"^ACTIVIT\w*:?$",
        re.IGNORECASE
    ),
    "reflections": re.compile(
        r"^REFLECT\w*:?$",
        re.IGNORECASE
    )
}

# A column label is a heading, not prose. A longer line is treated as
# student writing that merely mentions the word.
WAR_MAX_HEADER_LINE_LENGTH = 40

# Words whose tops differ by less than this share a visual line.
WAR_HEADER_LINE_TOLERANCE = 6.0

# Tolerance when deciding whether a ruling line is vertical.
WAR_RULE_TOLERANCE = 1.0

# A label is centred inside its header row, so the borders of that row sit
# a few points above and below the label itself. Borders are therefore
# matched against a band that reaches well past the text.
WAR_RULE_BAND_SLACK = 24.0

# Clearance used when a column is read straight off a cell border, so a
# glyph sitting flush against the border is not dropped.
WAR_COLUMN_PADDING = 1.0

# A table that continues onto the next page has to line up with the
# columns found on the page that carries the header.
WAR_TABLE_X_TOLERANCE = 12.0

# Anything narrower than this is not a column, it is a rule.
WAR_MIN_COLUMN_WIDTH = 20.0

WAR_SCOPE_STATUS_COLUMNS = "columns"
WAR_SCOPE_STATUS_NO_HEADER = "no_war_header"
WAR_SCOPE_STATUS_NO_DIVIDER = "no_war_divider"
WAR_SCOPE_STATUS_EMPTY = "empty_war_columns"


def is_vertical_rule(rule):
    """
    True when a table border runs top to bottom.
    """

    return (
        abs(rule["x0"] - rule["x1"]) <= WAR_RULE_TOLERANCE
        and (rule["bottom"] - rule["top"]) > 0
    )


def is_horizontal_rule(rule):
    """
    True when a table border runs left to right.
    """

    return (
        abs(rule["top"] - rule["bottom"]) <= WAR_RULE_TOLERANCE
        and (rule["x1"] - rule["x0"]) > 0
    )


def box_top(box):
    """
    The top edge of a box, given either a pdfplumber cell or a rule.
    """

    if isinstance(box, dict):

        return box["top"]

    return box[1]


def box_bottom(box):
    """
    The bottom edge of a box, given either a pdfplumber cell or a rule.
    """

    if isinstance(box, dict):

        return box["bottom"]

    return box[3]


def spans_band(box, top, bottom, slack):
    """
    True when a box overlaps a vertical band, padding included.
    """

    return (
        box_top(box) <= bottom + slack
        and box_bottom(box) >= top - slack
    )


def read_cell_text(page, x0, top, x1, bottom):
    """
    Read the text inside one rectangle of a page.
    """

    if bottom <= top or x1 <= x0:

        return ""

    box = (
        max(x0 - WAR_COLUMN_PADDING, 0),
        max(top - WAR_COLUMN_PADDING, 0),
        min(x1 + WAR_COLUMN_PADDING, page.width),
        min(bottom + WAR_COLUMN_PADDING, page.height)
    )

    try:

        return (
            page.crop(box)
            .extract_text()
            or ""
        ).strip()

    except Exception:

        return ""


def strip_column_label(name, text):
    """
    Remove a leading line that is nothing but the column label.

    Word repeats the header row at the top of every page, so the labels
    can reappear inside a block that was read as one piece.
    """

    lines = text.splitlines()

    while lines and not lines[0].strip():

        lines.pop(0)

    if lines and WAR_LABEL_LINE_PATTERNS[name].match(
        lines[0].strip()
    ):

        lines.pop(0)

    return "\n".join(lines).strip()


def is_label_row(texts):
    """
    True when a row only repeats the column labels.

    Tested against the raw text, before any label is stripped, so a row
    the trainee started with the word ACTIVITIES is still kept.
    """

    labelled = False

    for name in WAR_COLUMNS:

        text = texts.get(name) or ""

        if not text:

            continue

        first_line = text.splitlines()[0].strip()

        if not WAR_LABEL_LINE_PATTERNS[name].match(first_line):
    
            return False

        labelled = True

    return labelled


def build_column_ranges(activity_cell, reflection_cell):
    """
    Turn the two header cells into the column ranges they describe.
    """

    if activity_cell[0] <= reflection_cell[0]:

        left_cell, right_cell = activity_cell, reflection_cell

    else:

        left_cell, right_cell = reflection_cell, activity_cell

    left = left_cell[0]
    right = right_cell[2]
    divider = (
        left_cell[2] + right_cell[0]
    ) / 2.0

    ranges = {
        "activities": (
            activity_cell[0],
            activity_cell[2]
        ),
        "reflections": (
            reflection_cell[0],
            reflection_cell[2]
        )
    }

    for name in WAR_COLUMNS:

        x0, x1 = ranges[name]

        if x1 - x0 < WAR_MIN_COLUMN_WIDTH:

            return None

    if divider <= left or divider >= right:

        return None

    return {
        "left": left,
        "divider": divider,
        "right": right,
        "activities": ranges["activities"],
        "reflections": ranges["reflections"]
    }


def find_war_header_in_tables(page):
    """
    Find the header row among the ruled tables of a page.

    Reading the labels out of the header row's own cells hands back the
    exact column edges, which is what the rest of the extraction needs.
    """

    try:

        tables = page.find_tables()

    except Exception:

        return None

    for table in tables:

        for row in table.rows:

            cells = [
                cell for cell in (row.cells or [])
                if cell
            ]

            if len(cells) < 2:

                continue

            labels = {}

            for cell in cells:

                text = read_cell_text(
                    page,
                    cell[0],
                    cell[1],
                    cell[2],
                    cell[3]
                )

                first_line = (
                    text.splitlines()[0].strip()
                    if text else ""
                )

                for name, pattern in WAR_LABEL_PATTERNS.items():

                    if name in labels:

                        continue

                    if pattern.match(first_line):

                        labels[name] = cell

            if len(labels) != len(WAR_COLUMNS):

                continue

            columns = build_column_ranges(
                labels["activities"],
                labels["reflections"]
            )

            if columns is None:

                continue

            return {
                "top": row.bbox[1],
                "bottom": row.bbox[3],
                "columns": columns,
                "table": table,
                "header_row": row
            }

    return None


def find_war_header_row(page):
    """
    Find the row that labels the two WAR columns.

    Used when the page has no table to read the cells from. The labels are
    matched by word, not by position, so a trainee who typed REFLECTIONS
    first still resolves correctly.

    Returns the line the two labels sit on, or None when this page does
    not carry the WAR table header.
    """

    buckets = {}

    for word in page.extract_words():

        bucket = int(
            round(
                word["top"] / WAR_HEADER_LINE_TOLERANCE
            )
        )

        buckets.setdefault(
            bucket,
            []
        ).append(word)

    for bucket in sorted(buckets):

        words = buckets[bucket]

        activity_words = [
            word for word in words
            if WAR_ACTIVITY_HEADER_PATTERN.match(
                word["text"]
            )
        ]

        reflection_words = [
            word for word in words
            if WAR_REFLECTION_HEADER_PATTERN.match(
                word["text"]
            )
        ]

        if not activity_words or not reflection_words:
            continue

        activity = activity_words[0]
        reflection = reflection_words[0]

        # Both labels must sit on the same line, side by side.
        if abs(
            activity["x0"] - reflection["x0"]
        ) < 1:
            continue

        line_text = " ".join(
            word["text"]
            for word in sorted(
                words,
                key=lambda word: word["x0"]
            )
        )

        if len(line_text) > WAR_MAX_HEADER_LINE_LENGTH:
            continue

        return {
            "top": min(
                word["top"]
                for word in (activity, reflection)
            ),
            "bottom": max(
                word["bottom"]
                for word in (activity, reflection)
            ),
            "columns": None,
            "table": None,
            "header_row": None,
            "labels": {
                "activities": activity,
                "reflections": reflection
            }
        }

    return None


def find_war_header(page):
    """
    Locate the WAR header on a page.

    The ruled table is asked first because its header row carries the
    column edges, and the words are only the fallback for a page whose
    table was not detected.
    """

    header = find_war_header_in_tables(page)

    if header is not None:

        return header

    return find_war_header_row(page)


def find_war_rule_lines(page, header):
    """
    Collect the vertical borders that belong to the WAR table.

    A border only counts when it reaches the header band, which keeps
    unrelated rules elsewhere on the sheet out of the result.
    """

    return [
        rule for rule in page.edges
        if is_vertical_rule(rule)
        and spans_band(
            rule,
            header["top"],
            header["bottom"],
            WAR_RULE_BAND_SLACK
        )
    ]


def find_war_table_bottom(header, columns, verticals):
    """
    Find where the WAR table ends below its header row.

    The table's own borders are the measure. Measuring the last line of
    text instead would drag whatever sits under the table, such as the
    documentation heading, into the extracted columns.
    """

    left, right = columns["left"], columns["right"]

    bottoms = [
        rule["bottom"] for rule in verticals
        if left - WAR_RULE_TOLERANCE
        <= rule["x0"]
        <= right + WAR_RULE_TOLERANCE
        and rule["top"] >= header["top"] - WAR_RULE_BAND_SLACK
    ]

    if bottoms:

        return max(bottoms)

    return None


def resolve_war_columns(page, header):
    """
    Work out the x range of each WAR column from the ruling lines.

    Only needed when the header row could not be read out of a table. The
    ruling lines are then the only source for the divider, so a PDF
    exported without table borders still cannot be separated and returns
    None rather than mixing both columns together.

    Returns (columns, verticals) or (None, verticals).
    """

    activity = header["labels"]["activities"]
    reflection = header["labels"]["reflections"]

    left_label = min(
        activity["x0"],
        reflection["x0"]
    )

    right_label = max(
        activity["x1"],
        reflection["x1"]
    )

    divider_hint = (
        activity["x0"] + reflection["x0"]
    ) / 2.0

    verticals = find_war_rule_lines(
        page,
        header
    )

    divider_candidates = [
        rule for rule in verticals
        if left_label < rule["x0"] < right_label
    ]

    if not divider_candidates:

        return None, verticals

    divider_rule = min(
        divider_candidates,
        key=lambda rule: abs(
            rule["x0"] - divider_hint
        )
    )

    # The outer borders of the same table, so a second table elsewhere on
    # the page cannot stretch the columns across the sheet.
    siblings = [
        rule for rule in verticals
        if spans_band(
            rule,
            divider_rule["top"],
            divider_rule["bottom"],
            WAR_RULE_BAND_SLACK
        )
    ]

    left_candidates = [
        rule["x0"] for rule in siblings
        if rule["x0"] < left_label
    ]

    right_candidates = [
        rule["x1"] for rule in siblings
        if rule["x1"] > right_label
    ]

    left = (
        max(left_candidates)
        if left_candidates else left_label - WAR_COLUMN_PADDING
    )

    right = (
        min(right_candidates)
        if right_candidates else page.width
    )

    if divider_rule["x0"] <= left or divider_rule["x0"] >= right:

        return None, siblings

    if activity["x0"] <= reflection["x0"]:

        ranges = {
            "activities": (left, divider_rule["x0"]),
            "reflections": (divider_rule["x0"], right)
        }

    else:

        ranges = {
            "activities": (divider_rule["x0"], right),
            "reflections": (left, divider_rule["x0"])
        }

    columns = {
        "left": left,
        "divider": divider_rule["x0"],
        "right": right,
        "activities": ranges["activities"],
        "reflections": ranges["reflections"]
    }

    columns["bottom"] = find_war_table_bottom(
        header,
        columns,
        siblings
    )

    return columns, siblings


def find_continuation_table(page, columns):
    """
    Find the WAR table on a page that only continues it.

    The columns found on the first page are reused, so a continuation is
    only accepted when its borders line up with them.
    """

    try:

        tables = page.find_tables()

    except Exception:

        return None

    best = None
    best_height = 0.0

    for table in tables:

        bbox = table.bbox

        if abs(bbox[0] - columns["left"]) > WAR_TABLE_X_TOLERANCE:
            continue

        if abs(bbox[2] - columns["right"]) > WAR_TABLE_X_TOLERANCE:
            continue

        has_divider = any(
            abs(
                column.bbox[0] - columns["divider"]
            ) <= WAR_TABLE_X_TOLERANCE
            for column in table.columns
        )

        if not has_divider:
            continue

        height = bbox[3] - bbox[1]

        if height > best_height:

            best, best_height = table, height

    return best


def words_in_columns(page, columns, min_top=None):
    """
    Every word whose centre falls inside either column.
    """

    ranges = (
        columns["activities"],
        columns["reflections"]
    )

    words = []

    for word in page.extract_words():

        if min_top is not None and word["top"] < min_top:

            continue

        centre = (
            word["x0"] + word["x1"]
        ) / 2.0

        if any(
            x0 - 1 <= centre <= x1 + 1
            for x0, x1 in ranges
        ):

            words.append(word)

    return words


def resolve_continuation_rows(page, columns):
    """
    Find the extent of the two columns on a page that continues the table.

    Used when the page has no ruled table left to read, so the measure is
    the text that falls inside the columns instead of the table's borders.

    Returns (top, bottom) or None.
    """

    words = words_in_columns(
        page,
        columns
    )

    if not words:

        return None

    return (
        min(word["top"] for word in words) - WAR_COLUMN_PADDING,
        max(word["bottom"] for word in words) + WAR_COLUMN_PADDING
    )


def table_row_bands(table, header_row):
    """
    The horizontal slices of a table, one per row.

    The header row is left out, so the column labels never reach the
    entity matching.
    """

    start = 0

    for index, row in enumerate(table.rows):

        if row is header_row:

            start = index + 1

            break

    bands = []

    for row in table.rows[start:]:

        if row is None or not row.cells:

            continue

        bands.append((
            row.bbox[1],
            row.bbox[3]
        ))

    return bands


def header_row_bands(page, header, columns):
    """
    The body of the WAR table on the page that carries its header.
    """

    if header["table"] is not None:

        return table_row_bands(
            header["table"],
            header["header_row"]
        )

    top = header["bottom"] + 1
    bottom = columns.get("bottom")

    if bottom is None:

        words = words_in_columns(
            page,
            columns,
            min_top=top
        )

        bottom = (
            max(
                word["bottom"]
                for word in words
            ) + WAR_COLUMN_PADDING
            if words else None
        )

    if bottom is None or bottom <= top:

        return []

    return [(top, bottom)]


def continuation_row_bands(page, columns):
    """
    The body of the WAR table on a page that continues it.

    Continuation pages repeat the ruled rows but not the column labels, so
    the x ranges found on the first page are reused and only the rows have
    to be located. A table that still has its borders is read row by row
    like the first page; one that does not falls back on the extent of the
    text inside the columns.
    """

    table = find_continuation_table(
        page,
        columns
    )

    if table is not None:

        return table_row_bands(
            table,
            None
        )

    rows = resolve_continuation_rows(
        page,
        columns
    )

    if rows is None:

        return []

    top, bottom = rows

    if bottom <= top:

        return []

    return [(top, bottom)]


def collect_war_text(page, bands, columns, blocks):
    """
    Read the two columns of a page, one slice at a time.

    Working slice by slice keeps a cell from spilling into the row below it
    and keeps whatever sits under the table, such as the documentation
    heading, out of the result.
    """

    for top, bottom in bands:

        texts = {}

        for name in WAR_COLUMNS:

            x0, x1 = columns[name]

            texts[name] = read_cell_text(
                page,
                x0,
                top,
                x1,
                bottom
            )

        if is_label_row(texts):

            continue

        for name in WAR_COLUMNS:

            text = strip_column_label(
                name,
                texts[name]
            )

            if text:

                blocks[name].append(text)


def build_war_column_scope(pdf):
    """
    Locate the ACTIVITIES and REFLECTIONS columns across the document.

    Returns (scope, blocks). The scope describes what was found, so a
    report that cannot be scoped is reported as zero entities with a
    reason instead of silently matching the whole page.
    """

    scope = {
        "status": WAR_SCOPE_STATUS_NO_HEADER,
        "header_page": None,
        "pages_with_text": [],
        "columns": None,
        "message": ""
    }

    header = None
    header_page = None
    columns = None

    for page_number, page in enumerate(
        pdf.pages,
        start=1
    ):

        candidate = find_war_header(page)

        if candidate is None:

            continue

        resolved = candidate["columns"]

        if resolved is None:

            resolved, rules = resolve_war_columns(
                page,
                candidate
            )

        if resolved is None:

            if scope["status"] == WAR_SCOPE_STATUS_NO_HEADER:

                scope["status"] = WAR_SCOPE_STATUS_NO_DIVIDER
                scope["message"] = (
                    "The ACTIVITIES / REFLECTIONS table was found but its "
                    "column borders are missing, so the two columns cannot be "
                    "separated. Ask the trainee to re-export the report with "
                    "the table borders visible."
                )

            continue

        header = candidate
        header_page = page_number
        columns = resolved

        break

    if columns is None:

        if scope["status"] != WAR_SCOPE_STATUS_NO_DIVIDER:

            scope["message"] = (
                "No ACTIVITIES / REFLECTIONS table header was found, so no "
                "entities were extracted from this report."
            )

        return scope, {
            "activities": [],
            "reflections": []
        }

    blocks = {
        "activities": [],
        "reflections": []
    }

    pages_with_text = []

    for page_number, page in enumerate(
        pdf.pages,
        start=1
    ):

        if page_number == header_page:

            bands = header_row_bands(
                page,
                header,
                columns
            )

        else:

            bands = continuation_row_bands(
                page,
                columns
            )

        if not bands:

            continue

        before = (
            len(blocks["activities"])
            + len(blocks["reflections"])
        )

        collect_war_text(
            page,
            bands,
            columns,
            blocks
        )

        if (
            len(blocks["activities"])
            + len(blocks["reflections"])
        ) > before:

            pages_with_text.append(page_number)

    scope["status"] = (
        WAR_SCOPE_STATUS_COLUMNS
        if blocks["activities"] or blocks["reflections"]
        else WAR_SCOPE_STATUS_EMPTY
    )

    scope["header_page"] = header_page
    scope["pages_with_text"] = pages_with_text
    scope["columns"] = {
        "activities": {
            "x0": columns["activities"][0],
            "x1": columns["activities"][1]
        },
        "reflections": {
            "x0": columns["reflections"][0],
            "x1": columns["reflections"][1]
        }
    }

    if scope["status"] == WAR_SCOPE_STATUS_EMPTY:

        scope["message"] = (
            "The ACTIVITIES / REFLECTIONS columns were located but both are "
            "empty, so no entities were extracted."
        )

    else:

        scope["message"] = (
            "Entities were extracted only from the ACTIVITIES and REFLECTIONS "
            "columns."
        )

    return scope, blocks


def extract_war_column_text(scope_result):
    """
    Render the scoped column text for spaCy.

    Each column becomes its own labelled block so the two are never
    read as one continuous sentence.
    """

    scope, blocks = scope_result

    if scope["status"] != WAR_SCOPE_STATUS_COLUMNS:

        return ""

    parts = []

    if blocks["activities"]:

        parts.append(
            "ACTIVITIES:\n"
            + "\n".join(blocks["activities"])
        )

    if blocks["reflections"]:

        parts.append(
            "REFLECTIONS:\n"
            + "\n".join(blocks["reflections"])
        )

    return "\n\n".join(parts).strip()
# ============================================================
# CREATE SEARCHABLE PDF TEXT
# ============================================================

def prepare_search_text(text):

    """
    Creates a normalized version of the PDF text.

    The original PDF content is NOT changed.
    This is only used internally for matching.
    """

    return normalize(text)


# ============================================================
# TERM OCCURRENCES
# ============================================================

def find_term_occurrences(text, term):
    normalized_text = prepare_search_text(text)
    normalized_term = normalize(term)

    if not normalized_text or not normalized_term:
        return []

    if normalized_term in IGNORED_ENTITY_TERMS:
        return []

    pattern = r"(?<!\w)" + re.escape(normalized_term) + r"(?!\w)"
    return list(re.finditer(pattern, normalized_text, flags=re.IGNORECASE))


def count_term(text, term):

    return len(find_term_occurrences(text, term))


# ============================================================
# SPLIT ALIASES
# ============================================================

def split_aliases(aliases):

    if aliases is None:
        return []

    aliases = str(aliases).strip()

    if not aliases:
        return []

    # Support:
    #
    # PHP|PHP Language|PHP Programming
    #
    # PHP, PHP Language, PHP Programming
    #
    # PHP;PHP Language
    #
    # all together.

    parts = re.split(
        r"[|;,]",
        aliases
    )

    result = []

    for part in parts:

        part = part.strip()

        if part:
            result.append(part)

    return result


# ============================================================
# BUILD SEARCH TERMS
# ============================================================

def build_entity_terms(entity):

    terms = []

    canonical_name = str(
        entity.get("entity_name")
        or ""
    ).strip()

    if canonical_name:
        terms.append(
            canonical_name
        )

    aliases = split_aliases(
        entity.get("aliases")
    )

    terms.extend(
        aliases
    )

    # Remove duplicate normalized values
    unique_terms = []

    seen = set()

    for term in terms:

        normalized_term = normalize(term)

        if not normalized_term or normalized_term in IGNORED_ENTITY_TERMS:
            continue

        if normalized_term in seen:
            continue

        seen.add(
            normalized_term
        )

        unique_terms.append(
            term
        )

    return sorted(
        unique_terms,
        key=lambda term: (-len(normalize(term)), normalize(term))
    )


# ============================================================
# FIND PREDEFINED ENTITY MATCHES
# ============================================================

def find_predefined_matches(
    text,
    predefined_entities
):

    matches = []

    for entity in predefined_entities:

        canonical_name = str(entity.get("entity_name") or "").strip()
        if normalize(canonical_name) in IGNORED_ENTITY_TERMS:
            continue

        entity_terms = build_entity_terms(
            entity
        )

        if not entity_terms:
            continue

        best_term = ""
        best_frequency = 0
        best_term_length = 0

        # ----------------------------------------------------
        # Check canonical name and aliases
        # ----------------------------------------------------

        for term in entity_terms:

            occurrences = find_term_occurrences(text, term)
            frequency = len(occurrences)
            term_length = len(normalize(term))

            # Prefer the longer verified term when frequencies tie.
            if (
                frequency > best_frequency
                or (
                    frequency == best_frequency
                    and term_length > best_term_length
                )
            ):
                best_frequency = frequency
                best_term = term
                best_term_length = term_length

        # ----------------------------------------------------
        # Only return entities actually found
        # in the PDF.
        # ----------------------------------------------------

        if best_frequency <= 0:
            continue

        entity_name = str(
            entity.get("entity_name")
            or best_term
        ).strip()

        canonical_name = entity_name

        category = str(
            entity.get("category")
            or "Other"
        ).strip()

        activity_type = str(
            entity.get("activity_type")
            or "Other"
        ).strip()

        it_related = str(
            entity.get("it_related")
            or "unknown"
        ).strip().lower()

        # ----------------------------------------------------
        # Validate activity type
        # ----------------------------------------------------

        allowed_activity_types = [
            "Software",
            "Hardware",
            "Clerical",
            "Other"
        ]

        if activity_type not in allowed_activity_types:

            activity_type = "Other"

        # ----------------------------------------------------
        # Validate IT related
        # ----------------------------------------------------

        if it_related not in [
            "yes",
            "no",
            "unknown"
        ]:

            it_related = "unknown"

        # ----------------------------------------------------
        # Add matched entity
        # ----------------------------------------------------

        matches.append({

            "predefined_id":
                int(entity["id"]),

            "entity":
                entity_name,

            "entity_name":
                entity_name,

            "canonical_name":
                canonical_name,

            "category":
                category,

            "activity_type":
                activity_type,

            "it_related":
                it_related,

            "description":
                entity.get("description"),

            "matched_term":
                best_term,

            "frequency":
                best_frequency,

            "source":
                "predefined"

        })

    return matches


def resolve_entity_overlaps(text, matches):
    """
    Keep only verified, non-overlapping occurrences across all predefined
    entities. Longer terms win, so Windows 11 is preferred over Windows
    for the same occurrence, while a separate standalone Windows remains.
    """
    candidates = []
    for entity_index, match in enumerate(matches):
        term = normalize(match.get("matched_term", ""))
        if not term or term in IGNORED_ENTITY_TERMS:
            continue

        for occurrence in find_term_occurrences(text, term):
            candidates.append({
                "entity_index": entity_index,
                "start": occurrence.start(),
                "end": occurrence.end(),
                "length": occurrence.end() - occurrence.start()
            })

    candidates.sort(
        key=lambda item: (
            -item["length"],
            item["start"],
            item["entity_index"]
        )
    )

    accepted = []
    frequencies = {}

    for candidate in candidates:
        overlaps = any(
            candidate["start"] < span["end"]
            and candidate["end"] > span["start"]
            for span in accepted
        )
        if overlaps:
            continue

        accepted.append(candidate)
        index = candidate["entity_index"]
        frequencies[index] = frequencies.get(index, 0) + 1

    verified = []
    for index, match in enumerate(matches):
        frequency = frequencies.get(index, 0)
        if frequency == 0:
            continue

        verified_match = dict(match)
        verified_match["frequency"] = frequency
        verified.append(verified_match)

    return verified


# ============================================================
# SPAcy ENTITIES
# ============================================================

def extract_spacy_entities(doc):

    entities = []

    seen = set()

    for ent in doc.ents:

        entity_text = (
            ent.text
            .strip()
        )

        if not entity_text:
            continue

        key = (
            normalize(entity_text),
            ent.label_
        )

        if key in seen:
            continue

        seen.add(key)

        entities.append({

            "text":
                entity_text,

            "label":
                ent.label_

        })

    return entities


# ============================================================
# CALCULATE SUMMARY
# ============================================================

def calculate_summary(entities):

    software_count = 0
    hardware_count = 0
    clerical_count = 0
    other_count = 0

    it_related_count = 0
    non_it_count = 0

    total_frequency = 0

    for entity in entities:

        try:

            frequency = int(
                entity.get(
                    "frequency",
                    1
                )
            )

        except Exception:

            frequency = 1

        if frequency < 1:
            frequency = 1

        total_frequency += frequency

        # ----------------------------------------------------
        # Activity type
        # ----------------------------------------------------

        activity_type = str(
            entity.get(
                "activity_type",
                ""
            )
        ).strip().lower()

        if activity_type == "software":

            software_count += frequency

        elif activity_type == "hardware":

            hardware_count += frequency

        elif activity_type == "clerical":

            clerical_count += frequency

        else:

            other_count += frequency

        # ----------------------------------------------------
        # IT related
        # ----------------------------------------------------

        it_related = str(
            entity.get(
                "it_related",
                ""
            )
        ).strip().lower()

        if it_related == "yes":

            it_related_count += frequency

        elif it_related == "no":

            non_it_count += frequency

    # --------------------------------------------------------
    # IT / Clerical percentage
    # --------------------------------------------------------

    classified_total = (
        it_related_count
        + non_it_count
    )

    if classified_total > 0:

        it_percentage = round(
            (
                it_related_count
                / classified_total
            ) * 100,
            2
        )

        clerical_percentage = round(
            (
                non_it_count
                / classified_total
            ) * 100,
            2
        )

    else:

        it_percentage = 0
        clerical_percentage = 0

    # --------------------------------------------------------
    # Activity percentages
    # --------------------------------------------------------

    if total_frequency > 0:

        software_percentage = round(
            (
                software_count
                / total_frequency
            ) * 100,
            2
        )

        hardware_percentage = round(
            (
                hardware_count
                / total_frequency
            ) * 100,
            2
        )

        clerical_activity_percentage = round(
            (
                clerical_count
                / total_frequency
            ) * 100,
            2
        )

        other_percentage = round(
            (
                other_count
                / total_frequency
            ) * 100,
            2
        )

    else:

        software_percentage = 0
        hardware_percentage = 0
        clerical_activity_percentage = 0
        other_percentage = 0

    return {

        "total":
            total_frequency,

        "unique_entities":
            len(entities),

        "it_related":
            it_related_count,

        "clerical":
            non_it_count,

        "it_percentage":
            it_percentage,

        "clerical_percentage":
            clerical_percentage,

        "software":
            software_count,

        "hardware":
            hardware_count,

        "clerical_activity":
            clerical_count,

        "other":
            other_count,

        "software_percentage":
            software_percentage,

        "hardware_percentage":
            hardware_percentage,

        "clerical_activity_percentage":
            clerical_activity_percentage,

        "other_percentage":
            other_percentage
    }


# ============================================================
# MAIN
# ============================================================

def main():

    # The PHP caller reads stdout through a pipe, which on Windows
    # defaults to a code page that cannot encode the bullets and smart
    # quotes that appear in a trainee's report. Force UTF-8 up front so
    # neither the result nor an error can die on a character.
    try:

        sys.stdout.reconfigure(
            encoding="utf-8",
            errors="replace"
        )

    except Exception:

        pass

    # ========================================================
    # 1. GET PDF PATH
    # ========================================================

    if len(sys.argv) < 2:

        fail(
            "No PDF path was provided."
        )

    pdf_path = os.path.abspath(
        sys.argv[1]
    )

    # ========================================================
    # 2. VERIFY PDF
    # ========================================================

    if not os.path.isfile(pdf_path):

        fail(
            "PDF file not found.",
            pdf_path
        )

    # ========================================================
    # 3. LOAD spaCy
    # ========================================================

    try:

        nlp = spacy.load(
            "en_core_web_sm"
        )

    except Exception as e:

        fail(
            "Failed to load spaCy model 'en_core_web_sm'.",
            str(e)
        )

    # ========================================================
    # 4. EXTRACT PDF TEXT AND LOCATE THE WAR COLUMNS
    # ========================================================

    text = None
    scope_text = ""
    scope = {
        "status": WAR_SCOPE_STATUS_NO_HEADER,
        "message": ""
    }

    try:

        with pdfplumber.open(pdf_path) as pdf:

            text, pdf_error = extract_pdf_text(pdf)

            if pdf_error is None:

                scope, war_blocks = (
                    build_war_column_scope(pdf)
                )

                scope_text = extract_war_column_text(
                    (scope, war_blocks)
                )

    except Exception as e:

        fail(
            "Failed to read PDF.",
            str(e)
        )

    if pdf_error:

        fail(
            "Failed to read PDF.",
            pdf_error
        )

    if not text:

        fail(
            "No extractable text was found in the PDF."
        )

    # ========================================================
    # 5. PROCESS THE WAR COLUMNS WITH spaCy
    # ========================================================

    try:

        doc = nlp(scope_text)

    except Exception as e:

        fail(
            "spaCy processing failed.",
            str(e)
        )

    # ========================================================
    # 6. GET spaCy ENTITIES
    # ========================================================

    spacy_entities = (
        extract_spacy_entities(
            doc
        )
    )

    # ========================================================
    # 7. CONNECT DATABASE
    # ========================================================

    connection = None

    try:

        connection = connect_database()

        if connection is None:

            fail(
                "Unable to connect to database."
            )

        # ====================================================
        # 8. LOAD PREDEFINED ENTITIES
        # ====================================================

        predefined_entities = (
            load_predefined_entities(
                connection
            )
        )

        # ====================================================
        # 9. MATCH THE WAR COLUMNS AGAINST DATABASE
        # ====================================================

        matched_entities = (
            find_predefined_matches(
                scope_text,
                predefined_entities
            )
        )

        matched_entities = resolve_entity_overlaps(
            scope_text,
            matched_entities
        )

        # ====================================================
        # 10. CALCULATE SUMMARY
        # ====================================================

        summary = calculate_summary(
            matched_entities
        )

        # ====================================================
        # 11. RETURN JSON TO PHP
        # ====================================================

        result = {

            "success":
                True,

            # The full document text, so the reviewer page keeps
            # showing the whole submission.
            "content":
                text,

            # Only the ACTIVITIES and REFLECTIONS columns. This is
            # what every entity below was matched against.
            "scoped_content":
                scope_text,

            "extraction_scope":
                scope,

            # Raw spaCy entities are returned only
            # for debugging / verification.
            "spacy_entities":
                spacy_entities,

            # These are the ONLY entities that
            # should be displayed by the PHP page.
            "entities":
                matched_entities,

            "predefined_matches":
                matched_entities,

            "entity_count":
                len(matched_entities),

            "summary":
                summary

        }

        # The PHP caller reads stdout through a pipe, which on Windows
        # defaults to a code page that cannot encode bullets or smart
        # quotes found in a trainee's report. Force UTF-8 so extraction
        # never dies on a character.
        print(
            json.dumps(
                result,
                ensure_ascii=False
            )
        )

    except Error as e:
        fail(
            "Database error while loading predefined entities.",
            str(e)
        )

    except Exception as e:
        fail(
            "Entity matching failed.",
            str(e)
        )
    finally:
        if connection is not None:
            try:
                if connection.is_connected():
                    connection.close()
            except Exception:
                pass
if __name__ == "__main__":

    main()