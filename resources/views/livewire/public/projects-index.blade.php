<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 sm:pt-16 pb-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Pretraga') }}</p>
        <h1 class="font-display text-3xl sm:text-4xl font-semibold">{{ __('Svi projekti') }}</h1>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row gap-3">
            <x-text-input wire:model.live.debounce.300ms="location" type="search" class="w-full sm:w-64" placeholder="{{ __('Pretraži po lokaciji...') }}" />

            <select wire:model.live="status" class="border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 rounded-md shadow-sm text-sm focus:ring-emerald-600 focus:border-emerald-600">
                <option value="">{{ __('Svi statusi') }}</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        @if ($projects->isEmpty())
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Nema projekata koji odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
