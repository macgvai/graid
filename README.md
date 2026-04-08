# Graid

Graid is an educational social network built with Laravel. Users can register, publish different post types, follow authors, comment, like, repost, exchange private messages, and use a protected JSON API powered by Sanctum.

## Main features

- Registration and login with avatar upload
- Post publishing for text, quote, photo, video, and link formats
- Feed filtered by subscriptions
- Search by text and hashtags
- Likes, comments, reposts, and private messages
- Email notifications for new subscribers and new posts
- Web interface and authenticated API endpoints

## Stack

- PHP 8.3+
- Laravel 13
- MySQL 8.4
- Laravel Sail / Docker Compose
- Vite for frontend assets
- PHPUnit, PHPCS, Psalm

## Local setup

1. Copy `.env.example` to `.env`.
2. Start containers:

```bash
./vendor/bin/sail up -d
```

3. Install dependencies and prepare the application:

```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run build
```

4. Open the application at `http://localhost` or the port from `APP_PORT`.

## Useful commands

Run tests:

```bash
./vendor/bin/sail php artisan test
```

Run coding standards:

```bash
./vendor/bin/sail vendor/bin/phpcs
```

Run static analysis:

```bash
./vendor/bin/sail vendor/bin/psalm --no-progress
```

## Demo account

- Email: `test@example.com`
- Password: `password`

The demo user is created by `DatabaseSeeder`.
