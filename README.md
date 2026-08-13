Для запуска проекта надо выполнить следующие команды:
- docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
- docker compose exec php composer install
- docker compose exec node npm install
- docker compose exec php php bin/console doctrine:migrations:migrate

Сборка интерфейса CRM для прода:
- docker compose -f docker-compose.dev.yml run --rm node npm install
- docker compose -f docker-compose.dev.yml run --rm node npm run build

Пересборка воркера:
- docker compose up -d --build email-worker

Для создания нового пользователя с правами админа, нужна команда:
- docker compose exec php php bin/console app:root
