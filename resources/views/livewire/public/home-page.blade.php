<div>
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 text-white">
        @if ($heroImage)
            <img src="{{ $heroImage }}" class="absolute inset-0 w-full h-full object-cover" alt="" aria-hidden="true">
            <div class="absolute inset-0 bg-emerald-950/80"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-emerald-950/75 to-emerald-900/70"></div>
        @endif

        <div class="absolute inset-0 opacity-[0.07]" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 1px, transparent 26px), repeating-linear-gradient(-45deg, white 0, white 1px, transparent 1px, transparent 26px);"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 15% 15%, rgba(255,255,255,0.10), transparent 45%), radial-gradient(circle at 85% 85%, rgba(255,255,255,0.06), transparent 40%);"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-24 sm:pt-32 sm:pb-32">
            <p class="text-sm font-medium tracking-widest text-emerald-300 uppercase mb-5">{{ __('Stambeni projekti · na jednom mjestu') }}</p>
            <h1 class="font-display text-4xl sm:text-6xl font-semibold leading-[1.08] max-w-2xl text-white">
                {{ __('Pronađi svoj novi dom') }}
            </h1>
            <p class="mt-6 max-w-xl text-lg text-emerald-100/80 leading-relaxed">
                {{ __('Istraži stambene projekte, prošetaj kroz zgradu kat po kat i pronađi jedinicu koja ti odgovara.') }}
            </p>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('public.units.index') }}" wire:navigate class="inline-flex items-center px-6 py-3 rounded-full bg-white text-emerald-950 font-medium hover:bg-emerald-50 transition shadow-lg shadow-emerald-950/30">
                    {{ __('Pregledaj jedinice') }}
                </a>
                <a href="{{ route('public.projects.index') }}" wire:navigate class="inline-flex items-center px-6 py-3 rounded-full border border-white/30 text-white font-medium hover:bg-white/10 transition">
                    {{ __('Svi projekti') }}
                </a>
            </div>

            <dl class="mt-16 sm:mt-20 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-8 max-w-2xl">
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
