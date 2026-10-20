# MusicStats Helm chart

This chart deploys `benvaljean/musicstats:0.8.0`, a standalone Bitnami MySQL
instance, and a Traefik ingress. MySQL uses
the `local-path` StorageClass installed by default on k3s.

Add the secret `musicstats-db-credentials` manually first, in the format shown in the example.
