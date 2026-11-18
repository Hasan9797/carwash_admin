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
        <label for="{{ $id }}" class="block text-xs font-medium text-content">
            {{ $label }}
        </label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-border bg-surface px-3.5 py-2 text-sm text-content focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:bg-surface-2 disabled:text-muted' . ($errors->has($name) ? ' border-danger focus:border-danger focus:ring-danger/20' : '')
        ]) }}
    >
        {{ $slot }}
    </select>
    @if($name && $errors->has($name))
        <p class="text-xs text-danger mt-1">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="text-xs text-danger mt-1">{{ $error }}</p>
    @endif
</div>
