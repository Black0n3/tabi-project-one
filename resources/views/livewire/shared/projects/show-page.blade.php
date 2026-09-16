<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $project->investor->company_name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ $project->name }}
                @if ($project->is_featured)
                    <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 text-xs text-amber-800 dark:text-amber-300">{{ __('Istaknuto') }}</span>
                @endif
                @if ($project->is_hidden)
                    <span class="ms-2 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-700 dark:text-gray-300">{{ __('Skriveno') }}</span>
                @endif
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->location }} &middot; {{ $project->status->label() }}</p>
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
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 max-w-2xl">{{ $project->description }}</p>
    @endif

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Objekat') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Tip') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Katovi') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Jedinice') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($buildings as $building)
                    <tr wire:key="building-{{ $building->id }}">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route($routePrefix.'buildings.show', $building) }}" wire:navigate class="font-medium hover:underline">
                                {{ $building->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $building->type->label() }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $building->floors_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $building->units_count }}</td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route($routePrefix.'buildings.edit', $building) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteBuilding({{ $building->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovaj objekat, sve katove i jedinice u njemu?') }}" class="text-red-600 dark:text-red-400 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Ovaj projekt još nema objekata.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
