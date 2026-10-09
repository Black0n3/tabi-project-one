<div>
    <p class="text-sm text-ink-soft mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $project->investor->company_name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-extrabold text-2xl tracking-tight text-navy-900">
                {{ $project->name }}
                @if ($project->is_featured)
                    <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-800">{{ __('Istaknuto') }}</span>
                @endif
                @if ($project->is_hidden)
                    <span class="ms-2 inline-flex items-center rounded-full bg-line px-2 py-0.5 text-xs text-ink">{{ __('Skriveno') }}</span>
                @endif
            </h2>
            <p class="text-sm text-ink-soft">{{ $project->location }} &middot; {{ $project->status->label() }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route($routePrefix.'projects.edit', $project) }}" wire:navigate>
                <x-secondary-button type="button">{{ __('Uredi') }}</x-secondary-button>
            </a>
            <a href="{{ route($routePrefix.'buildings.create', ['project' => $project->id]) }}" wire:navigate>
                <x-primary-button>{{ __('Novi objekat') }}</x-primary-button>
            </a>
        </div>
    </div>

    @if ($project->description)
        <p class="text-sm text-ink-soft mb-6 max-w-2xl">{{ $project->description }}</p>
    @endif

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white shadow-card rounded-xl border border-line overflow-hidden">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-mist">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Objekat') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Tip') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Katovi') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Jedinice') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($buildings as $building)
                    <tr wire:key="building-{{ $building->id }}">
                        <td class="px-6 py-4 text-sm text-navy-900">
                            <a href="{{ route($routePrefix.'buildings.show', $building) }}" wire:navigate class="font-medium hover:underline">
                                {{ $building->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $building->type->label() }}</td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $building->floors_count }}</td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $building->units_count }}</td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route($routePrefix.'buildings.edit', $building) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteBuilding({{ $building->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovaj objekat, sve katove i jedinice u njemu?') }}" class="text-red-600 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-ink-soft">
                            {{ __('Ovaj projekt još nema objekata.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
