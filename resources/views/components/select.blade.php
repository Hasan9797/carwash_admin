@props([
    'disabled' => false,
    'label' => null,
    'name' => null,
    'id' => null,
    'error' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-medium text-slate-700">
            {{ $label }}
        </label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 disabled:bg-slate-50 disabled:text-slate-500' . ($errors->has($name) ? ' border-rose-500 focus:border-rose-500 focus:ring-rose-500/20' : '')
        ]) }}
    >
        {{ $slot }}
    </select>
    @if($name && $errors->has($name))
        <p class="text-xs text-rose-600 mt-1">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="text-xs text-rose-600 mt-1">{{ $error }}</p>
    @endif
</div>
