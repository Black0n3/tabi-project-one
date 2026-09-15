# Tabi

Portal za investitore koji prodaju stanove/kuće u vlastitim projektima —
s interaktivnom vizualizacijom zgrada (fasada → katovi → jedinice → prostorije)
i dva admin panela (globalni admin + panel investitora).

Plan razvoja i arhitektonske odluke prati se u [`PROJECT_PHASES.md`](./PROJECT_PHASES.md).

## Stack

- Laravel 13 (PHP 8.3+)
- Livewire 3 + Volt + Alpine.js (auth scaffold: Laravel Breeze)
- Tailwind CSS + Vite
- SQLite lokalno (MySQL u produkciji)
- Pest/PHPUnit za testove

## Pokretanje projekta lokalno

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

npm run build   # ili `npm run dev` za watch mode
php artisan serve
```

Aplikacija je dostupna na `http://localhost:8000`.

## Testovi

```bash
php artisan test
```
