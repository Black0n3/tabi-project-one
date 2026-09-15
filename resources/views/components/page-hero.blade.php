@props(['image' => null])

<section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 to-emerald-900 text-white">
    @if ($image)
        <img src="{{ $image }}" class="absolute inset-0 w-full h-full object-cover" alt="" aria-hidden="true">
        <div class="absolute inset-0 bg-emerald-950/80"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-emerald-950/75 to-emerald-900/70"></div>
    @endif

    <div class="absolute inset-0 opacity-[0.07]" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 1px, transparent 26px), repeating-linear-gradient(-45deg, white 0, white 1px, transparent 1px, transparent 26px);"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-14 sm:pt-28 sm:pb-16">
        {{ $slot }}
    </div>

    <svg class="relative block w-full h-8 sm:h-10 text-stone-50 dark:text-stone-950" viewBox="0 0 1440 48" preserveAspectRatio="none" fill="currentColor">
        <path d="M0 48 C 360 0, 1080 0, 1440 48 Z"></path>
    </svg>
</section>
