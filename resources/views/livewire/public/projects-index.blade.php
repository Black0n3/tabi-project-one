<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold">{{ __('Svi projekti') }}</h1>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <x-text-input wire:model.live.debounce.300ms="location" type="search" class="w-full sm:w-64" placeholder="{{ __('Pretraži po lokaciji...') }}" />

            <select wire:model.live="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('Svi statusi') }}</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if ($projects->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Nema projekata koji odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <a href="{{ route('public.projects.show', $project) }}" wire:navigate class="group block rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                        <div class="aspect-[4/3] bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if ($project->cover_image)
                                <img src="{{ Storage::disk('public')->url($project->cover_image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $project->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm">{{ __('Bez slike') }}</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $project->location }} &middot; {{ $project->status->label() }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $project->investor->company_name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
