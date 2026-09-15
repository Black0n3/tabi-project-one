<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|fraunces:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-stone-50 dark:bg-stone-950 text-stone-900 dark:text-stone-100">
        <header
            x-data="{ scrolled: false }"
            x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
            :class="scrolled ? 'border-stone-200 dark:border-stone-800 shadow-sm' : 'border-transparent'"
            class="sticky top-0 z-40 border-b bg-stone-50/85 dark:bg-stone-950/85 backdrop-blur-md transition-colors duration-300"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 group">
                    <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-md bg-emerald-900 dark:bg-emerald-800 text-white transition-transform group-hover:scale-105">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 sm:h-5 sm:w-5">
                            <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="font-display font-semibold text-lg sm:text-xl tracking-tight">Tabi</span>
                </a>

                <nav class="flex items-center gap-1 sm:gap-2 text-sm">
                    <a href="{{ route('public.projects.index') }}" wire:navigate class="hidden sm:inline-block px-3 py-2 rounded-md text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 hover:bg-stone-900/5 dark:hover:bg-white/5 transition">
                        {{ __('Projekti') }}
                    </a>
                    <a href="{{ route('public.units.index') }}" wire:navigate class="hidden sm:inline-block px-3 py-2 rounded-md text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 hover:bg-stone-900/5 dark:hover:bg-white/5 transition">
                        {{ __('Jedinice') }}
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="ms-1 sm:ms-2 inline-flex items-center px-4 py-2 rounded-md bg-emerald-900 dark:bg-emerald-800 text-white text-sm font-medium hover:bg-emerald-800 dark:hover:bg-emerald-700 transition">
                            {{ __('Moj panel') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="ms-1 sm:ms-2 inline-flex items-center px-4 py-2 rounded-md bg-emerald-900 dark:bg-emerald-800 text-white text-sm font-medium hover:bg-emerald-800 dark:hover:bg-emerald-700 transition">
                            {{ __('Prijava') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200 dark:border-stone-800 bg-stone-100/60 dark:bg-stone-900/40 mt-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-900 dark:bg-emerald-800 text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4">
                                <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="font-display font-semibold text-lg">Tabi</span>
                    </div>
                    <p class="mt-4 text-sm text-stone-500 dark:text-stone-400 max-w-sm leading-relaxed">
                        {{ __('Portal koji povezuje investitore i kupce — pregledaj stambene projekte, istraži zgrade kat po kat i pronađi jedinicu koja ti odgovara.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 dark:text-stone-500 mb-3">{{ __('Pregled') }}</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.projects.index') }}" wire:navigate class="text-stone-600 dark:text-stone-400 hover:text-emerald-800 dark:hover:text-emerald-400 transition">{{ __('Svi projekti') }}</a></li>
                        <li><a href="{{ route('public.units.index') }}" wire:navigate class="text-stone-600 dark:text-stone-400 hover:text-emerald-800 dark:hover:text-emerald-400 transition">{{ __('Sve jedinice') }}</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 dark:text-stone-500 mb-3">{{ __('Nalog') }}</p>
                    <ul class="space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('dashboard') }}" wire:navigate class="text-stone-600 dark:text-stone-400 hover:text-emerald-800 dark:hover:text-emerald-400 transition">{{ __('Moj panel') }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}" wire:navigate class="text-stone-600 dark:text-stone-400 hover:text-emerald-800 dark:hover:text-emerald-400 transition">{{ __('Prijava') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t border-stone-200 dark:border-stone-800">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-stone-400 dark:text-stone-500">
                    &copy; {{ now()->year }} Tabi.
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
