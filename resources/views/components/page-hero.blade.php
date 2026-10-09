@props(['image' => null])

<section class="relative overflow-hidden bg-canvas border-b border-line">
    <x-hero-backdrop :image="$image" compact />

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 sm:pt-20 sm:pb-16">
        {{ $slot }}
    </div>
</section>
