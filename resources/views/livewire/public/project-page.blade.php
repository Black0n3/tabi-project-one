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

        @if (! empty($project->gallery))
            @php
                $gallery = collect($project->gallery)->map(fn ($item) => [
                    'url' => Storage::disk('public')->url($item['path']),
                    'caption' => $item['caption'] ?? '',
                ])->values();
            @endphp

            <div class="mt-20" x-data="{ open: null }" @keydown.escape.window="open = null">
                <div class="mb-8">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ __('Vizualizacije') }}</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-navy-900">{{ __('Galerija') }}</h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($gallery as $i => $item)
                        <button type="button" @click="open = {{ $i }}" class="group relative aspect-[16/10] overflow-hidden rounded-xl border border-line bg-slate-100 text-left shadow-card">
                            <img src="{{ $item['url'] }}" alt="{{ $item['caption'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/85 to-transparent px-4 pb-3 pt-10 text-[13px] font-semibold leading-snug text-white">{{ $item['caption'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div x-show="open !== null" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/90 p-4" @click.self="open = null" role="dialog" aria-modal="true">
                    @foreach ($gallery as $i => $item)
                        <figure x-show="open === {{ $i }}" class="max-h-full max-w-6xl">
                            <img src="{{ $item['url'] }}" alt="{{ $item['caption'] }}" class="max-h-[80vh] w-auto rounded-lg shadow-2xl">
                            <figcaption class="mt-3 text-center text-sm font-semibold text-white/85">{{ $item['caption'] }}</figcaption>
                        </figure>
                    @endforeach
                    <button type="button" @click="open = null" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" aria-label="{{ __('Zatvori') }}">
                        <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5"><path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" @click="open = (open + {{ $gallery->count() - 1 }}) % {{ $gallery->count() }}" class="absolute left-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" aria-label="{{ __('Prethodna') }}">‹</button>
                    <button type="button" @click="open = (open + 1) % {{ $gallery->count() }}" class="absolute right-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" aria-label="{{ __('Sljedeća') }}">›</button>
                </div>
            </div>
        @endif
    </div>
</div>
