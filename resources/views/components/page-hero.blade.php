@props(['image' => null])

<section class="relative overflow-hidden bg-navy-950">
    <x-hero-backdrop :image="$image" compact />

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-16 sm:pb-16 text-white">
        {{ $slot }}
    </div>
</section>
