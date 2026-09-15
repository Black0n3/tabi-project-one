<div>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-6">
        {{ $investor ? __('Uredi investitora') : __('Novi investitor') }}
    </h2>

    <form wire:submit="save" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6 max-w-2xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Ime i prezime kontakt osobe')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email (za prijavu)')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="password" :value="$investor ? __('Nova lozinka (ostavi prazno za zadrži postojeću)') : __('Lozinka')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        <div>
            <x-input-label for="company_name" :value="__('Naziv tvrtke')" />
            <x-text-input wire:model="company_name" id="company_name" class="block mt-1 w-full" type="text" required />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="oib" :value="__('OIB')" />
                <x-text-input wire:model="oib" id="oib" class="block mt-1 w-full" type="text" />
                <x-input-error :messages="$errors->get('oib')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_phone" :value="__('Kontakt telefon')" />
                <x-text-input wire:model="contact_phone" id="contact_phone" class="block mt-1 w-full" type="text" />
                <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="contact_email" :value="__('Kontakt email (javno, za upite)')" />
            <x-text-input wire:model="contact_email" id="contact_email" class="block mt-1 w-full" type="email" />
            <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="logo" :value="__('Logo')" />
            <input wire:model="logo" id="logo" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-400" />
            <x-input-error :messages="$errors->get('logo')" class="mt-2" />

            @if ($logo)
                <img src="{{ $logo->temporaryUrl() }}" class="mt-2 h-16 rounded" alt="">
            @elseif ($investor?->logo_path)
                <img src="{{ Storage::disk('public')->url($investor->logo_path) }}" class="mt-2 h-16 rounded" alt="">
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Spremi') }}</x-primary-button>
            <a href="{{ $investor ? route('admin.investors.show', $investor) : route('admin.investors.index') }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Odustani') }}</x-secondary-button>
            </a>
        </div>
    </form>
</div>
