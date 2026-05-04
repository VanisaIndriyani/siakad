@props(['title' => null, 'subtitle' => null])

@php
    $title = $title;
    $subtitle = $subtitle;
@endphp

@include('layouts.portal', [
    'title' => $title,
    'subtitle' => $subtitle,
    'sidebar' => $sidebar ?? null,
    'slot' => $slot,
])
