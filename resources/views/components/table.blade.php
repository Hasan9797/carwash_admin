@props([
    'head' => null,
    'empty' => false,
    'emptyText' => null,
])

@php
    $emptyText = $emptyText ?? __('common.no_data');
@endphp

<div class="overflow-x-auto rounded-lg border border-border bg-surface">
    <table class="min-w-full divide-y divide-border text-left text-sm text-content">
        @if($head)
            <thead class="bg-table-header text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-border bg-surface">
            @if($empty)
                <tr>
                    <td colspan="100%" class="px-6 py-12 text-center text-muted">
                        <svg class="mx-auto h-10 w-10 text-muted/60 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-sm font-medium">{{ $emptyText }}</p>
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
