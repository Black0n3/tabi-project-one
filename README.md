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
    php8.4-sqlite3 php8.4-zip php8.4-intl php8.4-gd composer
```

(Ako već imaš PHP 8.4 iz nekog drugog izvora, provjeri samo da su ekstenzije
`pdo_sqlite`, `mbstring`, `xml`, `curl`, `fileinfo`, `zip` uključene —
`php -m | grep -E "sqlite|mbstring|xml|curl|fileinfo|zip"`.)

**Node**: Vite 8 (`package.json` → `"engines"`) traži Node `^20.19` ili
`>=22.12`. Ubuntov `apt install nodejs` obično instalira stariju verziju
(npr. 18.x) koja ne radi — build puca s greškom poput
`SyntaxError: The requested module 'node:util' does not provide an export
named 'styleText'`. Instaliraj noviji Node preko [nvm](https://github.com/nvm-sh/nvm)
umjesto apt paketa:

Install skripta (točan link je na [nvm-sh/nvm](https://github.com/nvm-sh/nvm#installing-and-updating)
jer se verzija povremeno mijenja), zatim:

```bash
# otvori novi terminal (ili source ~/.bashrc) nakon instalacije nvm-a, pa:
nvm install 22
nvm use 22
node -v   # treba biti 22.12+
```

## Pokretanje projekta lokalno

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed   # --seed dodaje demo podatke, vidi ispod

npm run build   # ili `npm run dev` za watch mode
php artisan serve
```

Aplikacija je dostupna na `http://localhost:8000`.

## Test/demo nalozi

`--seed` (ili `php artisan migrate:fresh --seed` za reset baze) puni bazu s
3 investitora (jedan ručno posložen "izlog" primjer + dva s nasumično
generiranim projektima, 4-6 projekata svaki, ukupno desetci zgrada/jedinica/
prostorija za testiranje pretrage, filtera i paginacije). Lozinka za sve
naloge je `password`.

| Uloga | Email | Lozinka | Napomena |
|---|---|---|---|
| Admin | `admin@tabi.hr` | `password` | `/admin` — vidi/uređuje sve investitore |
| Investitor 1 | `investitor@tabi.hr` | `password` | "Sunčani Vrt d.o.o." — ručno posloženi primjer (stabilni podaci, dobar za screenshotove) |
| Investitor 2 | `investitor2@tabi.hr` | `password` | "Jadranka Nekretnine d.o.o." — nasumično generirani projekti |
| Investitor 3 | `investitor3@tabi.hr` | `password` | "Kontinent Gradnja d.o.o." — nasumično generirani projekti |

## Testovi

```bash
php artisan test
```
