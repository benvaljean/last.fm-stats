"""Browsable view of every play in the MySQL `plays` view."""

import streamlit as st


st.set_page_config(page_title="Every play", page_icon="🎵", layout="wide")


@st.cache_data(ttl=300)
def load_plays():
    """Load all plays; cache the result for five minutes."""
    connection = st.connection("mysql", type="sql")
    return connection.query(
        """
        SELECT
            dt,
            tz,
            oldartist,
            artist,
            oldtrack,
            track,
            album,
            trackmbid,
            artistmbid,
            albummbid,
            source
        FROM plays
        ORDER BY dt DESC
        """,
        ttl=300,
    )


st.title("Every play")
st.page_link("app.py", label="← Dashboard")
st.caption(
    "Use the table toolbar or Ctrl+F to search. You can sort, resize or hide "
    "columns, copy cells, use fullscreen, and download the displayed data."
)

try:
    plays = load_plays()
except Exception as error:
    st.error("Could not load plays from MySQL.")
    st.exception(error)
    st.stop()

st.caption(f"{len(plays):,} plays")
st.dataframe(
    plays,
    hide_index=True,
    width="stretch",
    height=750,
    lazy=False,
)
