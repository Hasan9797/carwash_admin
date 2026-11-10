@props(['title' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden']) }}>
    @if($title || $action)
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            @if($title)
                <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
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
