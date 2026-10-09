@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-sky text-start text-base font-medium text-brand-dark bg-brand-light focus:outline-none focus:text-navy-800 focus:bg-brand-light focus:border-brand-dark transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-ink-soft hover:text-navy-900 hover:bg-mist hover:border-line-strong focus:outline-none focus:text-navy-900 focus:bg-mist focus:border-line-strong transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
