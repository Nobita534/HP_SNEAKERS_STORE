from pathlib import Path
import csv
import json

import psycopg
from psycopg import sql
from dotenv import dotenv_values


PROJECT_ROOT = Path(__file__).resolve().parents[3]
PIPELINE_ROOT = PROJECT_ROOT / "data_pipeline"

DATA_DIR = (
    PIPELINE_ROOT
    / "data"
    / "processed"
    / "legacy"
)

NULL_MARKER = "__NULL__"

TABLE_ORDER = [
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
]

JSON_COLUMNS = {
    ("products", "images"),
    ("vnpay_transactions", "response_data"),
}


def get_connection():
    env = dotenv_values(PROJECT_ROOT / ".env")

    return psycopg.connect(
        host=env.get("DB_HOST", "127.0.0.1"),
        port=env.get("DB_PORT", "5432"),
        dbname=env["DB_DATABASE"],
        user=env["DB_USERNAME"],
        password=env["DB_PASSWORD"],
    )


def convert_value(table, column, value):
    if value == NULL_MARKER:
        return None

    # Validate JSON but preserve original representation
    if (table, column) in JSON_COLUMNS and value != "":
        json.loads(value)

    return value


def ensure_tables_empty(conn):
    non_empty = []

    with conn.cursor() as cur:
        for table in TABLE_ORDER:

            query = sql.SQL(
                "SELECT COUNT(*) FROM {}"
            ).format(
                sql.Identifier(table)
            )

            cur.execute(query)

            count = cur.fetchone()[0]

            if count > 0:
                non_empty.append((table, count))

    if non_empty:
        raise RuntimeError(
            "Các business table chưa rỗng: "
            + ", ".join(
                f"{table}={count}"
                for table, count in non_empty
            )
        )


def load_table(conn, table):
    csv_file = DATA_DIR / f"{table}.csv"

    if not csv_file.exists():
        print(f"SKIP {table}: CSV không tồn tại")
        return

    with csv_file.open(
        "r",
        encoding="utf-8-sig",
        newline="",
    ) as file:

        reader = csv.DictReader(file)

        columns = reader.fieldnames

        placeholders = sql.SQL(", ").join(
            sql.Placeholder()
            for _ in columns
        )

        query = sql.SQL(
            "INSERT INTO {} ({}) VALUES ({})"
        ).format(
            sql.Identifier(table),

            sql.SQL(", ").join(
                sql.Identifier(column)
                for column in columns
            ),

            placeholders,
        )

        rows = []

        for row in reader:
            values = [
                convert_value(
                    table,
                    column,
                    row[column]
                )
                for column in columns
            ]

            rows.append(values)

        with conn.cursor() as cur:
            cur.executemany(query, rows)

        print(
            f"Loaded {len(rows):>5} rows "
            f"into {table}"
        )


def reset_sequence(conn, table):
    query = sql.SQL("""
        SELECT setval(
            pg_get_serial_sequence({}, 'id'),
            COALESCE(MAX(id), 1),
            MAX(id) IS NOT NULL
        )
        FROM {}
    """).format(
        sql.Literal(table),
        sql.Identifier(table),
    )

    with conn.cursor() as cur:
        cur.execute(query)


def main():
    with get_connection() as conn:

        ensure_tables_empty(conn)

        # One transaction:
        # any table fails -> whole migration rolls back.
        with conn.transaction():

            for table in TABLE_ORDER:
                load_table(conn, table)

            for table in TABLE_ORDER:
                reset_sequence(conn, table)

        print("\nMigration completed successfully.")


if __name__ == "__main__":
    main()