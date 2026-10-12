from pathlib import Path
import csv
import re

ROOT = Path(__file__).resolve().parents[2]

INPUT_FILE = ROOT / "data" / "raw" / "sneakershop.sql"
OUTPUT_DIR = ROOT / "data" / "processed" / "legacy"

TABLES = {
    "users",
    "categories",
    "products",
    "product_sizes",
    "addresses",
    "carts",
    "cart_items",
    "orders",
    "order_items",
    "inventory_transactions",
    "vnpay_transactions",
}

NULL_MARKER = "__NULL__"


def split_sql_statements(text):
    statements = []
    start = None
    in_string = False
    escaped = False

    i = 0

    while i < len(text):

        if start is None:
            pos = text.find("INSERT INTO", i)

            if pos == -1:
                break

            start = pos
            i = pos

        char = text[i]

        if in_string:
            if escaped:
                escaped = False

            elif char == "\\":
                escaped = True

            elif char == "'":
                # SQL escaped quote ''
                if i + 1 < len(text) and text[i + 1] == "'":
                    i += 1
                else:
                    in_string = False

        else:
            if char == "'":
                in_string = True

            elif char == ";":
                statements.append(text[start:i + 1])
                start = None

        i += 1

    return statements


def mysql_unescape(value):
    result = []
    i = 0

    escapes = {
        "0": "\0",
        "b": "\b",
        "n": "\n",
        "r": "\r",
        "t": "\t",
        "Z": "\x1a",
        "\\": "\\",
        "'": "'",
        '"': '"',
    }

    while i < len(value):

        if value[i] == "\\" and i + 1 < len(value):
            nxt = value[i + 1]

            if nxt in escapes:
                result.append(escapes[nxt])
            else:
                result.append("\\")
                result.append(nxt)

            i += 2
            continue

        # SQL quote escaping: ''
        if (
            value[i] == "'"
            and i + 1 < len(value)
            and value[i + 1] == "'"
        ):
            result.append("'")
            i += 2
            continue

        result.append(value[i])
        i += 1

    return "".join(result)


def parse_value(token):
    token = token.strip()

    if token.upper() == "NULL":
        return NULL_MARKER

    if token.startswith("'") and token.endswith("'"):
        return mysql_unescape(token[1:-1])

    return token


def split_rows(values_sql):
    rows = []

    depth = 0
    in_string = False
    escaped = False

    current = []

    i = 0

    while i < len(values_sql):
        char = values_sql[i]

        if in_string:
            current.append(char)

            if escaped:
                escaped = False

            elif char == "\\":
                escaped = True

            elif char == "'":
                if i + 1 < len(values_sql) and values_sql[i + 1] == "'":
                    current.append(values_sql[i + 1])
                    i += 1
                else:
                    in_string = False

        else:
            if char == "'":
                in_string = True
                current.append(char)

            elif char == "(":
                if depth > 0:
                    current.append(char)

                depth += 1

            elif char == ")":
                depth -= 1

                if depth == 0:
                    rows.append("".join(current))
                    current = []
                else:
                    current.append(char)

            elif depth > 0:
                current.append(char)

        i += 1

    return rows


def split_fields(row):
    fields = []

    current = []
    in_string = False
    escaped = False

    i = 0

    while i < len(row):
        char = row[i]

        if in_string:
            current.append(char)

            if escaped:
                escaped = False

            elif char == "\\":
                escaped = True

            elif char == "'":
                if i + 1 < len(row) and row[i + 1] == "'":
                    current.append(row[i + 1])
                    i += 1
                else:
                    in_string = False

        else:
            if char == "'":
                in_string = True
                current.append(char)

            elif char == ",":
                fields.append(parse_value("".join(current)))
                current = []

            else:
                current.append(char)

        i += 1

    fields.append(parse_value("".join(current)))

    return fields


def main():
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

    sql_text = INPUT_FILE.read_text(
        encoding="utf-8",
        errors="replace"
    )

    statements = split_sql_statements(sql_text)

    extracted = {}

    pattern = re.compile(
        r"INSERT INTO\s+`(?P<table>[^`]+)`\s*"
        r"\((?P<columns>.*?)\)\s*VALUES\s*"
        r"(?P<values>.*);$",
        re.DOTALL,
    )

    for statement in statements:
        match = pattern.search(statement)

        if not match:
            continue

        table = match.group("table")

        if table not in TABLES:
            continue

        columns = [
            column.strip().strip("`")
            for column in match.group("columns").split(",")
        ]

        rows = split_rows(match.group("values"))

        if table not in extracted:
            extracted[table] = {
                "columns": columns,
                "rows": [],
            }

        for row in rows:
            values = split_fields(row)

            if len(values) != len(columns):
                raise ValueError(
                    f"{table}: expected {len(columns)} columns "
                    f"but got {len(values)}"
                )

            extracted[table]["rows"].append(values)

    for table, data in extracted.items():
        output = OUTPUT_DIR / f"{table}.csv"

        with output.open(
            "w",
            newline="",
            encoding="utf-8-sig",
        ) as file:

            writer = csv.writer(file)

            writer.writerow(data["columns"])
            writer.writerows(data["rows"])

        print(
            f"{table:<25} "
            f"{len(data['rows']):>5} rows "
            f"-> {output.name}"
        )


if __name__ == "__main__":
    main()