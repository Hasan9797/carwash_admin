<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.camera_cars')],
]">
    <x-slot name="header">
        {{ __('cars.list_title') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-content">{{ __('cars.list_title') }}</h2>
            <p class="text-xs text-muted">{{ __('cars.list_subtitle') }}</p>
        </div>
    </div>

    <!-- Qidiruv paneli -->
    <x-card>
        <form method="GET" action="{{ route('cars.index') }}" class="flex items-center gap-3">
            <div class="flex-1">
                <x-input name="search" placeholder="{{ __('cars.search_placeholder') }}" value="{{ request('search') }}" />
            </div>
            <x-button type="submit" variant="secondary">{{ __('common.search') }}</x-button>
            @if(request('search'))
                <a href="{{ route('cars.index') }}" class="p-2 text-muted hover:text-danger rounded-lg hover:bg-danger-soft transition-colors" title="{{ __('common.clear') }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>
    </x-card>

    <x-card class="p-0">
        <x-table :empty="$cars->isEmpty()" emptyText="{{ __('cars.empty_cars') }}">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">{{ __('cars.plate_number') }}</th>
                <th class="px-4 py-3">{{ __('cars.camera_id') }}</th>
                <th class="px-4 py-3">{{ __('cars.image') }}</th>
                <th class="px-4 py-3">{{ __('cars.detected_at') }}</th>
                <th class="px-4 py-3">{{ __('cars.order_status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
            </x-slot>

            @foreach($cars as $car)
                <tr class="hover:bg-table-hover transition-colors">
                    <td class="px-4 py-3 text-xs text-muted">#{{ $car->id }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center font-mono font-bold tracking-wider px-2.5 py-1 rounded bg-slate-900 text-amber-400 text-xs border border-slate-700 shadow-xs">
                            {{ $car->plate_number }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-mono text-muted">{{ $car->camera_id ?? __('cars.unknown') }}</td>
                    <td class="px-4 py-3 text-xs text-muted">
                        @if($car->image_path)
                            <a href="{{ asset('storage/' . $car->image_path) }}" target="_blank" class="text-primary hover:underline">
                                {{ __('cars.view_image') }}
                            </a>
                        @else
                            <span class="text-muted/60">{{ __('cars.no_image') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-muted">
                        {{ $car->detected_at ? format_datetime($car->detected_at) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($car->latestOrder)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $car->latestOrder->status->badgeClass() }}">
                                #{{ $car->latestOrder->id }}: {{ $car->latestOrder->status->label() }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-warning-soft text-warning border border-warning/30">
                                {{ __('cars.no_order') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$car->latestOrder)
                                <a href="{{ route('orders.create', ['car_id' => $car->id]) }}">
                                    <x-button variant="primary" size="sm">
                                        {{ __('cars.open_order_btn') }}
                                    </x-button>
                                </a>
                            @endif
                            <a href="{{ route('cars.show', $car->id) }}" class="p-1.5 text-muted hover:text-primary rounded-lg hover:bg-surface-2 transition-colors" title="{{ __('cars.view_details') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($cars->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $cars->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
