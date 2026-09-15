<div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ $investorUrl }}" wire:navigate class="hover:underline">{{ $floor->building->project->investor->company_name }}</a>
        /
        <a href="{{ route($routePrefix.'projects.show', $floor->building->project) }}" wire:navigate class="hover:underline">{{ $floor->building->project->name }}</a>
        /
        <a href="{{ route($routePrefix.'buildings.show', $floor->building) }}" wire:navigate class="hover:underline">{{ $floor->building->name }}</a>
    </p>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Zone jedinica') }} — {{ $floor->label }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Označi na tlocrtu kata gdje se nalazi svaka jedinica.') }}</p>
        </div>

        <a href="{{ route($routePrefix.'buildings.show', $floor->building) }}" wire:navigate>
            <x-secondary-button type="button">{{ __('Natrag na objekat') }}</x-secondary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <x-zone-editor
            :image="$planUrl"
            :zones="$zones"
            new-label-placeholder="npr. A4"
            empty-image-message="Prvo dodaj sliku tlocrta kata u uređivanju kata da bi mogao/la označiti jedinice."
        />
    </div>
</div>
