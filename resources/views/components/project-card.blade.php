@props(['project'])

<a href="{{ route('public.projects.show', $project) }}" wire:navigate class="group block rounded-[20px] border border-line bg-canvas-raised overflow-hidden transition duration-300 hover:-translate-y-1.5 hover:border-white/30 hover:shadow-2xl hover:shadow-black/50">
    <div class="aspect-[4/3] overflow-hidden relative bg-gradient-to-br from-[#1D1D1B] to-canvas mullions-fine">
        @if ($project->cover_image)
            <img src="{{ Storage::disk('public')->url($project->cover_image) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $project->name }}">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
        @else
            <div class="absolute inset-0 flex items-center justify-center text-white/15">
                <x-building-placeholder-icon class="h-12 w-12" />
            </div>
        @endif

        @if ($project->is_featured)
            <span class="absolute top-3.5 left-3.5 inline-flex items-center rounded-full bg-ink px-3 py-1.5 text-[11px] font-bold text-canvas">
                {{ __('Istaknuto') }}
            </span>
        @endif

        <span @class([
            'absolute top-3.5 right-3.5 inline-flex items-center rounded-full px-3 py-1.5 text-[11px] font-bold backdrop-blur-sm',
            'bg-ink text-canvas' => $project->status === \App\Enums\ProjectStatus::Completed,
            'border border-white/50 bg-black/30 text-ink' => $project->status === \App\Enums\ProjectStatus::InProgress,
            'border border-line bg-black/40 text-ink-soft' => $project->status === \App\Enums\ProjectStatus::Planned,
        ])>{{ $project->status->label() }}</span>
    </div>

    <div class="p-5">
        <h3 class="font-display font-semibold text-lg leading-snug text-ink">{{ $project->name }}</h3>

        @if ($project->location)
            <p class="mt-2 flex items-center gap-1.5 text-[13px] text-ink-soft">
                <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5 shrink-0"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.4"/></svg>
                {{ $project->location }}
            </p>
        @endif

        <div class="mt-4 flex items-center justify-between text-[12.5px]">
            <span class="text-ink-faint">{{ $project->investor->company_name }}</span>
            @isset($project->buildings_count)
                <span class="font-semibold text-ink">{{ trans_choice('{1}:count zgrada|[2,4]:count zgrade|[5,*]:count zgrada', $project->buildings_count, ['count' => $project->buildings_count]) }}</span>
            @endisset
        </div>
    </div>
</a>
