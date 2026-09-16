# Tabi

Portal za investitore koji prodaju stanove/kuće u vlastitim projektima —
s interaktivnom vizualizacijom zgrada (fasada → katovi → jedinice → prostorije)
i dva admin panela (globalni admin + panel investitora).

Plan razvoja i arhitektonske odluke prati se u [`PROJECT_PHASES.md`](./PROJECT_PHASES.md).
Checklist za produkcijski deploy je u [`DEPLOYMENT.md`](./DEPLOYMENT.md).

## Stack

- Laravel 13 (PHP 8.3+)
- Livewire 3 + Volt + Alpine.js (auth scaffold: Laravel Breeze)
- Tailwind CSS + Vite
- SQLite lokalno (MySQL u produkciji)
- Pest/PHPUnit za testove

## Preduvjeti (Ubuntu)

Projekt traži samo PHP 8.3+ (composer.json: `"php": "^8.3"`) i SQLite — nema
Docker/Sail setupa jer nije potreban: SQLite je obična datoteka (bez servera
za pokrenuti), pa "goli" PHP + Composer + Node rade bez ičeg dodatnog.

Ako Ubuntu repo nema dovoljno noviji PHP (`php -v`), dodaj Ondřejev PPA:

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-sqlite3 php8.4-zip php8.4-intl php8.4-gd \
    composer nodejs npm
```

(Ako već imaš PHP 8.4 iz nekog drugog izvora, provjeri samo da su ekstenzije
`pdo_sqlite`, `mbstring`, `xml`, `curl`, `fileinfo`, `zip` uključene —
`php -m | grep -E "sqlite|mbstring|xml|curl|fileinfo|zip"`.)

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
