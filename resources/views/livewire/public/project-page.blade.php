<div>
    <x-page-hero :image="$project->cover_image ? Storage::disk('public')->url($project->cover_image) : null">
        <p class="text-sm text-emerald-300/90 mb-3">{{ $project->investor->company_name }}</p>
        <h1 class="font-display text-3xl sm:text-5xl font-semibold leading-tight">{{ $project->name }}</h1>
        <p class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-emerald-100/80">
            @if ($project->location)
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M9.69 18.933a.75.75 0 00.62 0c.327-.146 8.69-3.99 8.69-9.933a9 9 0 10-18 0c0 5.943 8.363 9.787 8.69 9.933zM10 12.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                    {{ $project->location }}
                </span>
                <span class="text-emerald-300/50">&middot;</span>
            @endif
            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-sm font-medium">{{ $project->status->label() }}</span>
        </p>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        @if ($project->description)
            <p class="max-w-2xl text-stone-600 dark:text-stone-400 leading-relaxed mb-14">{{ $project->description }}</p>
        @endif

        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('U ovom projektu') }}</p>
                <h2 class="font-display text-2xl sm:text-3xl font-semibold">{{ __('Objekti') }}</h2>
            </div>
        </div>

        @if ($buildings->isEmpty())
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Ovaj projekt još nema objavljenih objekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($buildings as $building)
                    <x-building-card :building="$building" />
                @endforeach
            </div>
        @endif
    </div>
</div>
