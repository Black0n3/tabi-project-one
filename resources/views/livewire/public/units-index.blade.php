<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold">{{ __('Sve jedinice') }}</h1>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <x-text-input wire:model.live.debounce.300ms="location" type="search" class="col-span-2 sm:col-span-1" placeholder="{{ __('Lokacija') }}" />

            <select wire:model.live="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('Svi statusi') }}</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('Svi tipovi') }}</option>
                @foreach ($types as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>

            <x-text-input wire:model.live.debounce.300ms="minArea" type="number" min="0" placeholder="{{ __('Min m²') }}" />
            <x-text-input wire:model.live.debounce.300ms="maxArea" type="number" min="0" placeholder="{{ __('Max m²') }}" />

            <div class="flex gap-2 col-span-2 sm:col-span-1">
                <x-text-input wire:model.live.debounce.300ms="minPrice" type="number" min="0" placeholder="{{ __('Min €') }}" />
                <x-text-input wire:model.live.debounce.300ms="maxPrice" type="number" min="0" placeholder="{{ __('Max €') }}" />
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if ($units->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Nema jedinica koje odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units as $unit)
                    <a href="{{ route('public.units.show', $unit) }}" wire:navigate class="group block rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if ($unit->floor_plan_image)
                                <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $unit->code }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm">{{ __('Bez tlocrta') }}</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium">{{ $unit->building->project->name }} — {{ $unit->code }}</h3>
                                <x-unit-status-badge :status="$unit->status" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $unit->area_m2 }} m²
                                @if ($unit->price) &middot; {{ number_format((float) $unit->price, 0, ',', '.') }} € @endif
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>
