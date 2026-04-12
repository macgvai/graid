# 1. зависимости
composer install

# 2. создать нужные папки (важно!)
mkdir -p storage/framework/{views,cache,sessions}
mkdir -p bootstrap/cache

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

## Demo account

- Email: `test@example.com`
- Password: `password`

The demo user is created by `DatabaseSeeder`.
