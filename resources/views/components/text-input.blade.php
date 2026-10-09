@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-line-strong text-ink placeholder:text-ink-faint focus:border-brand focus:ring-brand rounded-md shadow-sm disabled:bg-mist']) }}>
