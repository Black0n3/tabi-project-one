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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <header class="border-b border-gray-100 dark:border-gray-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" wire:navigate class="font-semibold text-lg">
                    Tabi
                </a>

                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('public.projects.index') }}" wire:navigate class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('Projekti') }}
                    </a>
                    <a href="{{ route('public.units.index') }}" wire:navigate class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('Jedinice') }}
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            {{ __('Moj panel') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            {{ __('Prijava') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-100 dark:border-gray-800 mt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ now()->year }} Tabi.
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
