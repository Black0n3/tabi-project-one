@props(['unit'])

<a href="{{ route('public.units.show', $unit) }}" wire:navigate class="group block rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <div class="aspect-square bg-stone-100 dark:bg-stone-800 overflow-hidden relative">
        @if ($unit->floor_plan_image)
            <img src="{{ Storage::disk('public')->url($unit->floor_plan_image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $unit->code }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-300 dark:text-stone-700">
                <x-building-placeholder-icon class="h-10 w-10" />
            </div>
        @endif

        <div class="absolute top-3 right-3">
            <x-unit-status-badge :status="$unit->status" />
        </div>
    </div>

    <div class="p-5">
        <h3 class="font-display font-semibold text-lg leading-snug text-stone-900 dark:text-stone-100">{{ $unit->building->project->name }}</h3>
        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
            {{ __('Jedinica') }} {{ $unit->code }} &middot; {{ $unit->area_m2 }} m²
            @if ($unit->room_count) &middot; {{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }} @endif
        </p>

        @if ($unit->price)
            <p class="mt-3 font-display text-xl font-semibold text-emerald-900 dark:text-emerald-400">
                {{ number_format((float) $unit->price, 0, ',', '.') }} €
            </p>
        @endif
    </div>
</a>
