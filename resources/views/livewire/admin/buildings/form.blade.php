<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('admin.projects.show', $project) }}" wire:navigate class="hover:underline">{{ $project->name }}</a>
    </p>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-6">
        {{ $building ? __('Uredi objekat') : __('Novi objekat') }}
    </h2>

    <form wire:submit="save" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6 max-w-2xl">
        <div>
            <x-input-label for="name" :value="__('Naziv objekta')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" placeholder="npr. Zgrada A" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="type" :value="__('Tip objekta')" />
            <select wire:model="type" id="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @foreach (\App\Enums\BuildingType::cases() as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Adresa')" />
            <x-text-input wire:model="address" id="address" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="facade_image" :value="__('Slika fasade')" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Ova slika koristi se za interaktivni prikaz katova na javnoj stranici projekta.') }}</p>
            <input wire:model="facade_image" id="facade_image" type="file" accept="image/*" class="block mt-2 w-full text-sm text-gray-600 dark:text-gray-400" />
            <x-input-error :messages="$errors->get('facade_image')" class="mt-2" />

            @if ($facade_image)
                <img src="{{ $facade_image->temporaryUrl() }}" class="mt-2 max-h-48 rounded" alt="">
            @elseif ($building?->facade_image)
                <img src="{{ Storage::disk('public')->url($building->facade_image) }}" class="mt-2 max-h-48 rounded" alt="">
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ $building ? route('admin.buildings.show', $building) : route('admin.projects.show', $project) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
