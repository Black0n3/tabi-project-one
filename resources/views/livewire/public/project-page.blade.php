<div>
    <x-page-hero :image="$project->cover_image ? Storage::disk('public')->url($project->cover_image) : null">
        <p class="text-[13px] font-semibold text-ink-soft">{{ $project->investor->company_name }}</p>
        <h1 class="mt-3.5 max-w-2xl font-display text-4xl sm:text-[52px] font-medium leading-[1.08] text-ink">{{ $project->name }}</h1>
        <p class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-ink-soft">
            @if ($project->location)
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.4"/></svg>
                    {{ $project->location }}
                </span>
                <span class="h-1 w-1 rounded-full bg-ink-faint"></span>
            @endif
            <span @class([
                'inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-bold',
                'bg-ink text-canvas' => $project->status === \App\Enums\ProjectStatus::Completed,
                'border border-white/50 text-ink' => $project->status === \App\Enums\ProjectStatus::InProgress,
                'border border-line bg-white/[0.06] text-ink-soft' => $project->status === \App\Enums\ProjectStatus::Planned,
            ])>{{ $project->status->label() }}</span>
        </p>

        <dl class="mt-9 flex">
            <div class="pr-8">
                <dt class="font-display text-2xl font-semibold text-ink">{{ $buildings->count() }}</dt>
                <dd class="mt-1 text-[11.5px] text-ink-faint">{{ __('objekata') }}</dd>
            </div>
            @isset($project->units_count)
                <div class="pl-8 border-l border-line">
                    <dt class="font-display text-2xl font-semibold text-ink">{{ $project->units_count }}</dt>
                    <dd class="mt-1 text-[11.5px] text-ink-faint">{{ __('jedinica') }}</dd>
                </div>
            @endisset
        </dl>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        @if ($project->description)
            <p class="max-w-2xl text-base leading-[1.7] text-ink-soft mb-14">{{ $project->description }}</p>
        @endif

        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('U ovom projektu') }}</span>
                <h2 class="mt-3 font-display text-3xl font-medium text-ink">{{ __('Objekti') }}</h2>
            </div>
        </div>

        @if ($buildings->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Ovaj projekt još nema objavljenih objekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach ($buildings as $building)
                    <x-building-card :building="$building" />
                @endforeach
            </div>
        @endif
    </div>
</div>
