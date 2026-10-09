<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl tracking-tight text-navy-900 leading-tight">
            {{ __('Nadzorna ploča') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-card rounded-xl border border-line">
        <div class="p-6 text-navy-900">
            <p>{{ __('Dobrodošao/la, :name.', ['name' => auth()->user()->name]) }}</p>
            <p class="mt-2 text-sm text-ink-soft">
                {{ __('Ovo je globalni admin panel. Ovdje ćeš moći upravljati investitorima i, u njihovo ime, projektima, objektima i jedinicama.') }}
            </p>
        </div>
    </div>
</x-admin-layout>
