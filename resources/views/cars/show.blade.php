<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.camera_cars'), 'url' => route('cars.index')],
    ['label' => $car->plate_number],
]">
    <x-slot name="header">
        {{ __('cars.details_title') }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center font-mono font-bold tracking-wider px-3 py-1 rounded bg-slate-900 text-amber-400 text-base border border-slate-700 shadow-sm">
                    {{ $car->plate_number }}
                </span>
                <span class="text-xs text-muted">{{ __('cars.camera_id') }}: {{ $car->camera_id ?? __('cars.unknown') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.create', ['car_id' => $car->id]) }}">
                    <x-button variant="primary" size="sm">{{ __('cars.open_order') }}</x-button>
                </a>
                <a href="{{ route('cars.index') }}">
                    <x-button variant="secondary" size="sm">{{ __('common.back') }}</x-button>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Asosiy ma'lumotlar -->
            <x-card :title="__('cars.camera_info')">
                <dl class="divide-y divide-border text-sm">
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-muted">ID:</dt>
                        <dd class="font-medium text-content">#{{ $car->id }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-muted">{{ __('cars.plate_number') }}:</dt>
                        <dd class="font-mono font-bold text-content">{{ $car->plate_number }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-muted">{{ __('cars.camera_id') }}:</dt>
                        <dd class="font-mono text-content">{{ $car->camera_id ?? '—' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-muted">{{ __('cars.detected_at') }}:</dt>
                        <dd class="font-medium text-content">{{ $car->detected_at ? format_datetime($car->detected_at, 'D MMM YYYY, HH:mm:ss') : '—' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-muted">{{ __('cars.system_recorded_at') }}:</dt>
                        <dd class="text-content">{{ format_datetime($car->created_at, 'D MMM YYYY, HH:mm:ss') }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Kamera Raw Payload -->
            <x-card title="Raw Payload (JSON)">
                <pre class="bg-surface-2 text-content border border-border p-4 rounded-lg text-xs font-mono overflow-x-auto max-h-64">{{ json_encode($car->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </x-card>
        </div>

        <!-- Ushbu avtomobil zakazlari tarixi -->
        <x-card :title="__('cars.order_history')" class="p-0">
            <x-table :empty="$car->orders->isEmpty()" :emptyText="__('cars.empty_orders')">
                <x-slot name="head">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">{{ __('orders.service_type') }}</th>
                    <th class="px-4 py-3">{{ __('common.price') }}</th>
                    <th class="px-4 py-3">{{ __('orders.operator') }}</th>
                    <th class="px-4 py-3">{{ __('orders.washer') }}</th>
                    <th class="px-4 py-3">{{ __('common.status') }}</th>
                    <th class="px-4 py-3">{{ __('common.date') }}</th>
                </x-slot>

                @foreach($car->orders as $ord)
                    <tr class="hover:bg-table-hover transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-muted">#{{ $ord->id }}</td>
                        <td class="px-4 py-3 font-medium text-content">{{ $ord->category->name }}</td>
                        <td class="px-4 py-3 font-semibold text-content">{{ format_money($ord->price) }}</td>
                        <td class="px-4 py-3 text-xs text-muted">{{ $ord->operator->name }}</td>
                        <td class="px-4 py-3 text-xs text-muted">{{ $ord->washer->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $ord->status->badgeClass() }}">
                                {{ $ord->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-muted">{{ format_datetime($ord->created_at) }}</td>
                    </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>
</x-app-layout>
