<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.orders')],
]">
    <x-slot name="header">
        {{ __('common.orders') }}
    </x-slot>

    <!-- Yuqori qism: Sarlavha va Yangi zakaz tugmasi -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-content">{{ __('orders.list_title') }}</h2>
            <p class="text-xs text-muted">{{ __('orders.list_subtitle') }}</p>
        </div>
        <div>
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('orders.open_new_order') }}</span>
                </x-button>
            </a>
        </div>
    </div>

    <!-- Filtr paneli -->
    <x-card>
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <x-input 
                    name="search" 
                    placeholder="{{ __('orders.search_placeholder') }}" 
                    value="{{ request('search') }}"
                />
            </div>
            <div>
                <x-select name="status">
                    <option value="">{{ __('common.all_statuses') }}</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ (string) request('status') === (string) $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="category_id">
                    <option value="">{{ __('common.all_services') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="washer_id">
                    <option value="">{{ __('common.all_washers') }}</option>
                    @foreach($washers as $w)
                        <option value="{{ $w->id }}" {{ request('washer_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-input 
                    type="date" 
                    name="date_from" 
                    value="{{ request('date_from') }}" 
                    title="{{ __('orders.date_from') }}"
                />
            </div>
            <div class="flex items-center gap-2">
                <x-input 
                    type="date" 
                    name="date_to" 
                    value="{{ request('date_to') }}" 
                    title="{{ __('orders.date_to') }}"
                    class="w-full"
                />
                <x-button type="submit" variant="secondary" class="h-10 px-3">
                    <svg class="w-4 h-4 text-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </x-button>
                @if(request()->hasAny(['search', 'status', 'category_id', 'washer_id', 'date_from', 'date_to']))
                    <a href="{{ route('orders.index') }}" class="p-2 text-muted hover:text-danger rounded-lg hover:bg-danger-soft" title="{{ __('common.clear') }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Zakazlar jadvali -->
    <x-card class="p-0">
        <x-table :empty="$orders->isEmpty()" emptyText="{{ __('orders.empty_orders') }}">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">{{ __('orders.car_number') }}</th>
                <th class="px-4 py-3">{{ __('orders.service_type') }}</th>
                <th class="px-4 py-3">{{ __('common.price') }}</th>
                <th class="px-4 py-3">{{ __('common.users') }}</th>
                <th class="px-4 py-3">{{ __('common.branches') }}</th>
                <th class="px-4 py-3">{{ __('common.status') }}</th>
                <th class="px-4 py-3">{{ __('common.date') }}</th>
                <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
            </x-slot>

            @foreach($orders as $order)
                <tr class="hover:bg-table-hover transition-colors">
                    <td class="px-4 py-3 text-xs font-semibold text-muted">#{{ $order->id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center font-mono font-bold tracking-wider px-2 py-0.5 rounded bg-slate-900 text-amber-400 text-xs border border-slate-700 shadow-xs">
                                {{ $order->plate_number }}
                            </span>
                            @if($order->car_id)
                                <span class="text-xs text-primary" title="{{ __('cars.camera_info') }}">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 font-medium text-content">{{ $order->category->name }}</td>
                    <td class="px-4 py-3 font-semibold text-content whitespace-nowrap">{{ format_money($order->price) }}</td>
                    <td class="px-4 py-3 text-xs text-muted">
                        <p><span class="text-muted/70">{{ __('orders.washer') }}:</span> <strong class="text-content">{{ $order->washer->name }}</strong></p>
                        <p><span class="text-muted/70">{{ __('orders.operator') }}:</span> <span class="text-content">{{ $order->operator->name }}</span></p>
                    </td>
                    <td class="px-4 py-3 text-xs text-content">{{ $order->region->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $order->status->badgeClass() }}">
                            {{ $order->status->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-muted whitespace-nowrap">
                        <p>{{ format_datetime($order->created_at) }}</p>
                        @if($order->finished_at)
                            <p class="text-[11px] text-success font-medium">{{ __('orders.finish_washing') }}: {{ format_datetime($order->finished_at, 'HH:mm') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <!-- Status o'zgartirish tezkor tugmalari -->
                        <div class="flex items-center justify-end gap-1.5">
                            @if($order->status === \App\Enums\OrderStatus::NEW)
                                <form method="POST" action="{{ route('orders.status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::IN_PROGRESS->value }}">
                                    <x-button type="submit" variant="secondary" size="sm" class="text-warning hover:bg-warning-soft hover:border-warning/30">
                                        {{ __('orders.start_washing') }}
                                    </x-button>
                                </form>
                            @elseif($order->status === \App\Enums\OrderStatus::IN_PROGRESS)
                                <form method="POST" action="{{ route('orders.status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::DONE->value }}">
                                    <x-button type="submit" variant="success" size="sm">
                                        {{ __('orders.finish_washing') }}
                                    </x-button>
                                </form>
                            @endif

                            @if($order->status !== \App\Enums\OrderStatus::DONE && $order->status !== \App\Enums\OrderStatus::CANCELLED)
                                <form method="POST" action="{{ route('orders.status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::CANCELLED->value }}">
                                    <button type="submit" class="p-1.5 text-muted hover:text-danger rounded-lg hover:bg-danger-soft transition-colors" title="{{ __('orders.cancel_order') }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
