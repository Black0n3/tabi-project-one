@props(['project'])

<a href="{{ route('public.projects.show', $project) }}" wire:navigate class="group block rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <div class="aspect-[4/3] bg-stone-100 dark:bg-stone-800 overflow-hidden relative">
        @if ($project->cover_image)
            <img src="{{ Storage::disk('public')->url($project->cover_image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $project->name }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-300 dark:text-stone-700">
                <x-building-placeholder-icon class="h-12 w-12" />
            </div>
        @endif

        @if ($project->is_featured)
            <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-amber-400 px-2.5 py-1 text-[11px] font-semibold text-stone-900 shadow">
                {{ __('Istaknuto') }}
            </span>
        @endif
    </div>

    <div class="p-5">
        <h3 class="font-display font-semibold text-lg leading-snug text-stone-900 dark:text-stone-100">{{ $project->name }}</h3>

        @if ($project->location)
            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-stone-500 dark:text-stone-400">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 shrink-0">
                    <path fill-rule="evenodd" d="M9.69 18.933a.75.75 0 00.62 0c.327-.146 8.69-3.99 8.69-9.933a9 9 0 10-18 0c0 5.943 8.363 9.787 8.69 9.933zM10 12.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
                {{ $project->location }}
            </p>
        @endif

        <div class="mt-4 flex items-center justify-between text-xs">
            <span class="inline-flex items-center rounded-full bg-stone-100 dark:bg-stone-800 px-2.5 py-1 font-medium text-stone-600 dark:text-stone-300">
                {{ $project->status->label() }}
            </span>
            <span class="text-stone-400 dark:text-stone-500">{{ $project->investor->company_name }}</span>
        </div>
    </div>
</a>
