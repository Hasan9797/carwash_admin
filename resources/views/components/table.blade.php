@props([
    'head' => null,
    'empty' => false,
    'emptyText' => 'Ma\'lumot topilmadi',
])

<div class="overflow-x-auto rounded-lg border border-slate-200">
    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
        @if($head)
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-200 bg-white">
            @if($empty)
                <tr>
                    <td colspan="100%" class="px-6 py-12 text-center text-slate-400">
                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
