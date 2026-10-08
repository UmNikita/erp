#!/bin/sh
# A pre-existing private validation checkout, never /opt/reklam-hub.
set -eu
stage=/opt/hub-validation.hIAy7b
test "$(realpath "$stage")" = /opt/hub-validation.hIAy7b
test -d "$stage/vendor"
network=hub-ui-validation-20261008
database=hub-ui-validation-db-20261008
if docker container inspect "$database" >/dev/null 2>&1; then
    printf '%s\n' 'Validation container name is already occupied; refusing to modify it.' >&2
    exit 1
fi
cleanup() { docker rm -f "$database" >/dev/null 2>&1 || true; docker network rm "$network" >/dev/null 2>&1 || true; }
docker network create "$network" >/dev/null
trap cleanup EXIT
docker run -d --name "$database" --network "$network" -e POSTGRES_DB=hub_validation -e POSTGRES_USER=validation -e POSTGRES_PASSWORD=validation_only postgres:16 >/dev/null
ready=0
for attempt in 1 2 3 4 5 6 7 8 9 10; do
    if docker exec "$database" pg_isready -U validation -d hub_validation >/dev/null 2>&1; then ready=1; break; fi
    sleep 1
done
test "$ready" = 1
docker run --rm --network "$network" -v "$stage:/var/www" -w /var/www -e APP_ENV=dev -e DATABASE_URL="postgresql://validation:validation_only@$database:5432/hub_validation?serverVersion=16&charset=utf8" reklam-hub-php sh -c 'php bin/console cache:clear && sh scripts/validate-modules.sh'
