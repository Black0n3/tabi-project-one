@props(['class' => 'h-10 w-10'])

<svg viewBox="0 0 24 24" fill="none" {{ $attributes->merge(['class' => $class]) }}>
    <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M5.5 10v9a1 1 0 0 0 1 1H10v-5.5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1V20h3.5a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
