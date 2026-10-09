@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-navy-900']) }}>
    {{ $value ?? $slot }}
</label>
