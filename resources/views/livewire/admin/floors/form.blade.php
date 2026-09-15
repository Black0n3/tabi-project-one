<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('admin.buildings.show', $building) }}" wire:navigate class="hover:underline">{{ $building->name }}</a>
    </p>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-6">
        {{ $floor ? __('Uredi kat') : __('Novi kat') }}
    </h2>

    <form wire:submit="save" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6 max-w-2xl">
        <div>
            <x-input-label for="label" :value="__('Naziv kata')" />
            <x-text-input wire:model="label" id="label" class="block mt-1 w-full" type="text" placeholder="npr. Prizemlje, 1. kat" required />
            <x-input-error :messages="$errors->get('label')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="order" :value="__('Redoslijed (0 = najniže)')" />
            <x-text-input wire:model="order" id="order" class="block mt-1 w-full" type="number" min="0" required />
            <x-input-error :messages="$errors->get('order')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="floor_plan_image" :value="__('Tlocrt kata')" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Slika na kojoj će se kasnije označiti pozicije jedinica na ovom katu.') }}</p>
            <input wire:model="floor_plan_image" id="floor_plan_image" type="file" accept="image/*" class="block mt-2 w-full text-sm text-gray-600 dark:text-gray-400" />
            <x-input-error :messages="$errors->get('floor_plan_image')" class="mt-2" />

            @if ($floor_plan_image)
                <img src="{{ $floor_plan_image->temporaryUrl() }}" class="mt-2 max-h-48 rounded" alt="">
            @elseif ($floor?->floor_plan_image)
                <img src="{{ Storage::disk('public')->url($floor->floor_plan_image) }}" class="mt-2 max-h-48 rounded" alt="">
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ route('admin.buildings.show', $building) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
