"""Overview page for the Last.fm play statistics dashboard."""

import streamlit as st

st.markdown("""
<style>
.st-key-year-table {
    line-height: 0.1 !important;
}
</style>
""", unsafe_allow_html=True)

st.set_page_config(page_title="MusicStats NG", page_icon="🎵", layout="wide")


@st.cache_data(ttl=300)
def query(sql: str):
    """Run a read-only query, refreshing cached statistics every five minutes."""
    connection = st.connection("mysql", type="sql")
    return connection.query(sql, ttl=300)


def show_database_error(error: Exception) -> None:
    st.error("Could not load statistics from MySQL.")
    st.caption(
        "Configure the `mysql` SQL connection in `.streamlit/secrets.toml` "
        "before starting the dashboard."
    )
    st.exception(error)

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


st.title("MusicStats NG")
st.page_link("pages/everything.py", label="Browse every play")

try:
    plays_per_year = query("""
        SELECT YEAR(dt) AS year, COUNT(*) AS records
        FROM plays
        GROUP BY YEAR(dt)
        ORDER BY year
    """)
except Exception as error:
    show_database_error(error)
    st.stop()

st.subheader("Plays per year")

# Streamlit dataframes cannot contain st.page_link widgets.  This small
# two-column table keeps each year as a native Streamlit page link.

_, middle_column, _ = st.columns([1, 2, 1], gap=None)

with middle_column:
    year_table = st.container(key="year-table")
    with year_table:
        year_column, records_column = st.columns((1, 1), gap=None)
        year_column.markdown("**Year**")
        records_column.markdown("**Records**")
        for row in plays_per_year.itertuples(index=False):
            year_column, records_column = st.columns((1, 1))
            year_column.page_link(
                "pages/most-played-year.py",
                label=str(row.year),
                query_params={"year": str(row.year)},
            )
            records_column.write(f"{row.records:,}")

try:
    most_played_songs = query("""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            artist,
            track
        FROM plays
        GROUP BY track, artist
        ORDER BY plays DESC
        LIMIT 200
    """)

    most_played_artists = query("""
        SELECT
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS `rank`,
            COUNT(*) AS plays,
            artist
        FROM plays
        GROUP BY artist
        ORDER BY plays DESC
        LIMIT 100
    """)
except Exception as error:
    show_database_error(error)
    st.stop()

st.subheader("Most-played songs")
st.dataframe(most_played_songs, hide_index=True, width=650, height="content")

st.subheader("Most-played artists")
st.dataframe(most_played_artists, hide_index=True, width=650, height="content")
