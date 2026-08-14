@props([
    'type' => 'button',
    'href' => null,
    'variant' => null,
    'size' => null,
    'icon' => null,
    'disabled' => false,
])

@php
    // Map variant prop to Bootstrap class (variant is optional — raw class still works)
    $variantClass = match ($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-outline-secondary',
        'danger' => 'btn-outline-danger',
        'success' => 'btn-success',
        'info' => 'btn-info',
        'ghost' => 'btn-light',
        default => '',
    };

    $sizeClass = match ($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => trim("btn {$variantClass} {$sizeClass}")]) }}>
        @if ($icon)
            <i class="fa fa-{{ $icon }} mr-1"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => trim("btn {$variantClass} {$sizeClass}")]) }}>
        @if ($icon)
            <i class="fa fa-{{ $icon }} mr-1"></i>
        @endif
        {{ $slot }}
    </button>
@endif
