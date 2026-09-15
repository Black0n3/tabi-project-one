# Tabi — plan faza razvoja

Ovaj dokument je "izvor istine" za tijek razvoja. Prije prelaska na novu fazu,
provjeri da su svi zadaci prethodne faze označeni kao gotovi i da je faza
potvrđena (commit + kratki sažetak u ovom fileu, sekcija "Status").

Ne preskačemo faze i ne radimo veliki broj faza u istom prolazu bez provjere —
cilj je da svaka faza bude mala, testabilna cjelina.

## Dogovorene tehničke odluke

- **Backend**: Laravel (najnovija stabilna verzija), Livewire + Alpine.js (bez Inertia/Vue/React).
- **Admin paneli**: custom Livewire/Blade (bez Filament/Jetstream), dva odvojena panela:
  - `/admin` — globalni admin (Tabi)
  - `/investitor` — panel investitora (scoped na vlastite podatke)
- **Vizualizacija zgrade**: 2D interaktivna ilustracija (upload slike fasade/tlocrta +
  SVG poligon hotspot zone), NE pravi 3D/WebGL model.
- **Alat za crtanje zona**: poligon editor (proizvoljan broj točaka), admin klika
  točke po slici, koordinate se spremaju kao JSON, frontend renderira SVG overlay
  s hover/klik stanjem.
- **DB**: MySQL (produkcija), SQLite dopušteno za brzi lokalni dev ako zatreba.
- **Statusi jedinica**: `dostupno`, `rezervirano`, `prodano` (enum).

## Hijerarhija podataka

```
Investitor (tvrtka/nalog)
  └─ Projekt (npr. "Rezidencija Sunčani Vrt")
       └─ Objekat (zgrada / urbana vila / kuća...)
            └─ Kat (opcionalan, npr. "1. kat") — ima svoju sliku tlocrta kata
                 └─ Jedinica (stan / kuća) — status, m², cijena, opis
                      └─ Prostorija (soba, kuhinja...) — naziv, m², poligon na tlocrtu jedinice
```

Poligon (hotspot) zona živi na "djetetu" i referencira sliku "roditelja":
- Kat ima poligon nacrtan preko slike fasade Objekta.
- Jedinica ima poligon nacrtan preko slike tlocrta Kata (ili fasade Objekta ako
  Objekat nema katove, npr. kuće).
- Prostorija ima poligon nacrtan preko slike tlocrta Jedinice.

## Konvencija statusa faze

- ⬜ Nije započeto
- 🔶 U tijeku
- ✅ Gotovo (commitano)

---

## Faza 0 — Bootstrap projekta ✅

- [x] `composer create-project laravel/laravel` u repo (zadržati postojeći README/.git)
- [x] Laravel Breeze (Livewire stack) instaliran kao baza za auth (bez Jetstream/Filament)
- [x] Tailwind CSS konfiguriran, osnovni layout
- [x] `.env.example` sređen, `.gitignore` provjeren (dodano ignoriranje `database/*.sqlite`)
- [x] DB konekcija (SQLite za lokalni dev) + `php artisan migrate` prolazi
- [x] PHPUnit test runner radi (`php artisan test` — 26/26 testova prolazi)
- [x] Osnovni `README.md` s uputama za pokretanje projekta

## Faza 1 — Autentikacija i role ✅

- [x] `users` tablica: dodan `role` enum (`admin`, `investor`) — `App\Enums\UserRole`
- [x] Middleware/Gate za razlikovanje `/admin` i `/investitor` ruta — `role:admin` / `role:investor` middleware
- [x] Layout za `/admin` panel (sidebar, navigacija) — `layouts.admin` + `x-admin-layout`
- [x] Layout za `/investitor` panel (sidebar, navigacija) — `layouts.investor` + `x-investor-layout`
- [x] Seeder za super-admin korisnika (+ demo investitor za testiranje) —
  `admin@tabi.hr` / `investitor@tabi.hr`, lozinka `password`
- [x] Investitor se NE može self-registrirati — ruta `/register` uklonjena,
  nalog kreira isključivo admin (seeder sad, admin CRUD u Fazi 3)

## Faza 2 — Data model (migracije + modeli) ✅

- [x] `investors` (naziv tvrtke, OIB, kontakt, logo, veza na `users`)
- [x] `projects` (naziv, opis, lokacija, status, cover slika, `investor_id`, `is_featured`)
- [x] `buildings` — "Objekat" (naziv, tip: zgrada/urbana vila/kuća, adresa,
  slika fasade, `project_id`)
