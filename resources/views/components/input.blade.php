@props([
    'disabled' => false,
    'label' => null,
    'name' => null,
    'id' => null,
    'error' => null,
    'type' => 'text',
    'value' => null,
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
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($name, $value) }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 disabled:bg-slate-50 disabled:text-slate-500' . ($errors->has($name) ? ' border-rose-500 focus:border-rose-500 focus:ring-rose-500/20' : '')
        ]) }}
    />
    @if($name && $errors->has($name))
        <p class="text-xs text-rose-600 mt-1">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="text-xs text-rose-600 mt-1">{{ $error }}</p>
    @endif
</div>
