@props(['unit'])

<a href="{{ route('public.units.show', $unit) }}" wire:navigate class="group block rounded-[18px] border border-line bg-canvas-raised overflow-hidden transition duration-300 hover:-translate-y-1.5 hover:border-white/30 hover:shadow-2xl hover:shadow-black/50">
    <div class="aspect-[4/3] overflow-hidden relative bg-gradient-to-br from-[#1D1D1B] to-canvas mullions-fine">
        @if ($unit->floor_plan_image)
            <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $unit->code }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-white/15">
                <x-building-placeholder-icon class="h-10 w-10" />
            </div>
        @endif

        <div class="absolute top-3 left-3">
            <x-unit-status-badge :status="$unit->status" />
        </div>
    </div>

    <div class="p-5">
        @if ($unit->price)
            <p class="font-display text-xl font-semibold text-ink">
                {{ number_format((float) $unit->price, 0, ',', '.') }} €
            </p>
        @else
            <p class="font-display text-xl font-semibold text-ink">{{ __('Jedinica') }} {{ $unit->code }}</p>
        @endif

        <p class="mt-1.5 text-[12.5px] text-ink-soft">
            @if ($unit->price){{ __('Jedinica') }} {{ $unit->code }} &middot; @endif{{ $unit->area_m2 }} m²
            @if ($unit->room_count) &middot; {{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }} @endif
        </p>
        <p class="mt-1 text-[11.5px] text-ink-faint">{{ $unit->building->project->name }}</p>
    </div>
</a>
