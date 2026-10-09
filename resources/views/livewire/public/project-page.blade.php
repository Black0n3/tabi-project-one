<div>
    <x-page-hero :image="$project->cover_image ? Storage::disk('public')->url($project->cover_image) : null">
        <p class="text-sm font-semibold text-brand-sky">{{ $project->investor->company_name }}</p>
        <h1 class="mt-2.5 max-w-2xl text-4xl sm:text-5xl font-extrabold leading-[1.08] tracking-tight">{{ $project->name }}</h1>
        <p class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/80">
            @if ($project->location)
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.5"/></svg>
                    {{ $project->location }}
                </span>
            @endif
            <span @class([
                'inline-flex items-center rounded px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white',
                'bg-brand' => $project->status === \App\Enums\ProjectStatus::InProgress,
                'bg-emerald-600' => $project->status === \App\Enums\ProjectStatus::Completed,
                'bg-white/20' => $project->status === \App\Enums\ProjectStatus::Planned,
            ])>{{ $project->status->label() }}</span>
        </p>

        <dl class="mt-8 flex gap-10">
            <div>
                <dt class="text-2xl font-extrabold">{{ $buildings->count() }}</dt>
                <dd class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-white/60">{{ __('objekata') }}</dd>
            </div>
            @isset($project->units_count)
                <div>
                    <dt class="text-2xl font-extrabold">{{ $project->units_count }}</dt>
                    <dd class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-white/60">{{ __('jedinica') }}</dd>
                </div>
            @endisset
        </dl>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        @if ($project->description)
            <p class="max-w-2xl text-base leading-[1.75] text-ink-soft mb-14">{{ $project->description }}</p>
        @endif

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ __('U ovom projektu') }}</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-navy-900">{{ __('Objekti') }}</h2>
        </div>

        @if ($buildings->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Ovaj projekt još nema objavljenih objekata.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($buildings as $building)
                    <x-building-card :building="$building" />
                @endforeach
            </div>
        @endif
    </div>
</div>
