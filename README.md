Для запуска проекта надо выполнить следующие команды:
- docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
- docker compose exec php composer install
- docker compose exec node npm install
- docker compose exec php php bin/console doctrine:migrations:migrate

Сборка интерфейса CRM для прода:
- docker compose exec node npm run build
- docker compose -f docker-compose.yml -f docker-compose.dev.yml stop node
- docker compose -f docker-compose.yml -f docker-compose.dev.yml rm node

Для создания нового пользователя с правами админа, нужна команда:
- docker compose exec php php bin/console app:root