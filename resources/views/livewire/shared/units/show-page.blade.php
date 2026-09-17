<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $unit->building->project->investor->company_name }}</a>
        /
        <a href="{{ route($routePrefix.'projects.show', $unit->building->project) }}" wire:navigate class="hover:underline">{{ $unit->building->project->name }}</a>
        /
        <a href="{{ route($routePrefix.'buildings.show', $unit->building) }}" wire:navigate class="hover:underline">{{ $unit->building->name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ __('Jedinica') }} {{ $unit->code }}
                @if ($unit->is_featured)
                    <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 text-xs text-amber-800 dark:text-amber-300">{{ __('Istaknuto') }}</span>
                @endif
                <span @class([
                    'ms-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs',
                    'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' => $unit->status === \App\Enums\UnitStatus::Dostupno,
                    'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' => $unit->status === \App\Enums\UnitStatus::Rezervirano,
                    'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' => $unit->status === \App\Enums\UnitStatus::Prodano,
                ])>{{ $unit->status->label() }}</span>
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $unit->type->label() }} &middot; {{ $unit->area_m2 }} m²
                @if ($unit->room_count) &middot; {{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }} @endif
                &middot; {{ $unit->floor?->label ?? __('bez kata') }}
                @if ($unit->price) &middot; {{ number_format((float) $unit->price, 0, ',', '.') }} € @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route($routePrefix.'units.zones', $unit) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Zone prostorija') }}</x-secondary-button>
            </a>
            <a href="{{ route($routePrefix.'units.edit', $unit) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Uredi') }}</x-secondary-button>
            </a>
        </div>
    </div>

    @if ($unit->description)
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 max-w-2xl">{{ $unit->description }}</p>
    @endif

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Prostorije') }}</h3>
        <a href="{{ route($routePrefix.'rooms.create', ['unit' => $unit->id]) }}" wire:navigate class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            + {{ __('Nova prostorija') }}
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg divide-y divide-gray-200 dark:divide-gray-700 max-w-2xl">
        @forelse ($rooms as $room)
            <div wire:key="room-{{ $room->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                <div>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $room->name }}</span>
                    @if ($room->area_m2)
                        <span class="text-gray-500 dark:text-gray-400">{{ $room->area_m2 }} m²</span>
                    @endif
                </div>
                <div class="space-x-3">
                    <a href="{{ route($routePrefix.'rooms.edit', $room) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                    <button type="button" wire:click="deleteRoom({{ $room->id }})" wire:confirm="{{ __('Obrisati ovu prostoriju?') }}" class="text-red-600 dark:text-red-400 hover:underline">{{ __('Obriši') }}</button>
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Jedinica još nema unesenih prostorija.') }}</p>
        @endforelse
    </div>
</div>
