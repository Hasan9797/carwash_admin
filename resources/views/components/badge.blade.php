@props([
    'color' => 'blue',
])

@php
    $classes = match($color) {
        'emerald', 'green', 'success' => 'bg-success-soft text-success border-success/30',
        'amber', 'yellow', 'warning' => 'bg-warning-soft text-warning border-warning/30',
        'rose', 'red', 'danger' => 'bg-danger-soft text-danger border-danger/30',
        'slate', 'gray' => 'bg-surface-2 text-muted border-border',
        'indigo', 'info' => 'bg-info-soft text-info border-info/30',
        default => 'bg-primary-soft text-primary border-primary/30',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    {{ $slot }}
</span>
