<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('admin.buildings.show', $building) }}" wire:navigate class="hover:underline">{{ $building->name }}</a>
    </p>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-6">
        {{ $unit ? __('Uredi jedinicu') : __('Nova jedinica') }}
    </h2>

    <form wire:submit="save" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6 max-w-2xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="code" :value="__('Oznaka (npr. A1)')" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" required />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="type" :value="__('Tip')" />
                <select wire:model="type" id="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach (\App\Enums\UnitType::cases() as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="area_m2" :value="__('Površina (m²)')" />
                <x-text-input wire:model="area_m2" id="area_m2" class="block mt-1 w-full" type="number" step="0.01" min="0" required />
                <x-input-error :messages="$errors->get('area_m2')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="price" :value="__('Cijena (€)')" />
                <x-text-input wire:model="price" id="price" class="block mt-1 w-full" type="number" step="0.01" min="0" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select wire:model="status" id="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach (\App\Enums\UnitStatus::cases() as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="floor_id" :value="__('Kat')" />
            <select wire:model="floor_id" id="floor_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('— bez kata (npr. kuća) —') }}</option>
                @foreach ($floors as $floorOption)
                    <option value="{{ $floorOption->id }}">{{ $floorOption->label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('floor_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Opis')" />
            <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="floor_plan_image" :value="__('Tlocrt jedinice')" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Slika na kojoj će se kasnije označiti prostorije unutar jedinice.') }}</p>
            <input wire:model="floor_plan_image" id="floor_plan_image" type="file" accept="image/*" class="block mt-2 w-full text-sm text-gray-600 dark:text-gray-400" />
            <x-input-error :messages="$errors->get('floor_plan_image')" class="mt-2" />

            @if ($floor_plan_image)
                <img src="{{ $floor_plan_image->temporaryUrl() }}" class="mt-2 max-h-48 rounded" alt="">
            @elseif ($unit?->floor_plan_image)
                <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" class="mt-2 max-h-48 rounded" alt="">
            @endif
        </div>

        <label class="flex items-center gap-2">
            <input wire:model="is_featured" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Istaknuta jedinica (prikazuje se na početnoj stranici)') }}</span>
        </label>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ $unit ? route('admin.units.show', $unit) : route('admin.buildings.show', $building) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
