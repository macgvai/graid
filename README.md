На проект затраченно около 20 часов 

php 8.4
composer install
composer run setup

# 1. создать нужные папки (важно!)
mkdir -p storage/framework/{views,cache,sessions}
mkdir -p bootstrap/cache

# 2. зависимости
composer install


# 3. поднять контейнеры
./vendor/bin/sail up -d

# 4. ключ приложения
./vendor/bin/sail artisan key:generate

# 5. очистить кеш (на всякий)
./vendor/bin/sail artisan optimize:clear

# 6. миграции
./vendor/bin/sail artisan migrate:fresh
./vendor/bin/sail artisan db:seed

# 7. ссылка на storage
./vendor/bin/sail artisan storage:link

# 8. Очереди
./vendor/bin/sail artisan queue:work

# 9 почта http://localhost:8025/

## Demo account
- Email: `test@example.com`
- Password: `password`

