import sys
import os
import json
import re

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
    return re.sub(
        r"\s+",
        " ",
        str(text or "").lower().strip()
    )


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
# READ PREDEFINED ENTITIES
# ============================================================

def load_predefined_entities(connection):

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
        ORDER BY LENGTH(entity_name) DESC
    """

    cursor.execute(query)

    entities = cursor.fetchall()

    cursor.close()

    return entities


# ============================================================
# EXTRACT TEXT FROM PDF
# ============================================================

def extract_pdf_text(pdf_path):

    full_text = []

    try:

        with pdfplumber.open(pdf_path) as pdf:

            for page_number, page in enumerate(
                pdf.pages,
                start=1
            ):

                page_text = (
                    page.extract_text()
                    or ""
                )

                if page_text.strip():

                    full_text.append(
                        f"--- PAGE {page_number} ---\n"
                        + page_text
                    )

    except Exception as e:

        return None, str(e)

    return (
        "\n\n".join(full_text).strip(),
        None
    )


# ============================================================
# COUNT A TERM IN THE PDF
# ============================================================

def count_term(text, term):

    term = normalize(term)

    if not term:
        return 0

    pattern = (
        r"(?<!\w)"
        + re.escape(term)
        + r"(?!\w)"
    )

    matches = re.findall(
        pattern,
        text,
        flags=re.IGNORECASE
    )

    return len(matches)


# ============================================================
# VERIFY PDF AGAINST PREDEFINED ENTITIES
# ============================================================

def find_predefined_matches(
    text,
    predefined_entities
):

    matches = []

    for entity in predefined_entities:

        canonical_name = (
            entity["entity_name"]
            or ""
        )

        aliases = (
            entity.get("aliases")
            or ""
        )

        # ----------------------------------------------------
        # Build searchable terms
        # ----------------------------------------------------

        terms = []

        if canonical_name.strip():
            terms.append(
                canonical_name
            )

        if aliases.strip():

            alias_list = aliases.split("|")

            for alias in alias_list:

                alias = alias.strip()

                if alias:
                    terms.append(alias)

        # ----------------------------------------------------
        # Remove duplicate search terms
        # ----------------------------------------------------

        unique_terms = []

        seen_terms = set()

        for term in terms:

            normalized_term = normalize(term)

            if (
                normalized_term
                and normalized_term not in seen_terms
            ):

                seen_terms.add(
                    normalized_term
                )

                unique_terms.append(term)

        # ----------------------------------------------------
        # Find the most frequent matching term
        # ----------------------------------------------------

        best_term = ""
        best_frequency = 0

        for term in unique_terms:

            frequency = count_term(
                text,
                term
            )

            if frequency > best_frequency:

                best_frequency = frequency

                best_term = term

        # ----------------------------------------------------
        # ONLY ADD IF FOUND IN PDF
        # ----------------------------------------------------

        if best_frequency > 0:

            matches.append({

                "predefined_id":
                    entity["id"],

                "entity":
                    entity["entity_name"],

                "entity_name":
                    entity["entity_name"],

                "canonical_name":
                    entity["entity_name"],

                "category":
                    entity["category"],

                "activity_type":
                    entity["activity_type"],

                "it_related":
                    entity["it_related"],

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


# ============================================================
# GET SPACY ENTITIES
# ============================================================

def extract_spacy_entities(
    doc
):

    entities = []

    seen = set()

    for ent in doc.ents:

        entity_text = (
            ent.text.strip()
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

def calculate_summary(
    entities
):

    it_related_count = 0

    clerical_count = 0

    for entity in entities:

        frequency = int(
            entity.get(
                "frequency",
                1
            )
        )

        it_related = str(
            entity.get(
                "it_related",
                ""
            )
        ).lower()

        if it_related == "yes":

            it_related_count += frequency

        elif it_related == "no":

            clerical_count += frequency

    total_count = (
        it_related_count
        + clerical_count
    )

    if total_count > 0:

        it_percentage = round(
            (
                it_related_count
                / total_count
            ) * 100,
            2
        )

        clerical_percentage = round(
            (
                clerical_count
                / total_count
            ) * 100,
            2
        )

    else:

        it_percentage = 0

        clerical_percentage = 0

    return {

        "total":
            total_count,

        "it_related":
            it_related_count,

        "clerical":
            clerical_count,

        "it_percentage":
            it_percentage,

        "clerical_percentage":
            clerical_percentage

    }


# ============================================================
# MAIN
# ============================================================

def main():

    # --------------------------------------------------------
    # Get PDF path
    # --------------------------------------------------------

    if len(sys.argv) < 2:

        fail(
            "No PDF path was provided."
        )

    pdf_path = os.path.abspath(
        sys.argv[1]
    )

    # --------------------------------------------------------
    # Verify PDF
    # --------------------------------------------------------

    if not os.path.isfile(pdf_path):

        fail(
            "PDF file not found.",
            pdf_path
        )

    # --------------------------------------------------------
    # Load spaCy
    # --------------------------------------------------------

    try:

        nlp = spacy.load(
            "en_core_web_sm"
        )

    except Exception as e:

        fail(
            "Failed to load spaCy model 'en_core_web_sm'.",
            str(e)
        )

    # --------------------------------------------------------
    # Extract PDF text
    # --------------------------------------------------------

    text, pdf_error = (
        extract_pdf_text(
            pdf_path
        )
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

    # --------------------------------------------------------
    # Process text using spaCy
    # --------------------------------------------------------

    try:

        doc = nlp(text)

    except Exception as e:

        fail(
            "spaCy processing failed.",
            str(e)
        )

    # --------------------------------------------------------
    # Get normal spaCy entities
    #
    # These are NOT displayed directly.
    # They are only the NLP processing result.
    # --------------------------------------------------------

    spacy_entities = (
        extract_spacy_entities(
            doc
        )
    )

    # --------------------------------------------------------
    # Connect to database
    # --------------------------------------------------------

    connection = None

    try:

        connection = connect_database()

        if connection is None:

            fail(
                "Unable to connect to database."
            )

        # ----------------------------------------------------
        # Load predefined entities
        # ----------------------------------------------------

        predefined_entities = (
            load_predefined_entities(
                connection
            )
        )

        # ----------------------------------------------------
        # IMPORTANT:
        #
        # Only entities that exist in
        # predefined_entities are returned.
        # ----------------------------------------------------

        matched_entities = (
            find_predefined_matches(
                text,
                predefined_entities
            )
        )

        # ----------------------------------------------------
        # Calculate summary
        # ----------------------------------------------------

        summary = calculate_summary(
            matched_entities
        )

        # ----------------------------------------------------
        # Return JSON to PHP
        # ----------------------------------------------------

        result = {

            "success":
                True,

            "content":
                text,

            "spacy_entities":
                spacy_entities,

            "entities":
                matched_entities,

            "predefined_matches":
                matched_entities,

            "entity_count":
                len(matched_entities),

            "summary":
                summary

        }

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


# ============================================================
# RUN
# ============================================================

if __name__ == "__main__":

    main()