<div>
    <p class="text-sm text-ink-soft mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $building->project->investor->company_name }}</a>
        /
        <a href="{{ route($routePrefix.'projects.show', $building->project) }}" wire:navigate class="hover:underline">{{ $building->project->name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-extrabold text-2xl tracking-tight text-navy-900">{{ $building->name }}</h2>
            <p class="text-sm text-ink-soft">{{ $building->type->label() }} @if ($building->address) &middot; {{ $building->address }} @endif</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route($routePrefix.'buildings.zones', $building) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Zone katova') }}</x-secondary-button>
            </a>
            <a href="{{ route($routePrefix.'buildings.edit', $building) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Uredi') }}</x-secondary-button>
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-navy-900">{{ __('Katovi') }}</h3>
                <a href="{{ route($routePrefix.'floors.create', ['building' => $building->id]) }}" wire:navigate class="text-sm text-brand hover:underline">
                    + {{ __('Novi kat') }}
                </a>
            </div>

            <div class="bg-white shadow-card rounded-xl border border-line divide-y divide-line">
                @forelse ($floors as $floor)
                    <div wire:key="floor-{{ $floor->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-navy-900">{{ $floor->label }}</span>
                            <span class="text-ink-soft">({{ $floor->units_count }} {{ __('jedinica') }})</span>
                        </div>
                        <div class="space-x-3">
                            <a href="{{ route($routePrefix.'floors.zones', $floor) }}" wire:navigate class="text-brand hover:underline">{{ __('Zone jedinica') }}</a>
                            <a href="{{ route($routePrefix.'floors.edit', $floor) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteFloor({{ $floor->id }})" wire:confirm="{{ __('Obrisati ovaj kat?') }}" class="text-red-600 hover:underline">{{ __('Obriši') }}</button>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-ink-soft">{{ __('Objekat još nema katova.') }}</p>
                @endforelse
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-navy-900">{{ __('Jedinice') }}</h3>
                <a href="{{ route($routePrefix.'units.create', ['building' => $building->id]) }}" wire:navigate class="text-sm text-brand hover:underline">
                    + {{ __('Nova jedinica') }}
                </a>
            </div>

            <div class="bg-white shadow-card rounded-xl border border-line divide-y divide-line">
                @forelse ($units as $unit)
                    <div wire:key="unit-{{ $unit->id }}" class="px-4 py-3 flex items-center justify-between text-sm">
                        <div>
                            <a href="{{ route($routePrefix.'units.show', $unit) }}" wire:navigate class="font-medium text-navy-900 hover:underline">{{ $unit->code }}</a>
                            <span class="text-ink-soft">{{ $unit->floor?->label ?? __('bez kata') }} &middot; {{ $unit->area_m2 }} m²</span>
                            <span @class([
                                'ms-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs',
                                'bg-emerald-100 text-emerald-800' => $unit->status === \App\Enums\UnitStatus::Dostupno,
                                'bg-amber-100 text-amber-800' => $unit->status === \App\Enums\UnitStatus::Rezervirano,
                                'bg-line text-ink' => $unit->status === \App\Enums\UnitStatus::Prodano,
                            ])>{{ $unit->status->label() }}</span>
                        </div>
                        <div class="space-x-3">
                            <a href="{{ route($routePrefix.'units.edit', $unit) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteUnit({{ $unit->id }})" wire:confirm="{{ __('Obrisati ovu jedinicu?') }}" class="text-red-600 hover:underline">{{ __('Obriši') }}</button>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-ink-soft">{{ __('Objekat još nema jedinica.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
