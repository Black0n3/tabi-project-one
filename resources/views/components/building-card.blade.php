@props(['building'])

<a href="{{ route('public.buildings.show', $building) }}" wire:navigate class="group block rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <div class="aspect-[4/3] bg-stone-100 dark:bg-stone-800 overflow-hidden">
        @if ($building->facade_image)
            <img src="{{ Storage::disk('public')->url($building->facade_image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $building->name }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-300 dark:text-stone-700">
                <x-building-placeholder-icon class="h-12 w-12" />
            </div>
        @endif
    </div>

    <div class="p-5">
        <h3 class="font-display font-semibold text-lg leading-snug text-stone-900 dark:text-stone-100">{{ $building->name }}</h3>
        <p class="mt-1.5 text-sm text-stone-500 dark:text-stone-400">
            {{ $building->type->label() }} &middot; {{ $building->units_count }} {{ __('jedinica') }}
        </p>
    </div>
</a>
