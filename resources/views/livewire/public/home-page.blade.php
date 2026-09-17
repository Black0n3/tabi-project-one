<div>
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-emerald-950 to-emerald-900 text-white">
        @if ($heroImage)
            <div class="absolute inset-0 overflow-hidden">
                <img src="{{ $heroImage }}" class="w-full h-full object-cover scale-110 blur-md saturate-[1.15]" alt="" aria-hidden="true">
            </div>
            <div class="absolute inset-0 bg-slate-950/70"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/90 via-emerald-950/75 to-slate-950/70"></div>
        @endif

        <div class="absolute inset-0 opacity-[0.06]" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 1px, transparent 26px), repeating-linear-gradient(-45deg, white 0, white 1px, transparent 1px, transparent 26px);"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 15% 15%, rgba(255,255,255,0.10), transparent 45%), radial-gradient(circle at 85% 85%, rgba(255,255,255,0.06), transparent 40%);"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 sm:pt-32 sm:pb-20">
            <p class="text-sm font-medium tracking-widest text-emerald-300 uppercase mb-5">{{ __('Novogradnja · Osijek i Slavonija') }}</p>
            <h1 class="font-display text-4xl sm:text-6xl font-semibold leading-[1.08] max-w-2xl text-white">
                {{ __('Pronađi svoj novi dom') }}
            </h1>
            <p class="mt-6 max-w-xl text-lg text-emerald-100/80 leading-relaxed">
                {{ __('Pretraži zgrade kat po kat, otvori tlocrt svakog stana i pronađi jedinicu koja ti odgovara.') }}
            </p>

            {{-- Quick search: glassmorphism traka, submit gradi query string i navigira preko Livewire.navigate --}}
            <form
                x-data="heroSearch()"
                @submit.prevent="submit()"
                class="mt-10 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl shadow-emerald-950/40 p-3 sm:p-3.5 max-w-3xl"
            >
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2.5">
                    <select x-model="projectId" class="rounded-xl border-0 bg-white/95 text-stone-900 text-sm shadow-sm focus:ring-2 focus:ring-emerald-400 py-3">
                        <option value="">{{ __('Svi projekti') }}</option>
                        @foreach ($searchProjects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>

                    <select x-model="roomCount" class="rounded-xl border-0 bg-white/95 text-stone-900 text-sm shadow-sm focus:ring-2 focus:ring-emerald-400 py-3">
                        <option value="">{{ __('Sobnost') }}</option>
                        <option value="1">{{ __('1-sobni') }}</option>
                        <option value="2">{{ __('2-sobni') }}</option>
                        <option value="3">{{ __('3-sobni') }}</option>
                        <option value="4+">{{ __('4+ sobni') }}</option>
                    </select>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-semibold text-sm px-5 py-3 transition shadow-lg shadow-emerald-950/30">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                        {{ __('Pretraži stanove') }}
                    </button>
                </div>
            </form>

            <a href="{{ route('public.projects.index') }}" wire:navigate class="mt-4 inline-flex items-center text-sm font-medium text-emerald-200/80 hover:text-white transition">
                {{ __('ili pregledaj sve projekte') }} →
            </a>

            <dl class="mt-14 sm:mt-16 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-8 max-w-2xl">
                <div>
                    <dt class="font-display text-3xl sm:text-4xl font-semibold">{{ $stats['projects'] }}</dt>
                    <dd class="text-sm text-emerald-200/70 mt-1">{{ __('projekata') }}</dd>
                </div>
                <div>
                    <dt class="font-display text-3xl sm:text-4xl font-semibold">{{ $stats['units'] }}</dt>
                    <dd class="text-sm text-emerald-200/70 mt-1">{{ __('jedinica') }}</dd>
                </div>
                <div>
                    <dt class="font-display text-3xl sm:text-4xl font-semibold">{{ $stats['available'] }}</dt>
                    <dd class="text-sm text-emerald-200/70 mt-1">{{ __('dostupno odmah') }}</dd>
                </div>
                <div>
                    <dt class="font-display text-3xl sm:text-4xl font-semibold">{{ $stats['locations'] }}</dt>
                    <dd class="text-sm text-emerald-200/70 mt-1">{{ __('lokacija') }}</dd>
                </div>
            </dl>

            @if ($projects->isNotEmpty())
                <div class="mt-14 sm:mt-16">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-300/80 mb-4">{{ __('Izbor urednika') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($projects->take(3) as $index => $project)
                            <a
                                href="{{ route('public.projects.show', $project) }}"
                                wire:navigate
                                class="group relative overflow-hidden rounded-2xl border border-white/15 bg-white/5 hover:bg-white/10 transition-colors {{ $index === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }}"
                            >
                                <div class="{{ $index === 0 ? 'aspect-[16/10]' : 'aspect-[16/9] sm:aspect-auto sm:h-full' }} relative">
                                    @if ($project->cover_image)
                                        <img src="{{ Storage::disk('public')->url($project->cover_image) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $project->name }}">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent"></div>
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 to-slate-900"></div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                                    @endif

                                    <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5">
                                        <h3 class="font-display font-semibold text-white {{ $index === 0 ? 'text-xl sm:text-2xl' : 'text-base' }}">{{ $project->name }}</h3>
                                        <p class="mt-1 text-sm text-emerald-100/80">
                                            {{ trans_choice('{1}:count zgrada|[2,4]:count zgrade|[5,*]:count zgrada', $project->buildings_count, ['count' => $project->buildings_count]) }}
                                            &middot; {{ __('interaktivni tlocrti') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <svg class="relative block w-full h-10 sm:h-14 text-stone-50 dark:text-stone-950" viewBox="0 0 1440 48" preserveAspectRatio="none" fill="currentColor">
            <path d="M0 48 C 360 0, 1080 0, 1440 48 Z"></path>
        </svg>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 sm:gap-8">
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-900/10 dark:bg-emerald-400/10 text-emerald-800 dark:text-emerald-400">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 21V9l8-6 8 6v12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 21v-6h6v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="mt-4 font-display text-lg font-semibold">{{ __('Istraži kat po kat') }}</h3>
                <p class="mt-2 text-sm text-stone-500 dark:text-stone-400 leading-relaxed">
                    {{ __('Prijeđi mišem preko fasade zgrade i odmah vidi koje su jedinice dostupne na svakom katu.') }}
                </p>
            </div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-900/10 dark:bg-emerald-400/10 text-emerald-800 dark:text-emerald-400">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><rect x="4" y="4" width="16" height="16" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M4 12h16M12 4v16" stroke="currentColor" stroke-width="1.5"/></svg>
                </div>
                <h3 class="mt-4 font-display text-lg font-semibold">{{ __('Detaljni tlocrti') }}</h3>
                <p class="mt-2 text-sm text-stone-500 dark:text-stone-400 leading-relaxed">
                    {{ __('Svaka jedinica ima tlocrt s označenim prostorijama — vidi raspored prije nego dogovoriš razgled.') }}
                </p>
            </div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-900/10 dark:bg-emerald-400/10 text-emerald-800 dark:text-emerald-400">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 8v4l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/></svg>
                </div>
                <h3 class="mt-4 font-display text-lg font-semibold">{{ __('Uvijek ažurno') }}</h3>
                <p class="mt-2 text-sm text-stone-500 dark:text-stone-400 leading-relaxed">
                    {{ __('Status svake jedinice — dostupno, rezervirano ili prodano — ažuriraju investitori u stvarnom vremenu.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Izbor urednika') }}</p>
                <h2 class="font-display text-2xl sm:text-3xl font-semibold">{{ __('Istaknuti projekti') }}</h2>
            </div>
            <a href="{{ route('public.projects.index') }}" wire:navigate class="hidden sm:inline-flex items-center text-sm font-medium text-emerald-800 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">
                {{ __('Svi projekti') }} →
            </a>
        </div>

        @if ($projects->isEmpty())
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Trenutno nema objavljenih projekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Spremno za useljenje') }}</p>
                <h2 class="font-display text-2xl sm:text-3xl font-semibold">{{ __('Istaknute jedinice') }}</h2>
            </div>
            <a href="{{ route('public.units.index') }}" wire:navigate class="hidden sm:inline-flex items-center text-sm font-medium text-emerald-800 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">
                {{ __('Sve jedinice') }} →
            </a>
        </div>

        @if ($units->isEmpty())
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Trenutno nema objavljenih jedinica.') }}</p>
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
