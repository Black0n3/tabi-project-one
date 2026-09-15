<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nadzorna ploča') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <p>{{ __('Dobrodošao/la, :name.', ['name' => auth()->user()->name]) }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Ovo je globalni admin panel. Ovdje ćeš moći upravljati investitorima i, u njihovo ime, projektima, objektima i jedinicama.') }}
            </p>
        </div>
    </div>
</x-admin-layout>
