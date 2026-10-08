#!/bin/bash
# Deploy only the approved Git delta; do not reset the customized live checkout.
set -euo pipefail
target=/opt/reklam-hub
base=${2:-a203c123d0b7ff61ad27cbb32f0c8b6c1b5eb399}
release=${1:?Provide the verified release commit}
test "$(realpath "$target")" = /opt/reklam-hub
cd "$target"
git cat-file -e "$release^{commit}"
git merge-base --is-ancestor "$base" "$release"
stage=$(mktemp -d /opt/hub-tasks-release.XXXXXX)
chmod 700 "$stage"
git diff --no-renames --name-only --diff-filter=AM "$base" "$release" > "$stage/files.txt"
mapfile -t files < "$stage/files.txt"
test "${#files[@]}" -gt 0
for path in "${files[@]}"; do
    [[ "$path" =~ ^[a-zA-Z0-9_./-]+$ && "$path" != /* && "$path" != *..* ]] || exit 1
done
git archive "$release" -- "${files[@]}" | tar -x -C "$stage"
for path in "${files[@]}"; do
    case "$path" in
        assets/*|src/*|templates/*)
            if git cat-file -e "$base:$path" 2>/dev/null; then
                test -f "$target/$path"
                expected=$(git show "$base:$path" | tr -d '\r' | sha256sum | cut -d' ' -f1)
                actual=$(tr -d '\r' < "$target/$path" | sha256sum | cut -d' ' -f1)
                if [ "$expected" != "$actual" ]; then echo "Unexpected live change: $path" >&2; exit 1; fi
            elif [ -e "$target/$path" ]; then
                cmp -s "$stage/$path" "$target/$path" || { echo "New-file collision: $path" >&2; exit 1; }
            fi
        ;;
    esac
done
backup=$(mktemp -d /var/backups/hub-tasks-git-20261008.XXXXXX)
chmod 700 "$backup"
echo "BACKUP=$backup"
git status --short > "$backup/git-status.txt"
printf '%s\n' "$release" > "$backup/release.txt"
tar -czf "$backup/source.tgz" --exclude=./vendor --exclude=./node_modules --exclude=./var/cache .
mkdir -p "$backup/rollback"
: > "$backup/added-files.txt"
for path in "${files[@]}"; do
    if [ -e "$path" ]; then cp --parents -a "$path" "$backup/rollback/"; else printf '%s\n' "$path" >> "$backup/added-files.txt"; fi
done
applied=0
successful=0
finish() {
    status=$?
    if [ "$successful" != 1 ]; then
        if [ "$applied" = 1 ]; then
            while IFS= read -r path; do
                if [ -e "$target/$path" ]; then
                    resolved=$(realpath "$target/$path")
                    [[ "$resolved" == "$target/"* ]] || continue
                    mkdir -p "$backup/failed-release/$(dirname "$path")"
                    mv "$target/$path" "$backup/failed-release/$path"
                fi
            done < "$backup/added-files.txt"
            cp -a "$backup/rollback/." "$target/"
            docker run --rm --network reklam-hub_default -v "$target:/var/www" -w /var/www reklam-hub-php php bin/console cache:clear --env=prod --no-debug || true
        fi
        docker start reklam-hub-php-1 reklam-hub-nginx-1 reklam-hub-email-worker-1 >/dev/null || true
        echo "Deployment failed; source restored. Additive database migrations are retained. Backup: $backup" >&2
    fi
    exit "$status"
}
trap finish EXIT
docker stop reklam-hub-nginx-1 reklam-hub-php-1 reklam-hub-email-worker-1 >/dev/null
docker exec reklam-hub-db-1 pg_dump -U reklam_admin -d app -Fc > "$backup/database.dump"
test -s "$backup/database.dump"
docker exec -i reklam-hub-db-1 pg_restore --list < "$backup/database.dump" > "$backup/database-contents.txt"
applied=1
for path in "${files[@]}"; do
    mkdir -p "$target/$(dirname "$path")"
    cp -a "$stage/$path" "$target/$path"
done
docker run --rm --network reklam-hub_default -v "$target:/var/www" -w /var/www reklam-hub-php sh -c 'php bin/console doctrine:migrations:migrate --env=prod --no-debug --no-interaction && php bin/console cache:clear --env=prod --no-debug && php bin/console lint:container --env=prod --no-debug && php scripts/modules-readonly-check.php'
docker start reklam-hub-php-1 reklam-hub-nginx-1 reklam-hub-email-worker-1 >/dev/null
curl --fail --silent --show-error -o /dev/null -w 'LOGIN_HTTP=%{http_code}\n' --resolve hub.reklamynet.ru:443:127.0.0.1 https://hub.reklamynet.ru/login
successful=1
printf '%s\n' "RELEASE=$release" "STAGE=$stage" "Deployment complete."
