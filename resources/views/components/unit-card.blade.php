@props(['unit'])

<a href="{{ route('public.units.show', $unit) }}" wire:navigate class="group block overflow-hidden rounded-xl border border-line bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-lift">
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        @if ($unit->floor_plan_image)
            <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" loading="lazy" class="absolute inset-0 h-full w-full bg-white object-contain p-2 transition duration-500 group-hover:scale-105" alt="{{ $unit->code }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                <x-building-placeholder-icon class="h-12 w-12" />
            </div>
        @endif

        <div class="absolute top-3 left-3">
            <x-unit-status-badge :status="$unit->status" />
        </div>
    </div>

    <div class="p-5">
        @if ($unit->price)
            <p class="text-[22px] font-extrabold tracking-tight text-navy-900">{{ number_format((float) $unit->price, 0, ',', '.') }} €</p>
        @else
            <p class="text-[22px] font-extrabold tracking-tight text-navy-900">{{ __('Na upit') }}</p>
        @endif

        <p class="mt-1 truncate text-sm font-semibold text-ink">{{ __('Jedinica') }} {{ $unit->code }}</p>
        <p class="truncate text-[13px] text-ink-faint">{{ $unit->building->project->name }}</p>

        <div class="mt-4 flex items-center gap-4 border-t border-line pt-4 text-[13px] text-ink-soft">
            @if ($unit->room_count)
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M3 15V8a1 1 0 011-1h12a1 1 0 011 1v7M3 12h14M3 15v2m14-2v2M6 7V5h8v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $unit->room_count }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5">
                <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M4 8V4h4M16 8V4h-4M4 12v4h4m8-4v4h-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ (float) $unit->area_m2 }} m²
            </span>
        </div>
    </div>
</a>
