<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $project->investor->company_name }}</p>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $project->name }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $project->location }} &middot; {{ $project->status->label() }}</p>

        @if ($project->description)
            <p class="mt-6 max-w-2xl text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
        @endif
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <h2 class="text-lg font-semibold mb-6">{{ __('Objekti') }}</h2>

        @if ($buildings->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ovaj projekt još nema objavljenih objekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($buildings as $building)
                    <a href="{{ route('public.buildings.show', $building) }}" wire:navigate class="group block rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                        <div class="aspect-[4/3] bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if ($building->facade_image)
                                <img src="{{ Storage::disk('public')->url($building->facade_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $building->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm">{{ __('Bez slike') }}</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium">{{ $building->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $building->type->label() }} &middot; {{ $building->units_count }} {{ __('jedinica') }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
