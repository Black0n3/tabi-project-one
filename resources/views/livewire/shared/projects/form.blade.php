<div>
    <p class="text-sm text-ink-soft mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $investor->company_name }}</a>
    </p>
    <h2 class="font-extrabold text-2xl tracking-tight text-navy-900 mb-6">
        {{ $project ? __('Uredi projekt') : __('Novi projekt') }}
    </h2>

    <form wire:submit="save" class="bg-white shadow-card rounded-xl border border-line p-6 space-y-6 max-w-2xl">
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
            <select wire:model="status" id="status" class="block mt-1 w-full border-line-strong rounded-md shadow-sm focus:ring-brand focus:border-brand">
                @foreach (\App\Enums\ProjectStatus::cases() as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Opis')" />
            <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-line-strong rounded-md shadow-sm focus:ring-brand focus:border-brand"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="cover_image" :value="__('Naslovna slika')" />
            <input wire:model="cover_image" id="cover_image" type="file" accept="image/*" class="block mt-1 w-full text-sm text-ink-soft" />
            <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />

            @if ($cover_image)
                <img src="{{ $cover_image->temporaryUrl() }}" class="mt-2 h-24 rounded" alt="">
            @elseif ($project?->cover_image)
                <img src="{{ Storage::disk('public')->url($project->cover_image) }}" class="mt-2 h-24 rounded" alt="">
            @endif
        </div>

        <label class="flex items-center gap-2">
            <input wire:model="is_featured" type="checkbox" class="rounded border-line-strong text-brand shadow-sm focus:ring-brand">
            <span class="text-sm text-ink">{{ __('Istaknuti projekt (prikazuje se na početnoj stranici)') }}</span>
        </label>

        <label class="flex items-center gap-2">
            <input wire:model="is_hidden" type="checkbox" class="rounded border-line-strong text-brand shadow-sm focus:ring-brand">
            <span class="text-sm text-ink">{{ __('Skriveno (projekt se ne prikazuje nigdje na javnom sajtu, ali ostaje vidljiv u ovom panelu)') }}</span>
        </label>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ $project ? route($routePrefix.'projects.show', $project) : $investorUrl }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
