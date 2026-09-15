<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $building->project->investor->company_name }}</a>
        /
        <a href="{{ route($routePrefix.'projects.show', $building->project) }}" wire:navigate class="hover:underline">{{ $building->project->name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $building->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $building->type->label() }} @if ($building->address) &middot; {{ $building->address }} @endif</p>
        </div>

        <a href="{{ route($routePrefix.'buildings.edit', $building) }}" wire:navigate>
            <x-secondary-button type="button">{{ __('Uredi') }}</x-secondary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Katovi') }}</h3>
                <a href="{{ route($routePrefix.'floors.create', ['building' => $building->id]) }}" wire:navigate class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    + {{ __('Novi kat') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($floors as $floor)
                    <div wire:key="floor-{{ $floor->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $floor->label }}</span>
                            <span class="text-gray-500 dark:text-gray-400">({{ $floor->units_count }} {{ __('jedinica') }})</span>
                        </div>
                        <div class="space-x-3">
                            <a href="{{ route($routePrefix.'floors.edit', $floor) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteFloor({{ $floor->id }})" wire:confirm="{{ __('Obrisati ovaj kat?') }}" class="text-red-600 dark:text-red-400 hover:underline">{{ __('Obriši') }}</button>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Objekat još nema katova.') }}</p>
                @endforelse
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Jedinice') }}</h3>
                <a href="{{ route($routePrefix.'units.create', ['building' => $building->id]) }}" wire:navigate class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    + {{ __('Nova jedinica') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($units as $unit)
                    <div wire:key="unit-{{ $unit->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                        <div>
                            <a href="{{ route($routePrefix.'units.show', $unit) }}" wire:navigate class="font-medium text-gray-900 dark:text-gray-100 hover:underline">{{ $unit->code }}</a>
                            <span class="text-gray-500 dark:text-gray-400">{{ $unit->floor?->label ?? __('bez kata') }} &middot; {{ $unit->area_m2 }} m²</span>
                            <span @class([
                                'ms-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs',
                                'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' => $unit->status === \App\Enums\UnitStatus::Dostupno,
                                'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' => $unit->status === \App\Enums\UnitStatus::Rezervirano,
                                'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' => $unit->status === \App\Enums\UnitStatus::Prodano,
                            ])>{{ $unit->status->label() }}</span>
                        </div>
                        <div class="space-x-3">
                            <a href="{{ route($routePrefix.'units.edit', $unit) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteUnit({{ $unit->id }})" wire:confirm="{{ __('Obrisati ovu jedinicu?') }}" class="text-red-600 dark:text-red-400 hover:underline">{{ __('Obriši') }}</button>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Objekat još nema jedinica.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
