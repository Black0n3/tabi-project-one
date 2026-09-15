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

## Faza 4 — Investitor panel (CRUD) ✅

- [x] Investitor vidi i uređuje samo svoje Projekte/Objekte/Jedinice/Prostorije
  (scoping po `investor_id`, autorizacija Policy klasama: `ProjectPolicy`,
  `BuildingPolicy`, `FloorPolicy`, `UnitPolicy`, `RoomPolicy`)
- [x] Isti UI obrasci kao admin panel — CRUD komponente za
  Projekte/Objekte/Katove/Jedinice/Prostorije preseljene u
  `App\Livewire\Shared\*` i registrirane pod OBA panela (`/admin/...` i
  `/investitor/...`), s dinamičkim layoutom i route-prefiksom prema roli
  (`ResolvesPanelContext` trait). Investitor nema pristup CRUD-u za
  Investitore (to ostaje isključivo admin), a njegov dashboard
  (`App\Livewire\Investor\DashboardPage`) mu odmah prikazuje popis
  vlastitih projekata
- [x] Upload slika za vlastite objekte/jedinice (isti upload flow kao admin,
  provjeren i s pravim uploadom kroz preglednik)

## Faza 5 — Poligon editor (alat za crtanje zona) ✅

- [x] Alpine.js/SVG komponenta: klik po slici dodaje točke poligona
  (`x-zone-editor` Blade komponenta, `resources/views/components/zone-editor.blade.php`)
- [x] Spremanje/uređivanje/brisanje poligona (JSON koordinate) za Kat, Jedinicu, Prostoriju
  — `Shared\Buildings\ZonesPage` (fasada→katovi), `Shared\Floors\ZonesPage`
  (tlocrt kata→jedinice), `Shared\Units\ZonesPage` (tlocrt jedinice→prostorije)
- [x] Pregled postojećih zona preko slike (edit mode) s mogućnošću pomicanja točaka
  (klik na zonu → prikaz vučnih vrhova, drag mijenja oblik, "Spremi promjene")
- [x] Vezanje zone na entitet — nakon crtanja poligona admin/investitor bira
  postojeću stavku bez zone ILI upisuje naziv za novu stavku koja se kreira
  na licu mjesta i odmah poveže s nacrtanim poligonom

Napomena: Alpine `x-for`/`x-if` direktive ne rade pouzdano unutar `<svg>` elementa,
pa se SVG sadržaj zona gradi kao HTML string (`x-html`) uz event delegation
na `<svg>` korijenu, umjesto Alpine template direktiva unutar SVG-a. Koordinate
poligona spremaju se kao postoci (0-100) relativno na sliku, neovisno o rezoluciji.

## Faza 6 — Javni frontend ✅

- [x] Početna stranica: istaknuti/popularni Projekti i Jedinice
  (`Public\HomePage`, `/`)
- [x] Stranica Projekta (`/projekti/{project}`): info + kartice objekata
- [x] Stranica Objekta (`/objekti/{building}`): interaktivna fasada — hover
  (desktop) ili tap (mobitel) na kat na slici ODMAH prebacuje aktivni
  kat i prikazuje listu jedinica s statusima, bez dodatnog klika/modala
  (za razliku od inkubator.hr primjera); katovi bez nacrtane zone i
  dalje dostupni kroz tab listu pored slike; jedinice bez kata (npr. kuće)
  prikazane odvojeno ispod
- [x] Stranica Jedinice (`/jedinice/{unit}`): tlocrt s hover po prostorijama
  (naziv + m² prikazani na samoj zoni pri hoveru, sinkronizirano s
  popisom prostorija sa strane), status badge (dostupno/rezervirano/
  prodano), opis, cijena, m²
- [x] Responsive prikaz — iste hover zone rade na dodir (tap) na mobitelu,
  layout se prelama u jedan stupac ispod `lg` breakpointa

Sve tri interaktivne stranice čitaju iste `polygon` podatke koje admin/
investitor crtaju u Fazi 5 (postoci 0-100, ista point-in-polygon logika za
hover/tap detekciju kao u editoru, samo bez mogućnosti crtanja/uređivanja).

## Faza 7 — Pretraga, filteri, SEO ✅

- [x] Nove stranice za popis SVIH projekata/jedinica (`/projekti`, `/jedinice`,
  `Public\ProjectsIndex`, `Public\UnitsIndex`) s live filterima bez reloada
  stranice: projekti — lokacija, status; jedinice — lokacija (preko projekta),
  status, tip, min/max m², min/max cijena. Filteri se sinkroniziraju s query
  stringom (`#[Url]`) pa su rezultati pretrage shareable linkom. Naslovna i
  navigacija linkaju na ove stranice ("Svi projekti" / "Sve jedinice")
- [x] Meta tagovi — `<meta name="description">`, Open Graph (`og:title`,
  `og:description`, `og:image`, `og:url`), `<link rel="canonical">` u
  `layouts.public`, dinamički po stranici (opis projekta/objekta/jedinice,
  slika naslovnice/fasade/tlocrta kao og:image)
- [x] `sitemap.xml` (`SitemapController`, ruta `sitemap`) — nabraja naslovnu,
  oba popisa i sve pojedinačne projekte/objekte/jedinice s `lastmod`
- [x] Lazy loading slika (`loading="lazy"`) na svim kartičnim prikazima
  (naslovna, popisi, stranica projekta); glavne interaktivne slike na
  stranici objekta/jedinice namjerno ostaju eager jer su iznad fold-a

## Faza 8 — Finalni polish i priprema za produkciju ⬜

- [ ] Testovi za autorizaciju (investitor ne smije vidjeti/uređivati tuđe podatke)
- [ ] Testovi ključnih Livewire komponenti
- [ ] Performance provjera (N+1 queryji, indeksi)
- [ ] Deployment checklist (env, storage link, queue/cache config)

---

## Status

**Trenutna faza:** Faza 7 gotova. Sljedeća: Faza 8 — Finalni polish i priprema za produkciju.

| Faza | Status |
|---|---|
| 0 — Bootstrap | ✅ |
| 1 — Auth i role | ✅ |
| 2 — Data model | ✅ |
| 3 — Admin panel | ✅ |
| 4 — Investitor panel | ✅ |
| 5 — Poligon editor | ✅ |
| 6 — Javni frontend | ✅ |
| 7 — Pretraga/SEO | ✅ |
| 8 — Polish/produkcija | ⬜ |
