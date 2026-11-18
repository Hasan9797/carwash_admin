@props(['title' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-surface rounded-xl shadow-sm border border-border overflow-hidden text-content']) }}>
    @if($title || $action)
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            @if($title)
                <h3 class="text-base font-semibold text-content">{{ $title }}</h3>
            @endif
            @if($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
