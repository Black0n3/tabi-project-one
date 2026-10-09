<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="color-scheme: dark;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0A0A0A">

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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|fraunces:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-canvas text-ink">
        <header
            x-data="{ scrolled: false, open: false }"
            x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
            :class="scrolled ? 'border-line' : 'border-transparent'"
            class="sticky top-0 z-40 border-b bg-canvas/80 backdrop-blur-md transition-colors duration-300"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 group">
                    <span class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-canvas-raised border border-line-strong transition-transform group-hover:scale-105">
                        <span class="font-display font-semibold text-[17px] text-ink">T</span>
                    </span>
                    <span class="flex flex-col leading-none">
                        <span class="font-display font-semibold text-xl tracking-tight">Tabi</span>
                        <span class="mt-1 hidden sm:block text-[9px] font-semibold tracking-[0.2em] text-ink-faint">NOVOGRADNJA</span>
                    </span>
                </a>

                <nav class="flex items-center gap-1 sm:gap-2 text-sm">
                    <a href="{{ route('public.projects.index') }}" wire:navigate @class(['hidden sm:inline-block px-3 py-2 rounded-md transition', 'font-semibold text-ink' => request()->routeIs('public.projects.*'), 'text-ink-soft hover:text-ink' => ! request()->routeIs('public.projects.*')])>
                        {{ __('Projekti') }}
                    </a>
                    <a href="{{ route('public.units.index') }}" wire:navigate @class(['hidden sm:inline-block px-3 py-2 rounded-md transition', 'font-semibold text-ink' => request()->routeIs('public.units.*'), 'text-ink-soft hover:text-ink' => ! request()->routeIs('public.units.*')])>
                        {{ __('Jedinice') }}
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="ms-1 sm:ms-3 inline-flex items-center rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-canvas transition hover:brightness-90">
                            {{ __('Moj panel') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="ms-1 sm:ms-3 inline-flex items-center rounded-full border border-line-strong px-5 py-2.5 text-sm font-semibold text-ink transition hover:bg-white/10">
                            {{ __('Prijava') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-line bg-canvas-raised mt-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-canvas border border-line-strong">
                            <span class="font-display font-semibold text-[15px] text-ink">T</span>
                        </span>
                        <span class="font-display font-semibold text-xl">Tabi</span>
                    </div>
                    <p class="mt-4 text-sm text-ink-soft max-w-sm leading-relaxed">
                        {{ __('Portal koji povezuje investitore i kupce — pregledaj stambene projekte, istraži zgrade kat po kat i pronađi jedinicu koja ti odgovara.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold text-ink mb-4">{{ __('Pregled') }}</p>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('public.projects.index') }}" wire:navigate class="text-ink-soft hover:text-ink transition">{{ __('Svi projekti') }}</a></li>
                        <li><a href="{{ route('public.units.index') }}" wire:navigate class="text-ink-soft hover:text-ink transition">{{ __('Sve jedinice') }}</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-bold text-ink mb-4">{{ __('Nalog') }}</p>
                    <ul class="space-y-3 text-sm">
                        @auth
                            <li><a href="{{ route('dashboard') }}" wire:navigate class="text-ink-soft hover:text-ink transition">{{ __('Moj panel') }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}" wire:navigate class="text-ink-soft hover:text-ink transition">{{ __('Prijava') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t border-line">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-ink-faint">
                    &copy; {{ now()->year }} Tabi. {{ __('Sva prava pridržana.') }}
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
