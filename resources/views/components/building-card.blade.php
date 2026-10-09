@props(['building'])

<a href="{{ route('public.buildings.show', $building) }}" wire:navigate class="group block overflow-hidden rounded-xl border border-line bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-lift">
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        @if ($building->facade_image)
            <img src="{{ Storage::disk('public')->url($building->facade_image) }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $building->name }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                <x-building-placeholder-icon class="h-14 w-14" />
            </div>
        @endif
        <span class="absolute top-3 left-3 rounded bg-brand px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">{{ $building->type->label() }}</span>
    </div>

    <div class="p-5">
        <h3 class="text-lg font-extrabold leading-snug tracking-tight text-navy-900 transition group-hover:text-brand">{{ $building->name }}</h3>
        <p class="mt-1.5 text-sm text-ink-soft">{{ $building->units_count }} {{ __('jedinica') }}</p>
        <span class="mt-4 inline-flex items-center gap-1.5 border-t border-line pt-4 text-[13px] font-bold text-brand transition-all group-hover:gap-2.5">
            {{ __('Pogledajte tlocrt kata') }} <span aria-hidden="true">→</span>
        </span>
    </div>
</a>
