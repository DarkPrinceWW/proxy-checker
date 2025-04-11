## Настройка файлов

```
cp .development/.env.example .development/.env
cp .env.example ./.env
```

## Установка Backend

```
cd .development/
docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) --build-arg UNAME=$(whoami) && docker compose up -d
docker compose exec php bash
composer install
php artisan key:generate --ansi
php artisan ide-helper:generate
php artisan ide-helper:meta
```
