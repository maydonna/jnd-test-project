## About JND Test Project

Mini project for JND recruitment testing

## Framework & Tools used

- Laravel 11

# Development

### Requirement

- PHP 8.3
- MySQL 8
- Vue 3
- Nuxt 3
- TypeScript
- Tailwind
- Nuxt UI

### Installation

1. Clone project (and initialize git flow if using SourceTree [main, develop])
2. Create .env file by copy content from .env.example `cp .env.example .env`
3. Run `composer install` 
4. Run `php artisan key:generate`
5. Create Database (MySQL 8, Collation: utf8mb4_unicode_ci), update `DB_USERNAME`, `DB_PASSWORD` in .env
6. Run `php artisan migrate --seed` to migrate and seed database
7. Run `php artisan serve`
8. `cd client` to go to client folder and run `npm install`
9. Run `npm run dev` (or you can just run `npm run dev` on the root folder for the next time)

### Admin account for test
- email: `admin@example.com`
- password: `secret`
