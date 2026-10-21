# Keep Python 2 only as a build stage. The runtime uses a current MariaDB
# client, which supports MySQL 8's default caching_sha2_password authentication.
FROM python:2.7.18-slim-stretch AS python2

FROM mariadb:11.4

COPY --from=python2 /usr/local /usr/local

WORKDIR /app
COPY lastexport.py importscrobbles.sql importscrobbles.sh ./
RUN chmod 0555 /app/importscrobbles.sh \
    && mkdir /work \
    && ln -s /usr/bin/mariadb /usr/local/bin/mysql \
    && printf '%s\n' /usr/local/lib > /etc/ld.so.conf.d/python2.7.conf \
    && ldconfig

ENV PATH="/usr/local/bin:${PATH}" \
    TZ=Europe/London
ENTRYPOINT ["/app/importscrobbles.sh"]
