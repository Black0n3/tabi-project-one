<div>
    <p class="text-sm text-ink-soft mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $unit->building->project->investor->company_name }}</a>
        /
        <a href="{{ route($routePrefix.'projects.show', $unit->building->project) }}" wire:navigate class="hover:underline">{{ $unit->building->project->name }}</a>
        /
        <a href="{{ route($routePrefix.'buildings.show', $unit->building) }}" wire:navigate class="hover:underline">{{ $unit->building->name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-extrabold text-2xl tracking-tight text-navy-900">
                {{ __('Jedinica') }} {{ $unit->code }}
                @if ($unit->is_featured)
                    <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-800">{{ __('Istaknuto') }}</span>
                @endif
                <span @class([
                    'ms-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs',
                    'bg-emerald-100 text-emerald-800' => $unit->status === \App\Enums\UnitStatus::Dostupno,
                    'bg-amber-100 text-amber-800' => $unit->status === \App\Enums\UnitStatus::Rezervirano,
                    'bg-line text-ink' => $unit->status === \App\Enums\UnitStatus::Prodano,
                ])>{{ $unit->status->label() }}</span>
            </h2>
            <p class="text-sm text-ink-soft">
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
        <p class="text-sm text-ink-soft mb-6 max-w-2xl">{{ $unit->description }}</p>
    @endif

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-navy-900">{{ __('Prostorije') }}</h3>
        <a href="{{ route($routePrefix.'rooms.create', ['unit' => $unit->id]) }}" wire:navigate class="text-sm text-brand hover:underline">
            + {{ __('Nova prostorija') }}
        </a>
    </div>

    <div class="bg-white shadow-card rounded-xl border border-line divide-y divide-line max-w-2xl">
        @forelse ($rooms as $room)
            <div wire:key="room-{{ $room->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                <div>
                    <span class="font-medium text-navy-900">{{ $room->name }}</span>
                    @if ($room->area_m2)
                        <span class="text-ink-soft">{{ $room->area_m2 }} m²</span>
                    @endif
                </div>
                <div class="space-x-3">
                    <a href="{{ route($routePrefix.'rooms.edit', $room) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                    <button type="button" wire:click="deleteRoom({{ $room->id }})" wire:confirm="{{ __('Obrisati ovu prostoriju?') }}" class="text-red-600 hover:underline">{{ __('Obriši') }}</button>
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-sm text-ink-soft">{{ __('Jedinica još nema unesenih prostorija.') }}</p>
        @endforelse
    </div>
</div>
