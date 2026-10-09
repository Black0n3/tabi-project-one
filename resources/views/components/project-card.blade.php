@props(['project'])

<a href="{{ route('public.projects.show', $project) }}" wire:navigate class="group block overflow-hidden rounded-xl border border-line bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-lift">
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        @if ($project->cover_image)
            <img src="{{ Storage::disk('public')->url($project->cover_image) }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $project->name }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                <x-building-placeholder-icon class="h-14 w-14" />
            </div>
        @endif

        <span @class([
            'absolute top-3 left-3 rounded px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white',
            'bg-brand' => $project->status === \App\Enums\ProjectStatus::InProgress,
            'bg-emerald-600' => $project->status === \App\Enums\ProjectStatus::Completed,
            'bg-navy-800' => $project->status === \App\Enums\ProjectStatus::Planned,
        ])>{{ $project->status->label() }}</span>

        @if ($project->is_featured)
            <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded bg-white/95 px-2.5 py-1 text-[11px] font-bold text-navy-900 shadow-sm">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3 text-amber-500"><path d="M10 1.5l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.9 4.7 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
                {{ __('Istaknuto') }}
            </span>
        @endif
    </div>

    <div class="p-5">
        <h3 class="text-lg font-extrabold leading-snug tracking-tight text-navy-900 transition group-hover:text-brand">{{ $project->name }}</h3>

        @if ($project->location)
            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-soft">
                <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4 shrink-0 text-brand"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.5"/></svg>
                {{ $project->location }}
            </p>
        @endif

        <div class="mt-4 flex items-center justify-between gap-3 border-t border-line pt-4 text-[13px]">
            <span class="truncate text-ink-faint">{{ $project->investor->company_name }}</span>
            @isset($project->buildings_count)
                <span class="inline-flex shrink-0 items-center gap-1.5 font-semibold text-ink-soft">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><rect x="4" y="3" width="12" height="14" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M8 7h1m2 0h1M8 10h1m2 0h1M8.5 17v-3h3v3" stroke="currentColor" stroke-width="1.5"/></svg>
                    {{ $project->buildings_count }}
                </span>
            @endisset
        </div>
    </div>
</a>
