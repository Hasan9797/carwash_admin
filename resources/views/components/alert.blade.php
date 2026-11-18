@props([
    'type' => 'info',
])

@php
    $classes = match($type) {
        'success' => 'bg-success-soft border-success/30 text-success',
        'danger', 'error' => 'bg-danger-soft border-danger/30 text-danger',
        'warning' => 'bg-warning-soft border-warning/30 text-warning',
        default => 'bg-info-soft border-info/30 text-info',
    };
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-xl border text-sm flex items-start gap-3 {$classes}"]) }} role="alert">
    <div class="flex-1">
        {{ $slot }}
    </div>
</div>
