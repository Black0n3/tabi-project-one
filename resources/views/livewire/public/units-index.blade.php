<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 sm:pt-16 pb-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Pretraga') }}</p>
        <h1 class="font-display text-3xl sm:text-4xl font-semibold">{{ __('Sve jedinice') }}</h1>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 shadow-sm p-4 sm:p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <x-text-input wire:model.live.debounce.300ms="location" type="search" class="col-span-2 sm:col-span-1" placeholder="{{ __('Lokacija') }}" />

                <select wire:model.live="status" class="border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 rounded-md shadow-sm text-sm focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="">{{ __('Svi statusi') }}</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>

                <select wire:model.live="type" class="border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 rounded-md shadow-sm text-sm focus:ring-emerald-600 focus:border-emerald-600">
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
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        @if ($units->isEmpty())
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Nema jedinica koje odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units as $unit)
                    <x-unit-card :unit="$unit" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>
