@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'blue',
    'subtext' => null,
])

@php
    $colorClasses = match($color) {
        'emerald', 'green', 'success' => 'bg-success-soft text-success border-success/30',
        'amber', 'yellow', 'warning' => 'bg-warning-soft text-warning border-warning/30',
        'indigo', 'info' => 'bg-info-soft text-info border-info/30',
        'rose', 'red', 'danger' => 'bg-danger-soft text-danger border-danger/30',
        default => 'bg-primary-soft text-primary border-primary/30',
    };
@endphp

<div class="bg-surface rounded-xl shadow-sm border border-border p-6 flex items-start justify-between text-content">
    <div class="space-y-1">
        <p class="text-xs font-medium uppercase tracking-wider text-muted">{{ $title }}</p>
        <p class="text-2xl font-bold text-content">{{ $value }}</p>
        @if($subtext)
            <p class="text-xs text-muted pt-1">{{ $subtext }}</p>
        @endif
    </div>
    @if($icon)
        <div class="w-12 h-12 rounded-xl flex items-center justify-center border {{ $colorClasses }}">
            {{ $icon }}
        </div>
    @endif
</div>