- [x] `floors` — "Kat" (label, redoslijed, poligon JSON na fasadi, slika tlocrta kata,
  `building_id`)
- [x] `units` — "Jedinica" (broj/oznaka, tip: stan/kuća, m², cijena, status,
  opis, slika tlocrta, poligon JSON, `building_id`, `floor_id` nullable, `is_featured`)
- [x] `rooms` — "Prostorija" (naziv, m², poligon JSON, `unit_id`)
- [x] Eloquent relacije (Investor→Project→Building→Floor→Unit→Room) + factories/seederi
  s demo podacima za razvoj (`DemoDataSeeder` — Sunčani Vrt d.o.o. → Rezidencija
  Sunčani Vrt → Zgrada A → 3 kata → 3 jedinice → prostorije)

## Faza 3 — Globalni admin panel (CRUD) ✅

- [x] CRUD: Investitori (kreiranje naloga investitora od strane admina —
  kreira i `User` login i `Investor` profil u istom obrascu)
- [x] CRUD: Projekti, Objekti, Jedinice, Prostorije — admin može upravljati
  podacima BILO KOJEG investitora, kroz kontekstualnu navigaciju
  Investitor → Projekt → Objekat → (Kat/Jedinica) → Prostorija
- [x] Upload slika (logo investitora, cover projekta, fasada objekta,
  tlocrt kata, tlocrt jedinice) s validacijom (`image`, max veličina),
  spremaju se na `public` disk
- [x] Pregled/pretraga liste investitora (search + paginacija); ostali nivoi
  hijerarhije pregledavaju se kroz "show" stranice roditelja (bez posebnog
  globalnog popisa — navigacija je kontekstualna)

## Faza 4 — Investitor panel (CRUD) ⬜

- [ ] Investitor vidi i uređuje samo svoje Projekte/Objekte/Jedinice/Prostorije
  (scoping po `investor_id`, autorizacija policy-jima)
- [ ] Isti UI obrasci kao admin panel (dijeljene Livewire komponente gdje ima smisla)
- [ ] Upload slika za vlastite objekte/jedinice

## Faza 5 — Poligon editor (alat za crtanje zona) ⬜

- [ ] Alpine.js/SVG komponenta: klik po slici dodaje točke poligona
- [ ] Spremanje/uređivanje/brisanje poligona (JSON koordinate) za Kat, Jedinicu, Prostoriju
- [ ] Pregled postojećih zona preko slike (edit mode) s mogućnošću pomicanja točaka
- [ ] Vezanje zone na entitet (npr. novonacrtani poligon na fasadi → odabir/kreiranje Kata)

## Faza 6 — Javni frontend ⬜

- [ ] Početna stranica: istaknuti/popularni Projekti i Jedinice
- [ ] Stranica Projekta: interaktivna fasada Objekta — hover/klik na kat odmah
  prikazuje detalje kata i listu jedinica na tom katu (bez dodatnog klika/modala,
  za razliku od inkubator.hr primjera)
- [ ] Stranica Jedinice: tlocrt s hover po prostorijama (naziv + m²), status badge
  (dostupno/rezervirano/prodano), osnovni opis, cijena, m²
- [ ] Responsive prikaz (mobitel — touch umjesto hover)

## Faza 7 — Pretraga, filteri, SEO ⬜

- [ ] Filteri na listi projekata/jedinica (lokacija, status, m², cijena)
- [ ] Meta tagovi, sitemap.xml
- [ ] Optimizacija slika (lazy load, responsive images)

## Faza 8 — Finalni polish i priprema za produkciju ⬜

- [ ] Testovi za autorizaciju (investitor ne smije vidjeti/uređivati tuđe podatke)
- [ ] Testovi ključnih Livewire komponenti
- [ ] Performance provjera (N+1 queryji, indeksi)
- [ ] Deployment checklist (env, storage link, queue/cache config)

---

## Status

**Trenutna faza:** Faza 3 gotova. Sljedeća: Faza 4 — Investitor panel (CRUD).

| Faza | Status |
|---|---|
| 0 — Bootstrap | ✅ |
| 1 — Auth i role | ✅ |
| 2 — Data model | ✅ |
| 3 — Admin panel | ✅ |
| 4 — Investitor panel | ⬜ |
| 5 — Poligon editor | ⬜ |
| 6 — Javni frontend | ⬜ |
| 7 — Pretraga/SEO | ⬜ |
| 8 — Polish/produkcija | ⬜ |
