# Deployment checklist

Praktični koraci za podizanje Tabi aplikacije na produkcijski server. Ovo je
checklist, ne punopravni infra-as-code setup — prilagodi konkretnom hostingu
(VPS, Forge, Vapor...).

## 1. Preduvjeti na serveru

- PHP 8.3+ (`composer.json` traži `^8.3`) s ekstenzijama: `pdo_mysql` (ili
  `pdo_sqlite` ako ipak ostaješ na SQLite-u), `mbstring`, `xml`, `curl`,
  `fileinfo`, `zip`, `gd` ili `intl` po potrebi
- Composer 2.x
- Node 20+ / npm (samo za build koraka, ne treba na serveru u runtimeu ako
  se assets buildaju u CI i samo kopiraju `public/build`)
- Web server (nginx/Apache) usmjeren na `public/` kao document root

## 2. `.env` za produkciju

Kopiraj `.env.example` u `.env` na serveru i postavi:

- `APP_ENV=production`
- `APP_DEBUG=false` — **obavezno**, inače se stack trace-ovi (uključujući DB
  kredencijale iz upita) prikazuju posjetiteljima na grešci
- `APP_URL=https://tvoja-domena.hr` — koristi se za generiranje javnih
  linkova, meta tagova (og:url, canonical) i `sitemap.xml` iz Faze 7
- `APP_KEY` — generiraj s `php artisan key:generate` (ne kopiraj key iz dev
  okruženja)
- `DB_CONNECTION=mysql` + `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/
  `DB_PASSWORD` — projekt je razvijen na SQLite-u lokalno, ali
  `PROJECT_PHASES.md` predviđa MySQL za produkciju; Eloquent/migracije ne
  koriste ništa SQLite-specifično pa je prelazak samo pitanje `.env`-a
- `SESSION_DRIVER=database`, `CACHE_STORE=database` rade out-of-the-box bez
  dodatne infrastrukture (Redis nije potreban za trenutni opseg aplikacije —
  nema queue jobova ni notifikacija koje bi od njega imale koristi)
- `MAIL_MAILER` — postavi na pravi SMTP/transactional provider ako se u
  budućnosti doda slanje e-maila (trenutno se ne koristi nigdje u aplikaciji)

## 3. Instalacija i build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan key:generate   # samo ako .env već nema APP_KEY
php artisan migrate --force
php artisan storage:link
```

`storage:link` je **kritičan korak** — sve uploadane slike (logo investitora,
cover projekta, fasada objekta, tlocrt kata/jedinice) spremaju se na
`storage/app/public` i servira ih se kroz `public/storage` simlink; bez
ovoga sve slike na sajtu vraćaju 404.

## 4. Ne pokretati demo seeder u produkciji

`DatabaseSeeder` (pokreće se s `php artisan db:seed`) kreira demo admin
(`admin@tabi.hr` / `password`) i demo investitora s lažnim projektom preko
`DemoDataSeeder`. **Ne pokretati u produkciji** — kreiraj stvarni admin nalog
ručno:

```bash
php artisan tinker
>>> \App\Models\User::create(['name' => '...', 'email' => '...', 'role' => \App\Enums\UserRole::Admin, 'password' => bcrypt('jaka-lozinka')]);
```

(Admin zatim kroz `/admin/investitori` kreira stvarne investitorske naloge —
to je i dizajnirano ponašanje iz Faze 1/3: self-registracija je namjerno
isključena.)

## 5. Optimizacija (nakon svakog deploya)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Napomena: `config:cache` zamrzava trenutni `.env` u cache — ako mijenjaš
env varijable nakon ovoga, moraš ponovno pokrenuti `config:cache` (ili
`config:clear`), inače će aplikacija i dalje čitati stare vrijednosti.

## 6. Web server / dozvole

- Document root: `public/`
- `storage/` i `bootstrap/cache/` moraju biti writable za PHP-FPM/web
  server usera
- Health check endpoint za uptime monitoring: `GET /up` (već konfiguriran u
  `bootstrap/app.php`)
- Ako je aplikacija iza reverse proxyja / load balancera koji terminira
  TLS (nginx pred PHP-FPM-om na istom hostu ne broji se — to je tek pitanje
  ako je ispred Cloudflare/ELB/itd.), provjeri da su generirani linkovi
  `https://` — po potrebi konfiguriraj `TrustProxies` u `bootstrap/app.php`
  (`->withMiddleware(fn ($m) => $m->trustProxies(at: '*'))` ili konkretan
  raspon IP-ova) jer trenutno nije postavljeno

## 7. Nema queue workera ni cron scheduler zadataka

Aplikacija trenutno ne koristi queued jobove, notifikacije ni Laravelov
task scheduler (`routes/console.php` ima samo defaultnu `inspire` naredbu) —
nije potrebno pokretati `queue:work` ni dodavati `* * * * * php artisan
schedule:run` u cron. Ako se ovo promijeni u budućnosti (npr. email
notifikacije investitorima), ovaj checklist treba proširiti.

## 8. Nakon deploya — brza provjera

- [ ] Naslovna (`/`) učitava se bez grešaka, slike se prikazuju
- [ ] Login radi za admin i investitor nalog
- [ ] Upload slike (npr. fasada objekta) radi i slika se prikazuje javno
- [ ] `sitemap.xml` dostupan i sadrži prave URL-ove s `https://` domenom
- [ ] `php artisan test` zeleno u CI prije svakog deploya
