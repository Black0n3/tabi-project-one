<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tabi') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800">
            <a href="/" wire:navigate class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand text-white">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6"><path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="flex flex-col leading-none">
                    <span class="text-2xl font-extrabold tracking-tight text-white uppercase">Tabi</span>
                    <span class="mt-1 text-[9px] font-bold tracking-[0.28em] text-brand-sky">NOVOGRADNJA</span>
                </span>
            </a>

            <div class="w-full sm:max-w-md mt-8 px-7 py-7 bg-white shadow-lift overflow-hidden rounded-xl">
                {{ $slot }}
            </div>

            <a href="{{ route('home') }}" wire:navigate class="mt-6 text-sm font-semibold text-white/70 transition hover:text-white">← {{ __('Natrag na stranicu') }}</a>
        </div>
    </body>
</html>
