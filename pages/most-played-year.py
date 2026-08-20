"""Year-filtered Last.fm play statistics."""

import streamlit as st


st.set_page_config(page_title="Most-played year", page_icon="🎵", layout="wide")


@st.cache_data(ttl=300)
def query(sql: str, params: dict[str, int]):
    """Run a read-only parameterized query, refreshing every five minutes."""
    connection = st.connection("mysql", type="sql")
    return connection.query(sql, params=params, ttl=300)


def get_year() -> int:
    """Read and validate the year supplied by the overview-page link."""
    value = st.query_params.get("year")
    if value is None:
        st.error("Choose a year from the dashboard overview.")
        st.page_link("app.py", label="Back to dashboard")
        st.stop()

    try:
        year = int(value)
    except (TypeError, ValueError):
        st.error("The `year` parameter must be a whole number.")
        st.stop()

    if not 1 <= year <= 9999:
        st.error("The `year` parameter must be between 1 and 9999.")
        st.stop()
    return year


def show_database_error(error: Exception) -> None:
    st.error("Could not load statistics from MySQL.")
    st.caption(
        "Configure the `mysql` SQL connection in `.streamlit/secrets.toml` "
        "before starting the dashboard."
    )
    st.exception(error)


year = get_year()
st.title(f"Most-played music in {year}")
st.page_link("app.py", label="← All years")

params = {"year": year}

try:
    most_played_songs = query("""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            LEFT(artist, 50) AS artist,
            LEFT(track, 50) AS track
        FROM plays
        WHERE YEAR(dt) = :year
        GROUP BY track, artist
        ORDER BY plays DESC
        LIMIT 50
    """, params)

    most_played_artists = query("""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            LEFT(artist, 50) AS artist
        FROM plays
        WHERE YEAR(dt) = :year
        GROUP BY artist
        ORDER BY plays DESC
        LIMIT 25
    """, params)
except Exception as error:
    show_database_error(error)
    st.stop()

st.subheader("Most-played songs")
st.dataframe(most_played_songs, hide_index=True, width=650, height="content")

st.subheader("Most-played artists")
st.dataframe(most_played_artists, hide_index=True, width=650, height="content")
