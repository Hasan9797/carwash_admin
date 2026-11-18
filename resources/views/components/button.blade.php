@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-5 py-2.5 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $variantClasses = match($variant) {
        'secondary' => 'bg-surface-2 text-content hover:bg-border/60 focus:ring-ring border border-border',
        'danger' => 'bg-danger text-white hover:bg-danger/90 focus:ring-danger shadow-sm',
        'success' => 'bg-success text-white hover:bg-success/90 focus:ring-success shadow-sm',
        'outline' => 'bg-transparent text-content hover:bg-surface-2 border border-border focus:ring-primary',
        default => 'bg-primary text-primary-contrast hover:bg-primary-hover focus:ring-ring shadow-sm',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}
>
    {{ $slot }}
</button>
