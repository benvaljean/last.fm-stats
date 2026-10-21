FROM python:2.7.18-slim-stretch

# Python 2 requires an archived Debian base. The image is deliberately isolated
# to this legacy importer and should not be used for the web application.
RUN sed -i \
      -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
      /etc/apt/sources.list \
    && sed -i '/security.debian.org/d; /stretch-updates/d' /etc/apt/sources.list \
    && apt-get -o Acquire::Check-Valid-Until=false update \
    && apt-get install -y --no-install-recommends default-mysql-client tzdata \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY lastexport.py importscrobbles.sql importscrobbles.sh ./
RUN chmod 0555 /app/importscrobbles.sh \
    && mkdir /work

ENV TZ=Europe/London
ENTRYPOINT ["/app/importscrobbles.sh"]
