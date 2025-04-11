## Настройка файлов

```
cp .development/.env.example .development/.env
cp .env.example ./.env
```

## Установка

```
cd .development/
docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) --build-arg UNAME=$(whoami) && docker compose up -d
docker compose exec php bash
composer install
php artisan key:generate --ansi
php artisan migrate:fresh --seed
```

## Frontend

```
cd .development/
docker compose run --rm -u $(id -u) node sh
npm install
npm run build
```

## Для параллельной обработки данных
```
cd .development/
docker compose start
docker compose exec php bash

for i in {1..10}; do
    php artisan queue:work --queue=default --sleep=1 --timeout=60 &
done
```
