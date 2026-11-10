@props([
    'type' => 'info',
])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'danger', 'error' => 'bg-rose-50 border-rose-200 text-rose-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        default => 'bg-blue-50 border-blue-200 text-blue-800',
    };
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-xl border text-sm flex items-start gap-3 {$classes}"]) }} role="alert">
    <div class="flex-1">
        {{ $slot }}
    </div>
</div>
