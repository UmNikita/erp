#!/bin/sh
set -eu
# Source-only update; preserve .env, JWT keys, infrastructure and persistent data.
test "${1:-}" = /opt/reklam-hub
test -f /tmp/hub-modules-release-20261008.tgz
test -d /opt/reklam-hub/.git
cd /opt/reklam-hub
backup=$(mktemp -d /var/backups/hub-modules-20261008.XXXXXX)
chmod 700 "$backup"
git status --short > "$backup/git-status.txt"
tar -czf "$backup/source.tgz" --exclude=./vendor --exclude=./node_modules --exclude=./var/cache .
docker stop reklam-hub-email-worker-1 >/dev/null
docker exec reklam-hub-db-1 pg_dump -U reklam_admin -d app -Fc > "$backup/database.dump"
test -s "$backup/database.dump"
docker exec -i reklam-hub-db-1 pg_restore --list < "$backup/database.dump" > "$backup/database-contents.txt"
echo "BACKUP=$backup"
tar -xzf /tmp/hub-modules-release-20261008.tgz -C /opt/reklam-hub
docker run --rm --network reklam-hub_default -v /opt/reklam-hub:/var/www -w /var/www reklam-hub-php sh -c 'php bin/console doctrine:migrations:migrate --no-interaction && php bin/console cache:clear --env=prod --no-debug && php bin/console lint:container --env=prod --no-debug'
docker start reklam-hub-php-1 reklam-hub-nginx-1 reklam-hub-email-worker-1
docker update --restart unless-stopped reklam-hub-php-1 reklam-hub-nginx-1 >/dev/null
docker exec reklam-hub-php-1 php bin/console app:tasks:materialize-duties
curl --fail --silent --show-error -o /dev/null -w 'LOGIN_HTTP=%{http_code}\n' --resolve hub.reklamynet.ru:443:127.0.0.1 https://hub.reklamynet.ru/login
echo 'Deployment complete; verify authenticated module pages.'
