@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm text-sky-100']) }}>
        {{ $status }}
    </div>
@endif
