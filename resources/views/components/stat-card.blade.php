@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'blue',
    'subtext' => null,
])

@php
    $colorClasses = match($color) {
        'emerald', 'green' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'amber', 'yellow' => 'bg-amber-50 text-amber-600 border-amber-100',
        'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        'rose', 'red' => 'bg-rose-50 text-rose-600 border-rose-100',
        default => 'bg-blue-50 text-blue-600 border-blue-100',
    };
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-start justify-between">
    <div class="space-y-1">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ $title }}</p>
        <p class="text-2xl font-bold text-slate-900">{{ $value }}</p>
        @if($subtext)
            <p class="text-xs text-slate-500 pt-1">{{ $subtext }}</p>
        @endif
    </div>
    @if($icon)
        <div class="w-12 h-12 rounded-xl flex items-center justify-center border {{ $colorClasses }}">
            {{ $icon }}
        </div>
    @endif
</div>
