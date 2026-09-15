<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('admin.investors.show', $investor) }}" wire:navigate class="hover:underline">{{ $investor->company_name }}</a>
    </p>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-6">
        {{ $project ? __('Uredi projekt') : __('Novi projekt') }}
    </h2>

    <form wire:submit="save" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6 max-w-2xl">
        <div>
            <x-input-label for="name" :value="__('Naziv projekta')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="location" :value="__('Lokacija')" />
            <x-text-input wire:model="location" id="location" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('location')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select wire:model="status" id="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @foreach (\App\Enums\ProjectStatus::cases() as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Opis')" />
            <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="cover_image" :value="__('Naslovna slika')" />
            <input wire:model="cover_image" id="cover_image" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-400" />
            <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />

            @if ($cover_image)
                <img src="{{ $cover_image->temporaryUrl() }}" class="mt-2 h-24 rounded" alt="">
            @elseif ($project?->cover_image)
                <img src="{{ Storage::disk('public')->url($project->cover_image) }}" class="mt-2 h-24 rounded" alt="">
            @endif
        </div>

        <label class="flex items-center gap-2">
            <input wire:model="is_featured" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Istaknuti projekt (prikazuje se na početnoj stranici)') }}</span>
        </label>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ $project ? route('admin.projects.show', $project) : route('admin.investors.show', $investor) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
