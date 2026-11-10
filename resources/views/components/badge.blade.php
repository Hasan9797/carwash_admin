@props([
    'color' => 'blue',
])

@php
    $classes = match($color) {
        'emerald', 'green', 'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber', 'yellow', 'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rose', 'red', 'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        'slate', 'gray' => 'bg-slate-50 text-slate-700 border-slate-200',
        'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        default => 'bg-blue-50 text-blue-700 border-blue-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    {{ $slot }}
</span>
