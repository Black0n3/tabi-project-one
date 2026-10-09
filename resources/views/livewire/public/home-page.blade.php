<div>
    {{-- HERO (animirana zgrada, navy sumrak) --}}
    <section class="relative overflow-hidden bg-navy-950">
        <x-hero-backdrop :image="$heroImage" />

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-36 sm:pt-24 sm:pb-40">
            <div class="max-w-2xl text-white">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-white/80 backdrop-blur-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-sky anim-pulse-dot"></span>
                    {{ __('Novogradnja · Osijek i Slavonija') }}
                </p>

                <h1 class="mt-6 text-5xl sm:text-6xl lg:text-[68px] font-extrabold leading-[1.05] tracking-tight">
                    {{ __('Pretražite zgradu.') }}<br>
                    <span class="text-brand-sky">{{ __('Odaberite svoj kat.') }}</span>
                </h1>

                <p class="mt-6 max-w-lg text-lg leading-relaxed text-white/80">
                    {{ __('Otvorite tlocrt svakog stana, provjerite dostupnost u stvarnom vremenu i pronađite dom izravno kod investitora — bez posrednika.') }}
                </p>

                <div class="mt-9 flex flex-wrap gap-3.5">
                    <a href="{{ route('public.projects.index') }}" wire:navigate class="inline-flex items-center gap-2.5 rounded-md bg-brand px-7 py-3.5 text-[15px] font-bold text-white shadow-lg shadow-black/20 transition hover:bg-brand-dark">
                        {{ __('Istražite projekte') }} <span aria-hidden="true">→</span>
                    </a>
                    <a href="#kako-radi" class="inline-flex items-center gap-2.5 rounded-md border border-white/40 bg-white/5 px-7 py-3.5 text-[15px] font-bold text-white backdrop-blur-sm transition hover:bg-white/15">
                        {{ __('Kako radi') }}
                        <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M7 5.5l7 4.5-7 4.5v-9z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                <dl class="mt-12 flex flex-wrap gap-x-10 gap-y-4">
                    @foreach ([['projects', 'projekata'], ['units', 'jedinica'], ['available', 'dostupno odmah']] as [$key, $label])
                        <div>
                            <dt class="text-3xl font-extrabold tracking-tight">{{ $stats[$key] }}</dt>
                            <dd class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-white/60">{{ __($label) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- KARTICA PRETRAGE (preklapa hero) --}}
    <div class="relative z-10 -mt-20 sm:-mt-24 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <form
            x-data="heroSearch()"
            @submit.prevent="submit()"
            class="rounded-xl border border-line bg-white p-3 shadow-lift sm:p-4"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.2fr_1.2fr_1fr_1fr_auto] gap-3 lg:gap-0 lg:divide-x lg:divide-line">
                <label class="block lg:px-5">
                    <span class="block text-sm font-bold text-navy-900">{{ __('Lokacija') }}</span>
                    <input x-model="location" type="text" placeholder="{{ __('Grad ili kvart') }}" class="mt-1 block w-full border-0 bg-transparent p-0 text-sm text-ink placeholder:text-ink-faint focus:ring-0">
                </label>

                <label class="block lg:px-5">
                    <span class="block text-sm font-bold text-navy-900">{{ __('Projekt') }}</span>
                    <select x-model="projectId" class="mt-1 block w-full border-0 bg-transparent p-0 pe-8 text-sm text-ink focus:ring-0">
                        <option value="">{{ __('Svi projekti') }}</option>
                        @foreach ($searchProjects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block lg:px-5">
                    <span class="block text-sm font-bold text-navy-900">{{ __('Sobnost') }}</span>
                    <select x-model="roomCount" class="mt-1 block w-full border-0 bg-transparent p-0 pe-8 text-sm text-ink focus:ring-0">
                        <option value="">{{ __('Bilo koja') }}</option>
                        <option value="1">{{ __('1-sobni') }}</option>
                        <option value="2">{{ __('2-sobni') }}</option>
                        <option value="3">{{ __('3-sobni') }}</option>
                        <option value="4+">{{ __('4+ sobni') }}</option>
                    </select>
                </label>

                <label class="block lg:px-5">
                    <span class="block text-sm font-bold text-navy-900">{{ __('Cijena do') }}</span>
                    <select x-model="maxPrice" class="mt-1 block w-full border-0 bg-transparent p-0 pe-8 text-sm text-ink focus:ring-0">
                        <option value="">{{ __('Bez limita') }}</option>
                        @foreach ([80000, 100000, 150000, 200000, 300000] as $price)
                            <option value="{{ $price }}">{{ number_format($price, 0, ',', '.') }} €</option>
                        @endforeach
                    </select>
                </label>

                <button type="submit" class="inline-flex items-center justify-center gap-2.5 rounded-md bg-brand px-8 py-3.5 text-sm font-bold text-white transition hover:bg-brand-dark lg:ms-3">
                    {{ __('Pretraži') }}
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- PREDNOSTI --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ([
                ['Istraži kat po kat', 'Prijeđi mišem preko fasade i odmah vidi što je slobodno na svakom katu.', 'M4 21V9l8-6 8 6v12M9 21v-6h6v6'],
                ['Detaljni tlocrti', 'Svaka jedinica ima tlocrt s označenim prostorijama i kvadraturom.', 'M4 4h16v16H4zM4 12h16M12 4v16'],
                ['Uvijek ažurno', 'Investitori u stvarnom vremenu označavaju dostupno, rezervirano i prodano.', 'M12 7v5l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Izravno kod investitora', 'Kontaktirate investitora izravno — bez posrednika i provizije.', 'M12 3l7 3v5c0 4.5-3 8.2-7 10-4-1.8-7-5.5-7-10V6l7-3zM9 12l2 2 4-4'],
            ] as [$title, $text, $path])
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-navy-900 text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6"><path d="{{ $path }}" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-navy-900">{{ __($title) }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ __($text) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ISTAKNUTI PROJEKTI --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
        <div class="mb-9 flex items-end justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ __('Istaknuti projekti') }}</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-navy-900">{{ __('Projekti koje ćete zavoljeti') }}</h2>
            </div>
            <a href="{{ route('public.projects.index') }}" wire:navigate class="hidden sm:inline-flex items-center gap-2 text-sm font-bold text-brand transition-all hover:gap-3">
                {{ __('Svi projekti') }} <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($projects->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Trenutno nema objavljenih projekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects->take(6) as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- BANER ZA INVESTITORE --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        <div class="grid grid-cols-1 overflow-hidden rounded-xl bg-navy-900 shadow-lift lg:grid-cols-2">
            <div class="p-8 sm:p-12">
                <p class="text-sm font-semibold text-brand-sky">{{ __('Ste investitor ili graditelj?') }}</p>
                <h2 class="mt-3 text-3xl sm:text-[34px] font-extrabold leading-tight tracking-tight text-white">{{ __('Predstavite svoj projekt onako kako zaslužuje') }}</h2>
                <p class="mt-4 max-w-md text-[15px] leading-relaxed text-white/75">
                    {{ __('Interaktivni tlocrt cijele zgrade i status svakog stana u stvarnom vremenu — vaš prodajni tim ažurira, kupci vide odmah.') }}
                </p>
                <a href="{{ route('login') }}" wire:navigate class="mt-8 inline-flex items-center gap-2.5 rounded-md bg-white px-6 py-3.5 text-sm font-bold text-navy-900 transition hover:bg-brand-light">
                    {{ __('Prijava za investitore') }} <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="relative flex items-center justify-center bg-gradient-to-br from-navy-800 to-navy-700 p-8 sm:p-12" aria-hidden="true">
                <div class="absolute inset-0 mullions opacity-60"></div>
                <div class="relative w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl shadow-black/30">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-extrabold text-navy-900">{{ __('Zgrada B — pregled katova') }}</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 anim-pulse-dot"></span>{{ __('Uživo') }}</span>
                    </div>
                    <div class="mt-4 space-y-1.5">
                        @foreach ([['Kat 4', 'a', 'a', 'a', 's'], ['Kat 3', 'r', 'a', 's'], ['Kat 2', 's', 's', 's'], ['Kat 1', 'a', 'a']] as $row)
                            @php $label = array_shift($row); $active = $label === 'Kat 4'; @endphp
                            <div @class(['relative flex items-center gap-3 rounded-md px-2.5 py-2', 'bg-brand-light ring-1 ring-brand/30' => $active])>
                                <span @class(['w-10 text-[11px] font-bold', 'text-brand' => $active, 'text-ink-faint' => ! $active])>{{ $label }}</span>
                                <div class="flex flex-1 gap-1.5">
                                    @foreach ($row as $cell)
                                        <span @class(['h-4 flex-1 rounded-sm', 'bg-emerald-500' => $cell === 'a', 'bg-amber-400' => $cell === 'r', 'bg-slate-300' => $cell === 's'])></span>
                                    @endforeach
                                </div>
                                @if ($active)
                                    <span class="absolute -top-3 right-2 anim-float whitespace-nowrap rounded bg-navy-900 px-2 py-1 text-[10px] font-bold text-white shadow-lg">{{ __('3/4 stana dostupno') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex gap-4 border-t border-line pt-3 text-[11px] font-medium text-ink-soft">
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-emerald-500"></span>{{ __('Dostupno') }}</span>
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-amber-400"></span>{{ __('Rezervirano') }}</span>
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-slate-300"></span>{{ __('Prodano') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ISTAKNUTE JEDINICE --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6">
        <div class="mb-9 flex items-end justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ __('Spremno za useljenje') }}</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-navy-900">{{ __('Istaknute jedinice') }}</h2>
            </div>
            <a href="{{ route('public.units.index') }}" wire:navigate class="hidden sm:inline-flex items-center gap-2 text-sm font-bold text-brand transition-all hover:gap-3">
                {{ __('Sve jedinice') }} <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($units->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Trenutno nema objavljenih jedinica.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units->take(4) as $unit)
                    <x-unit-card :unit="$unit" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- KAKO RADI --}}
    <section id="kako-radi" class="mt-20 bg-mist">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ __('Kako radi') }}</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-navy-900">{{ __('Od pretrage do useljenja jednostavno') }}</h2>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ([
                    ['01', 'Pretražite zgradu', 'Filtrirajte projekte po lokaciji, broju soba i proračunu.'],
                    ['02', 'Istražite kat po kat', 'Prijeđite mišem preko fasade i vidite dostupne jedinice po katu.'],
                    ['03', 'Otvorite tlocrt', 'Pregledajte raspored prostorija i kvadraturu prije razgleda.'],
                    ['04', 'Kontaktirajte investitora', 'Izravan kontakt s investitorom — bez posrednika i provizije.'],
                ] as [$num, $title, $text])
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-light text-base font-extrabold text-brand">{{ $num }}</span>
                        <div>
                            <h3 class="font-extrabold text-navy-900">{{ __($title) }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ __($text) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

@once
    @push('scripts')
        <script>
            function heroSearch() {
                return {
                    location: '',
                    projectId: '',
                    roomCount: '',
                    maxPrice: '',

                    submit() {
                        const params = new URLSearchParams();
                        if (this.location.trim()) params.set('lokacija', this.location.trim());
                        if (this.projectId) params.set('projekt', this.projectId);
                        if (this.roomCount) params.set('sobe', this.roomCount);
                        if (this.maxPrice) params.set('max_cijena', this.maxPrice);

                        const query = params.toString();
                        window.Livewire.navigate('{{ route('public.units.index') }}' + (query ? '?' + query : ''));
                    },
                };
            }
        </script>
    @endpush
@endonce
