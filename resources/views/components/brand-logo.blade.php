@props(['variant' => 'logo', 'class' => '', 'alt' => null])

@php
    $alt = $alt ?? Brand::name() . ' Logo';

    $src = match ($variant) {
        'white', 'logo-white' => Brand::logoWhite(),
        'favicon' => Brand::favicon(),
        'avatar' => Brand::fallbackAvatar(),
        default => Brand::logo(),
    };
@endphp

<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $class]) }}>
