#!/bin/sh
set -eu
test "$(pwd)" = /var/www
grep -q 'hub_validation' .env
find src migrations -name '*.php' -exec php -l {} \; > var/php-lint.log
php bin/console lint:container
php bin/console lint:twig templates/services/task templates/services/accounting
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:schema:validate --skip-sync
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php scripts/modules-smoke.php
php vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php tests/TaskItemTest.php
