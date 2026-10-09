<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0A1E45">

        @php
            $pageTitle = (isset($title) ? $title.' — ' : '').config('app.name', 'Tabi');
            $pageDescription = $description ?? 'Pregledaj stambene projekte i dostupne jedinice naših investitora.';
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ url()->current() }}">

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if (isset($image))
            <meta property="og:image" content="{{ $image }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-ink">
        {{-- Top utility bar --}}
        <div class="hidden md:block bg-navy-900 text-white/85">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-10 flex items-center justify-between text-xs font-medium">
                <div class="flex items-center gap-7">
                    <span class="inline-flex items-center gap-2">
                        <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.5"/></svg>
                        {{ __('Novogradnja · Osijek i Slavonija') }}
                    </span>
                    <span class="hidden lg:inline-flex items-center gap-2">
                        <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><rect x="3" y="3" width="14" height="14" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 10h14M10 3v14" stroke="currentColor" stroke-width="1.5"/></svg>
                        {{ __('Interaktivni tlocrti svakog stana') }}
                    </span>
                    <span class="hidden lg:inline-flex items-center gap-2">
                        <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l2.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        {{ __('Status u stvarnom vremenu') }}
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="transition hover:text-white">{{ __('Moj panel') }}</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="transition hover:text-white">{{ __('Prijava za investitore') }}</a>
                    @endauth
                </div>
            </div>
        </div>

        <header
            x-data="{ scrolled: false, open: false }"
            x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
            :class="scrolled ? 'shadow-card' : ''"
            class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-line transition-shadow"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 group">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand text-white transition-transform group-hover:scale-105">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5">
                            <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="flex flex-col leading-none">
                        <span class="text-[22px] font-extrabold tracking-tight text-navy-900">TABI</span>
                        <span class="mt-1 text-[9px] font-bold tracking-[0.28em] text-brand">NOVOGRADNJA</span>
                    </span>
                </a>

                @php
                    $nav = [
                        ['Početna', route('home'), request()->routeIs('home')],
                        ['Projekti', route('public.projects.index'), request()->routeIs('public.projects.*', 'public.buildings.*')],
                        ['Jedinice', route('public.units.index'), request()->routeIs('public.units.*')],
                    ];
                @endphp

                <nav class="hidden md:flex items-center gap-9 text-[15px]">
                    @foreach ($nav as [$label, $url, $active])
                        <a href="{{ $url }}" wire:navigate @class(['relative py-6 font-semibold transition', 'text-brand' => $active, 'text-ink hover:text-brand' => ! $active])>
                            {{ __($label) }}
                            @if ($active)<span class="absolute inset-x-0 bottom-0 h-0.5 bg-brand"></span>@endif
                        </a>
                    @endforeach
                    <a href="{{ route('home') }}#kako-radi" class="py-6 font-semibold text-ink transition hover:text-brand">{{ __('Kako radi') }}</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="hidden sm:inline-flex items-center rounded-md bg-brand px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-dark">{{ __('Moj panel') }}</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="hidden sm:inline-flex items-center rounded-md bg-brand px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-dark">{{ __('Prijava') }}</a>
                    @endauth

                    <button type="button" @click="open = !open" class="md:hidden flex h-10 w-10 items-center justify-center rounded-md border border-line-strong text-ink" :aria-expanded="open" aria-label="{{ __('Izbornik') }}">
                        <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-cloak x-transition class="md:hidden border-t border-line bg-white">
                <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col text-[15px] font-semibold">
                    @foreach ($nav as [$label, $url, $active])
                        <a href="{{ $url }}" wire:navigate @class(['py-3', 'text-brand' => $active, 'text-ink' => ! $active])>{{ __($label) }}</a>
                    @endforeach
                    <a href="{{ route('home') }}#kako-radi" class="py-3 text-ink">{{ __('Kako radi') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="mt-2 rounded-md bg-brand py-3 text-center font-bold text-white">{{ __('Moj panel') }}</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="mt-2 rounded-md bg-brand py-3 text-center font-bold text-white">{{ __('Prijava') }}</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="bg-navy-950 text-white/70 mt-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="flex flex-col leading-none">
                            <span class="text-[22px] font-extrabold tracking-tight text-white">TABI</span>
                            <span class="mt-1 text-[9px] font-bold tracking-[0.28em] text-brand-sky">NOVOGRADNJA</span>
                        </span>
                    </div>
                    <p class="mt-5 text-sm max-w-sm leading-relaxed">
                        {{ __('Portal koji povezuje investitore i kupce — pregledaj stambene projekte, istraži zgrade kat po kat i pronađi jedinicu koja ti odgovara.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-bold text-white mb-4">{{ __('Pregled') }}</p>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" wire:navigate class="transition hover:text-white">{{ __('Početna') }}</a></li>
                        <li><a href="{{ route('public.projects.index') }}" wire:navigate class="transition hover:text-white">{{ __('Svi projekti') }}</a></li>
                        <li><a href="{{ route('public.units.index') }}" wire:navigate class="transition hover:text-white">{{ __('Sve jedinice') }}</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-bold text-white mb-4">{{ __('Nalog') }}</p>
                    <ul class="space-y-3 text-sm">
                        @auth
                            <li><a href="{{ route('dashboard') }}" wire:navigate class="transition hover:text-white">{{ __('Moj panel') }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}" wire:navigate class="transition hover:text-white">{{ __('Prijava za investitore') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-white/50">
                    &copy; {{ now()->year }} Tabi. {{ __('Sva prava pridržana.') }}
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
