<div>
    <p class="text-sm text-ink-soft mb-1">
        <a href="{{ route($routePrefix.'units.show', $unit) }}" wire:navigate class="hover:underline">{{ __('Jedinica') }} {{ $unit->code }}</a>
    </p>
    <h2 class="font-extrabold text-2xl tracking-tight text-navy-900 mb-6">
        {{ $room ? __('Uredi prostoriju') : __('Nova prostorija') }}
    </h2>

    <form wire:submit="save" class="bg-white shadow-card rounded-xl border border-line p-6 space-y-6 max-w-md">
        <div>
            <x-input-label for="name" :value="__('Naziv prostorije')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" placeholder="npr. Kuhinja" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="area_m2" :value="__('Površina (m²)')" />
            <x-text-input wire:model="area_m2" id="area_m2" class="block mt-1 w-full" type="number" step="0.01" min="0" />
            <x-input-error :messages="$errors->get('area_m2')" class="mt-2" />
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ route($routePrefix.'units.show', $unit) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
