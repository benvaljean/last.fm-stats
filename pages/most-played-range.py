"""Most-played songs and artists for a year or a particular month."""

from calendar import month_name
from datetime import datetime

import streamlit as st

st.markdown("""
<style>
.st-key-month-table {
    line-height: 0.1 !important;
}
</style>
""", unsafe_allow_html=True)

st.set_page_config(page_title="Most-played range", page_icon="🎵", layout="wide")


@st.cache_data(ttl=300)
def query(sql: str, params: dict[str, int | str]):
    """Run a cached, read-only parameterized query."""
    connection = st.connection("mysql", type="sql")
    return connection.query(sql, params=params, ttl=300)


def get_period() -> tuple[int | None, int | None]:
    """Read optional year and month filters from the URL."""
    raw_year = st.query_params.get("year")
    if raw_year is None:
        year = None
    else:
        try:
            year = int(raw_year)
        except (TypeError, ValueError):
            st.error("The `year` parameter must be a whole number.")
            st.stop()

    raw_month = st.query_params.get("month")
    if raw_month is None:
        return year, None

    if year is None:
        st.error("The `month` parameter requires a `year` parameter.")
        st.stop()

    try:
        month = int(raw_month)
    except (TypeError, ValueError):
        st.error("The `month` parameter must be a whole number from 1 to 12.")
        st.stop()

    if not 1 <= month <= 12:
        st.error("The `month` parameter must be between 1 and 12.")
        st.stop()
    return year, month


def parse_date(value: str, label: str) -> str | None:
    """Validate an optional ISO date and return it unchanged for MySQL."""
    value = value.strip()
    if not value:
        return None

    try:
        parsed_date = datetime.strptime(value, "%Y-%m-%d")
        if parsed_date.strftime("%Y-%m-%d") != value:
            raise ValueError
    except ValueError:
        st.error(f"{label} must use the format `yyyy-mm-dd`.")
        st.stop()
    return value


year, month = get_period()
params: dict[str, int | str] = {}
where_conditions: list[str] = []
title = "Most-played music"

if year is not None:
    params["year"] = year
    where_conditions.append("YEAR(dt) = :year")
    title = f"Most-played music in {year}"

if month is not None:
    params["month"] = month
    where_conditions.append("MONTH(dt) = :month")
    title = f"Most-played music in {month_name[month]} {year}"

from_column, to_column = st.columns(2)
with from_column:
    from_date = parse_date(
        st.text_input("From date", placeholder="yyyy-mm-dd"), "From date"
    )
with to_column:
    to_date = parse_date(
        st.text_input("To date", placeholder="yyyy-mm-dd"), "To date"
    )

if from_date and to_date and from_date > to_date:
    st.error("From date must be on or before to date.")
    st.stop()

if from_date:
    params["from_date"] = from_date
    where_conditions.append("DATE(dt) >= :from_date")
if to_date:
    params["to_date"] = to_date
    where_conditions.append("DATE(dt) <= :to_date")

if from_date or to_date:
    title = f"Most-played music from {from_date or 'the beginning'} to {to_date or 'today'}"

where_clause = f"WHERE {' AND '.join(where_conditions)}" if where_conditions else ""

st.markdown("""
<style>
[data-testid="stPageLink"] {
    margin-top: -0.35rem;
    margin-bottom: -0.35rem;
}

[data-testid="stPageLink"] a {
    padding-top: 0.1rem;
    padding-bottom: 0.1rem;
}
</style>
""", unsafe_allow_html=True)

st.title(title)
if month is not None:
    st.page_link(
        "pages/most-played-range.py",
        label=f"← {year} overview",
        query_params={"year": str(year)},
)

try:
    if month is None and year is not None:
        plays_per_month = query(f"""
            SELECT
                MONTH(dt) AS month_number,
                MONTHNAME(dt) AS month,
                COUNT(*) AS records
            FROM plays
            {where_clause}
            GROUP BY MONTH(dt), MONTHNAME(dt)
            ORDER BY month_number
        """, params)

    most_played_songs = query(f"""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            LEFT(artist, 50) AS artist,
            LEFT(track, 50) AS track
        FROM plays
        {where_clause}
        GROUP BY track, artist
        ORDER BY plays DESC
        LIMIT 50
    """, params)

    most_played_artists = query(f"""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            LEFT(artist, 50) AS artist
        FROM plays
        {where_clause}
        GROUP BY artist
        ORDER BY plays DESC
        LIMIT 25
    """, params)
except Exception as error:
    st.error("Could not load statistics from MySQL.")
    st.exception(error)
    st.stop()

if month is None and year is not None:
    st.subheader("Plays by month")

    _, middle_column, _ = st.columns([1, 2, 1], gap=None)

    with middle_column:
        month_table = st.container(key="month-table")
        with month_table:
            month_column, records_column = st.columns((1, 1), gap=None)
            month_column.markdown("**Month**")
            records_column.markdown("**Records**")

            for row in plays_per_month.itertuples(index=False):
                month_column, records_column = st.columns((1, 1), gap=None)

                month_column.page_link(
                    "pages/most-played-range.py",
                    label=row.month,
                    query_params={
                        "year": str(year),
                        "month": str(row.month_number),
                    },
                )
                records_column.write(f"{row.records:,}")

st.subheader("Most-played songs")
st.dataframe(most_played_songs, hide_index=True, width=650, height="content")

st.subheader("Most-played artists")
st.dataframe(most_played_artists, hide_index=True, width=650, height="content")
