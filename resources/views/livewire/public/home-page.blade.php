<div>
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">{{ __('Pronađi svoj novi dom') }}</h1>
        <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            {{ __('Pregledaj stambene projekte i dostupne jedinice naših investitora.') }}
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <h2 class="text-xl font-semibold mb-6">{{ __('Istaknuti projekti') }}</h2>

        @if ($projects->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Trenutno nema objavljenih projekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <a href="{{ route('public.projects.show', $project) }}" wire:navigate class="group block rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                        <div class="aspect-[4/3] bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if ($project->cover_image)
                                <img src="{{ Storage::disk('public')->url($project->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $project->name }}">
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
        @endif
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <h2 class="text-xl font-semibold mb-6">{{ __('Istaknute jedinice') }}</h2>

        @if ($units->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Trenutno nema objavljenih jedinica.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units as $unit)
                    <a href="{{ route('public.units.show', $unit) }}" wire:navigate class="group block rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if ($unit->floor_plan_image)
                                <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $unit->code }}">
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
        @endif
    </section>
</div>
