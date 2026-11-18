@props([
    'items' => [],
])

@if(! empty($items))
    <nav {{ $attributes->merge(['class' => 'mb-2 flex w-full items-center gap-1 text-xs text-muted overflow-x-auto whitespace-nowrap']) }} aria-label="Breadcrumb">
        <ol class="inline-flex items-center gap-1 sm:gap-1.5">
            @foreach($items as $item)
                <li class="inline-flex items-center gap-1 sm:gap-1.5">
                    @if(! $loop->first)
                        <!-- Ajratuvchi Chevron (~12px) -->
                        <svg class="w-3 h-3 text-muted/60 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif

                    @if($loop->last)
                        <!-- Joriy sahifa (oxirgi element, link emas, to'qroq text-content va font-medium) -->
                        <span class="inline-flex items-center gap-1 text-content font-medium tracking-tight truncate max-w-[200px] sm:max-w-none" aria-current="page">
                            @if($loop->first && str_contains(strtolower($item['label'] ?? ''), 'dashboard'))
                                <svg class="w-3 h-3 text-muted/60 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            @endif
                            <span>{{ $item['label'] ?? '' }}</span>
                        </span>
                    @elseif(! empty($item['url']))
                        <!-- Bosiladigan link (text-muted, hover:text-primary) -->
                        <a href="{{ $item['url'] }}" class="inline-flex items-center gap-1 text-muted hover:text-primary transition-colors">
                            @if($loop->first && str_contains(strtolower($item['label'] ?? ''), 'dashboard'))
                                <svg class="w-3 h-3 text-muted/60 group-hover:text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            @endif
                            <span>{{ $item['label'] ?? '' }}</span>
                        </a>
                    @else
                        <!-- Bo'lim/guruh sarlavhasi (link emas, oddiy text-muted matn) -->
                        <span class="text-muted">
                            {{ $item['label'] ?? '' }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
