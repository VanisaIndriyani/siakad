@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-emerald-100/85']) }}>
    {{ $value ?? $slot }}
</label>
