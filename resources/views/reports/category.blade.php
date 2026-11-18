<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.reports'), 'url' => route('reports.category')],
    ['label' => __('reports.tabs_services')],
]">
    <x-slot name="header">
        {{ __('reports.title') }}: {{ __('reports.tabs_services') }}
    </x-slot>

    <!-- Hisobot bo'limlari navigatsiyasi (Tabs) -->
    <div class="flex items-center gap-2 border-b border-border pb-3 overflow-x-auto">
        <a href="{{ route('reports.category') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.category') ? 'bg-primary text-primary-contrast' : 'text-muted hover:bg-surface-2 hover:text-content' }}">
            {{ __('reports.tabs_services') }}
        </a>
        <a href="{{ route('reports.region') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.region') ? 'bg-primary text-primary-contrast' : 'text-muted hover:bg-surface-2 hover:text-content' }}">
            {{ __('reports.tabs_regions') }}
        </a>
        <a href="{{ route('reports.operator') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.operator') ? 'bg-primary text-primary-contrast' : 'text-muted hover:bg-surface-2 hover:text-content' }}">
            {{ __('reports.tabs_operators') }}
        </a>
        <a href="{{ route('reports.washer') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.washer') ? 'bg-primary text-primary-contrast' : 'text-muted hover:bg-surface-2 hover:text-content' }}">
            {{ __('reports.tabs_washers') }}
        </a>
    </div>

    <!-- Filtr va Eksport paneli -->
    <x-card>
        <form method="GET" action="{{ route('reports.category') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <x-input type="date" label="{{ __('reports.date_from') }}" name="date_from" value="{{ request('date_from') }}" />
            </div>
            <div>
                <x-input type="date" label="{{ __('reports.date_to') }}" name="date_to" value="{{ request('date_to') }}" />
            </div>
            <div>
                <x-select label="{{ __('reports.branch_select') }}" name="region_id">
                    <option value="">{{ __('common.all_branches') }}</option>
                    @foreach($regions as $r)
                        <option value="{{ $r->id }}" {{ request('region_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" class="w-full">{{ __('common.filter') }}</x-button>
                <a href="{{ route('reports.category.export', request()->query()) }}" class="inline-flex">
                    <x-button type="button" variant="success" class="gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Excel</span>
                    </x-button>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Xulosa kartalari -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card :title="__('reports.total_orders_count')" :value="$totalOrders . ' ' . __('dashboard.pcs')" color="blue" />
        <x-stat-card :title="__('reports.total_revenue')" :value="format_money($totalAmount)" color="emerald" />
        <x-stat-card 
            :title="__('reports.average_check')" 
            :value="format_money($totalOrders > 0 ? ($totalAmount / $totalOrders) : 0)" 
            color="indigo" 
        />
    </div>

    <!-- Jadval -->
    <x-card class="p-0">
        <x-table :empty="$data->isEmpty()" :emptyText="__('reports.no_data')">
            <x-slot name="head">
                <th class="px-4 py-3">{{ __('reports.service_name') }}</th>
                <th class="px-4 py-3 text-right">{{ __('reports.orders_count') }}</th>
                <th class="px-4 py-3 text-right">{{ __('reports.total_amount') }}</th>
                <th class="px-4 py-3 text-right">{{ __('reports.average_check') }}</th>
            </x-slot>

            @foreach($data as $row)
                <tr class="hover:bg-table-hover transition-colors">
                    <td class="px-4 py-3 font-semibold text-content">{{ $row->title }}</td>
                    <td class="px-4 py-3 text-right font-medium text-content">{{ $row->orders_count }} {{ __('dashboard.pcs') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-content whitespace-nowrap">{{ format_money($row->total_amount) }}</td>
                    <td class="px-4 py-3 text-right font-mono text-muted whitespace-nowrap">{{ format_money($row->average_amount) }}</td>
                </tr>
            @endforeach

            @if($data->isNotEmpty())
                <tr class="bg-table-header font-bold text-content border-t-2 border-border">
                    <td class="px-4 py-3 text-xs uppercase tracking-wider">{{ __('reports.total_label') }}</td>
                    <td class="px-4 py-3 text-right">{{ $totalOrders }} {{ __('dashboard.pcs') }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap text-primary">{{ format_money($totalAmount) }}</td>
                    <td class="px-4 py-3 text-right font-mono whitespace-nowrap">{{ format_money($totalOrders > 0 ? ($totalAmount / $totalOrders) : 0) }}</td>
                </tr>
            @endif
        </x-table>
    </x-card>
</x-app-layout>
