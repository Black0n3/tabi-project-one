<div>
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-canvas">
        <x-hero-backdrop :image="$heroImage" />

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 sm:pt-28 sm:pb-20">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2.5 rounded-full border border-line bg-panel px-4 py-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-ink"></span>
                    <span class="text-[11px] font-bold uppercase tracking-[0.16em] text-ink-soft">{{ __('Novogradnja · Osijek i Slavonija') }}</span>
                </div>

                <h1 class="mt-7 font-display text-5xl sm:text-6xl lg:text-[66px] font-medium leading-[1.06] tracking-tight text-ink">
                    {{ __('Pretražite zgradu.') }}<br>{{ __('Odaberite svoj kat.') }}
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-ink-soft">
                    {{ __('Otvorite tlocrt svakog stana, provjerite dostupnost u stvarnom vremenu i pronađite dom izravno kod investitora — bez posrednika i skrivenih troškova.') }}
                </p>

                <form
                    x-data="heroSearch()"
                    @submit.prevent="submit()"
                    class="mt-10 max-w-2xl rounded-[20px] border border-white/15 bg-white/[0.06] backdrop-blur-xl p-3 shadow-2xl shadow-black/60"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-[1.3fr_1fr_auto] gap-2">
                        <label class="block rounded-[13px] bg-ink/95 px-4 py-2.5 transition hover:bg-white">
                            <span class="block text-[9px] font-bold uppercase tracking-[0.1em] text-stone-500">{{ __('Projekt') }}</span>
                            <select x-model="projectId" class="mt-0.5 block w-full border-0 bg-transparent p-0 text-sm font-semibold text-[#141414] focus:ring-0">
                                <option value="">{{ __('Svi projekti') }}</option>
                                @foreach ($searchProjects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block rounded-[13px] bg-ink/95 px-4 py-2.5 transition hover:bg-white">
                            <span class="block text-[9px] font-bold uppercase tracking-[0.1em] text-stone-500">{{ __('Sobnost') }}</span>
                            <select x-model="roomCount" class="mt-0.5 block w-full border-0 bg-transparent p-0 text-sm font-semibold text-[#141414] focus:ring-0">
                                <option value="">{{ __('Bilo koja') }}</option>
                                <option value="1">{{ __('1-sobni') }}</option>
                                <option value="2">{{ __('2-sobni') }}</option>
                                <option value="3">{{ __('3-sobni') }}</option>
                                <option value="4+">{{ __('4+ sobni') }}</option>
                            </select>
                        </label>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-[13px] bg-ink px-6 py-3.5 text-sm font-bold text-canvas transition hover:brightness-90">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                            {{ __('Pretraži') }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex items-center justify-between max-w-2xl">
                    <a href="{{ route('public.projects.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-semibold text-ink-soft transition-all hover:gap-2.5 hover:text-ink">
                        {{ __('ili pregledajte sve projekte') }} <span aria-hidden="true">→</span>
                    </a>
                    <span class="hidden sm:flex items-center gap-2 text-xs text-ink-faint">
                        <span class="h-1.5 w-1.5 rounded-full bg-ink anim-pulse-dot"></span> {{ __('Ažurirano u stvarnom vremenu') }}
                    </span>
                </div>

                <dl class="mt-14 flex flex-wrap gap-y-6">
                    @foreach ([['projects', 'projekata'], ['units', 'jedinica'], ['available', 'dostupno odmah'], ['locations', 'lokacija']] as $i => [$key, $label])
                        <div @class(['pr-8 sm:pr-9', 'pl-8 sm:pl-9 border-l border-line' => $i > 0])>
                            <dt class="font-display text-3xl font-semibold text-ink">{{ $stats[$key] }}</dt>
                            <dd class="mt-1 text-xs uppercase tracking-wider text-ink-faint">{{ __($label) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- PARTNER INVESTITORI (stvarni podaci iz baze) --}}
    @php $investorNames = $projects->pluck('investor.company_name')->filter()->unique()->take(6); @endphp
    @if ($investorNames->isNotEmpty())
        <section class="border-b border-line bg-canvas-raised">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-9 flex flex-col items-center gap-5">
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Partnerski investitori i graditelji') }}</span>
                <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-3 opacity-60">
                    @foreach ($investorNames as $name)
                        <span class="font-display text-[19px] font-medium text-ink-soft">{{ $name }}</span>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- KAKO FUNKCIONIRA --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28">
        <div class="mx-auto max-w-xl text-center">
            <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Kako funkcionira') }}</span>
            <h2 class="mt-4 font-display text-3xl sm:text-4xl font-medium leading-tight text-ink">{{ __('Od pretrage do useljenja u tri koraka') }}</h2>
            <p class="mt-4 text-base leading-relaxed text-ink-soft">{{ __('Sve informacije dostupne su odmah, online — bez čekanja na odgovor i bez agencijskih posjeta.') }}</p>
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7">
            @foreach ([
                ['01', 'Pretražite zgradu', 'Filtrirajte projekte po lokaciji, broju soba i proračunu i pronađite zgrade koje vam odgovaraju.'],
                ['02', 'Istražite kat po kat', 'Prijeđite mišem preko fasade zgrade i odmah vidite koje su jedinice slobodne na svakom katu.'],
                ['03', 'Otvorite tlocrt', 'Svaka jedinica ima tlocrt s označenim prostorijama — vidite raspored prije nego dogovorite razgled.'],
            ] as [$num, $title, $text])
                <div class="rounded-[20px] border border-line bg-panel p-8 transition duration-300 hover:-translate-y-1.5 hover:border-white/30">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full border border-line-strong">
                        <span class="font-display text-base font-semibold text-ink">{{ $num }}</span>
                    </div>
                    <h3 class="mt-5 font-display text-xl font-semibold text-ink">{{ __($title) }}</h3>
                    <p class="mt-2.5 text-sm leading-relaxed text-ink-soft">{{ __($text) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- DIFERENCIJATOR: tlocrt po katovima --}}
    <section class="border-y border-line bg-canvas-raised">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28 grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20 items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Jedinstveno na Tabiju') }}</span>
                <h2 class="mt-4 font-display text-3xl sm:text-4xl font-medium leading-tight text-ink">{{ __('Vidite cijelu zgradu, prije nego kročite unutra') }}</h2>
                <p class="mt-5 max-w-md text-base leading-relaxed text-ink-soft">{{ __('Svaka zgrada ima interaktivni prikaz kata — dovoljan je jedan pogled da znate što je slobodno, a što već ima kupca.') }}</p>

                <ul class="mt-8 space-y-5">
                    @foreach ([
                        ['Interaktivni tlocrt svake jedinice', 'Sobe, kvadratura i raspored — sve na dodir miša.'],
                        ['Status u stvarnom vremenu', 'Dostupno, rezervirano ili prodano — uvijek ažurno.'],
                        ['Filtriranje po katu, sobnosti i cijeni', 'Suzite izbor u par klikova, bez telefonskih upita.'],
                    ] as [$title, $text])
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full border border-line-strong">
                                <svg viewBox="0 0 20 20" fill="none" class="h-3 w-3"><path d="M4 10l4 4 8-9" stroke="#F3F1EC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ __($title) }}</p>
                                <p class="mt-0.5 text-[13px] text-ink-soft">{{ __($text) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('public.projects.index') }}" wire:navigate class="mt-9 inline-flex items-center gap-1.5 text-sm font-bold text-ink transition-all hover:gap-2.5">
                    {{ __('Istražite projekte') }} <span aria-hidden="true">→</span>
                </a>
            </div>

            {{-- Ilustracija principa (statična) --}}
            <div class="relative rounded-[22px] border border-line-strong bg-canvas p-6 shadow-2xl shadow-black/60" aria-hidden="true">
                <p class="mb-4 border-b border-line pb-3.5 text-xs text-ink-faint">{{ __('Primjer zgrade · aktivan kat: Kat 4') }}</p>
                <div class="space-y-1">
                    @foreach ([
                        ['Kat 6', 'a', 'r', 's'],
                        ['Kat 5', 's', 's', 'a'],
                        ['Kat 4', 'a', 'a', 'a', 's'],
                        ['Kat 3', 's', 'r', 'a'],
                        ['Kat 2', 's', 's', 's'],
                        ['Kat 1', 'a', 'a'],
                    ] as $row)
                        @php $label = array_shift($row); $active = $label === 'Kat 4'; @endphp
                        <div @class(['relative flex items-center gap-3 rounded-[10px] px-3 py-2.5', 'border-l-[3px] border-ink bg-white/[0.09] pl-[9px]' => $active])>
                            <span @class(['w-11 text-[11px]', 'font-bold text-ink' => $active, 'text-ink-faint' => ! $active])>{{ $label }}</span>
                            <div class="flex flex-1 gap-1.5">
                                @foreach ($row as $cell)
                                    <span @class(['h-5 flex-1 rounded-[5px]', 'bg-white/80' => $cell === 'a', 'border border-line-strong' => $cell === 'r', 'bg-white/[0.08]' => $cell === 's'])></span>
                                @endforeach
                            </div>
                            @if ($active)
                                <span class="absolute right-2 top-1/2 hidden sm:block -translate-y-[170%] anim-float whitespace-nowrap rounded-[10px] bg-ink px-3 py-2 text-xs font-bold text-canvas shadow-xl shadow-black/50">{{ __('Kat 4 · 3/4 stana dostupno') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex gap-5 border-t border-line pt-4 text-xs text-ink-soft">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-[3px] bg-white/80"></span>{{ __('Dostupno') }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-[3px] border border-line-strong"></span>{{ __('Rezervirano') }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-[3px] bg-white/10"></span>{{ __('Prodano') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ISTAKNUTI PROJEKTI --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Izbor urednika') }}</span>
                <h2 class="mt-3 font-display text-3xl font-medium text-ink">{{ __('Istaknuti projekti') }}</h2>
            </div>
            <a href="{{ route('public.projects.index') }}" wire:navigate class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-ink-soft transition-all hover:gap-2.5 hover:text-ink">
                {{ __('Svi projekti') }} <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($projects->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Trenutno nema objavljenih projekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- ZA INVESTITORE --}}
    <section class="relative overflow-hidden border-y border-line bg-gradient-to-br from-[#161616] via-[#101010] to-canvas">
        <div class="pointer-events-none absolute -right-28 -top-36 h-[420px] w-[420px] rounded-full blur-[10px]" style="background: radial-gradient(circle, rgba(255,255,255,.06), transparent 70%);"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28 grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-14 lg:gap-16 items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Za investitore i graditelje') }}</span>
                <h2 class="mt-4 font-display text-3xl sm:text-4xl font-medium leading-tight text-ink">{{ __('Predstavite svoj projekt onako kako zaslužuje') }}</h2>
                <p class="mt-5 max-w-lg text-base leading-relaxed text-ink-soft">{{ __('Digitalizirajte prodaju stanova — od interaktivnog tlocrta cijele zgrade do statusa u stvarnom vremenu za vaš prodajni tim.') }}</p>

                <ul class="mt-7 space-y-3">
                    @foreach (['Kupci dolaze pripremljeni, uz manje upita', 'Vaš tim ažurira status stanova uživo', 'Tlocrti, zone i prostorije uređuju se izravno u panelu'] as $point)
                        <li class="flex items-center gap-2.5 text-sm text-ink-soft">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4 shrink-0"><path d="M4 10l4 4 8-9" stroke="#F3F1EC" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __($point) }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex flex-wrap gap-3.5">
                    <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center rounded-full bg-ink px-7 py-3.5 text-sm font-bold text-canvas transition hover:brightness-90">{{ __('Prijava za investitore') }}</a>
                </div>
            </div>

            <div class="rounded-[20px] border border-line-strong bg-panel-strong p-6 shadow-2xl shadow-black/60" aria-hidden="true">
                <div class="flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-ink">{{ __('Pregled prodaje') }}</span>
                    <span class="flex items-center gap-1.5 text-[10px] font-bold tracking-widest text-ink-soft"><span class="h-1.5 w-1.5 rounded-full bg-ink anim-pulse-dot"></span>{{ __('UŽIVO') }}</span>
                </div>
                <div class="mt-5 grid grid-cols-3 gap-2.5">
                    <div class="rounded-xl border border-line-strong bg-white/[0.08] p-3.5 text-center"><p class="font-display text-[22px] font-semibold text-ink">{{ $stats['available'] }}</p><p class="mt-0.5 text-[11px] text-ink-faint">{{ __('Dostupno') }}</p></div>
                    <div class="rounded-xl border border-dashed border-line-strong p-3.5 text-center"><p class="font-display text-[22px] font-semibold text-ink-soft">{{ max($stats['units'] - $stats['available'], 0) }}</p><p class="mt-0.5 text-[11px] text-ink-faint">{{ __('Rezervirano / prodano') }}</p></div>
                    <div class="rounded-xl border border-line bg-white/[0.02] p-3.5 text-center"><p class="font-display text-[22px] font-semibold text-ink-faint">{{ $stats['units'] }}</p><p class="mt-0.5 text-[11px] text-ink-faint">{{ __('Ukupno') }}</p></div>
                </div>
                <div class="mt-6 space-y-2.5">
                    @foreach ([['Kat 6', 82, 'bg-ink'], ['Kat 5', 61, 'bg-ink'], ['Kat 4', 45, 'bg-white/50'], ['Kat 3', 28, 'bg-white/30']] as [$floor, $pct, $tone])
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 text-[11px] text-ink-faint">{{ $floor }}</span>
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-white/[0.08]"><span class="block h-full rounded-full {{ $tone }}" style="width: {{ $pct }}%"></span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ISTAKNUTE JEDINICE --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Spremno za useljenje') }}</span>
                <h2 class="mt-3 font-display text-3xl font-medium text-ink">{{ __('Istaknute jedinice') }}</h2>
            </div>
            <a href="{{ route('public.units.index') }}" wire:navigate class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-ink-soft transition-all hover:gap-2.5 hover:text-ink">
                {{ __('Sve jedinice') }} <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($units->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Trenutno nema objavljenih jedinica.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units as $unit)
                    <x-unit-card :unit="$unit" />
                @endforeach
            </div>
        @endif
    </section>
</div>

@once
    @push('scripts')
        <script>
            function heroSearch() {
                return {
                    projectId: '',
                    roomCount: '',

                    submit() {
                        const params = new URLSearchParams();
                        if (this.projectId) params.set('projekt', this.projectId);
                        if (this.roomCount) params.set('sobe', this.roomCount);

                        const query = params.toString();
                        window.Livewire.navigate('{{ route('public.units.index') }}' + (query ? '?' + query : ''));
                    },
                };
            }
        </script>
    @endpush
@endonce
