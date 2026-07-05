Для запуска проекта надо выполнить следующие команды:
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate
