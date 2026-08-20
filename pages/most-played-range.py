"""Most-played songs and artists for a year or a particular month."""

from calendar import month_name

import streamlit as st


st.set_page_config(page_title="Most-played range", page_icon="🎵", layout="wide")


@st.cache_data(ttl=300)
def query(sql: str, params: dict[str, int]):
    """Run a cached, read-only parameterized query."""
    connection = st.connection("mysql", type="sql")
    return connection.query(sql, params=params, ttl=300)


def get_period() -> tuple[int, int | None]:
    """Read a required year and an optional month from the URL."""
    try:
        year = int(st.query_params["year"])
    except (KeyError, TypeError, ValueError):
        st.error("Choose a year from the dashboard overview.")
        st.page_link("app.py", label="Back to dashboard")
        st.stop()

    raw_month = st.query_params.get("month")
    if raw_month is None:
        return year, None

    try:
        month = int(raw_month)
    except (TypeError, ValueError):
        st.error("The `month` parameter must be a whole number from 1 to 12.")
        st.stop()

    if not 1 <= month <= 12:
        st.error("The `month` parameter must be between 1 and 12.")
        st.stop()
    return year, month


year, month = get_period()
params = {"year": year}
where_clause = "WHERE YEAR(dt) = :year"
title = f"Most-played music in {year}"

if month is not None:
    params["month"] = month
    where_clause += " AND MONTH(dt) = :month"
    title = f"Most-played music in {month_name[month]} {year}"

st.title(title)
st.page_link(
    "pages/most-played-year.py",
    label=f"← {year} overview",
    query_params={"year": str(year)},
)

try:
    if month is None:
        plays_per_month = query("""
            SELECT
                MONTH(dt) AS month_number,
                MONTHNAME(dt) AS month,
                COUNT(*) AS records
            FROM plays
            WHERE YEAR(dt) = :year
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

if month is None:
    st.subheader("Plays by month")

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
