<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-navy-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('investitor.dashboard') }}" wire:navigate class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="text-xl font-extrabold tracking-tight uppercase">Tabi</span>
                        <span class="rounded bg-white/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.18em] text-brand-sky">Investitor</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('investitor.dashboard') }}" wire:navigate
                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold {{ request()->routeIs('investitor.*') ? 'border-brand-sky text-white' : 'border-transparent text-white/60 hover:text-white hover:border-white/30' }}">
                        {{ __('Moji projekti') }}
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-2 text-sm font-semibold text-white/60 transition hover:text-white">{{ __('Javna stranica') }} ↗</a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-white/15 text-sm leading-4 font-semibold rounded-md text-white/90 hover:bg-white/10 focus:outline-none transition ease-in-out duration-150">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand text-[11px] font-extrabold">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Odjava') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('investitor.dashboard') }}" wire:navigate
                class="block ps-3 pe-4 py-2 border-l-4 text-base font-semibold {{ request()->routeIs('investitor.*') ? 'border-brand-sky text-white bg-white/10' : 'border-transparent text-white/60' }}">
                {{ __('Moji projekti') }}
            </a>
        </div>

        <div class="pt-4 pb-3 border-t border-white/10">
            <div class="px-4">
                <div class="font-semibold text-base text-white">{{ auth()->user()->name }}</div>
                <div class="text-sm text-white/50">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile') }}" wire:navigate class="block ps-3 pe-4 py-2 text-base font-semibold text-white/70">{{ __('Profil') }}</a>
                <button wire:click="logout" class="block w-full ps-3 pe-4 py-2 text-start text-base font-semibold text-white/70">{{ __('Odjava') }}</button>
            </div>
        </div>
    </div>
</nav>
