@props(['building'])

<a href="{{ route('public.buildings.show', $building) }}" wire:navigate class="group block rounded-[20px] border border-line bg-canvas-raised overflow-hidden transition duration-300 hover:-translate-y-1.5 hover:border-white/30 hover:shadow-2xl hover:shadow-black/50">
    <div class="aspect-[4/3] overflow-hidden relative bg-gradient-to-br from-[#1A1A1A] to-canvas mullions">
        @if ($building->facade_image)
            <img src="{{ Storage::disk('public')->url($building->facade_image) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $building->name }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-white/15">
                <x-building-placeholder-icon class="h-12 w-12" />
            </div>
        @endif
    </div>

    <div class="p-5">
        <h3 class="font-display font-semibold text-[17px] leading-snug text-ink">{{ $building->name }}</h3>
        <p class="mt-2 text-[13px] text-ink-soft">
            {{ $building->type->label() }} &middot; {{ $building->units_count }} {{ __('jedinica') }}
        </p>
        <span class="mt-3 inline-flex items-center gap-1.5 text-[12.5px] font-bold text-ink transition-all group-hover:gap-2.5">
            {{ __('Pogledajte tlocrt kata') }} <span aria-hidden="true">→</span>
        </span>
    </div>
</a>
