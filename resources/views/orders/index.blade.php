<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Zakazlar'],
]">
    <x-slot name="header">
        Zakazlar boshqaruvi
    </x-slot>

    <!-- Yuqori qism: Sarlavha va Yangi zakaz tugmasi -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Zakazlar ro'yxati</h2>
            <p class="text-xs text-slate-500">Mijozlar buyurtmalarini kuzatish va holatini boshqarish</p>
        </div>
        <div>
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi zakaz ochish</span>
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
                    placeholder="Raqam bo'yicha (masalan 01A777AA)..." 
                    value="{{ request('search') }}"
                />
            </div>
            <div>
                <x-select name="status">
                    <option value="">Barcha holatlar</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ (string) request('status') === (string) $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="category_id">
                    <option value="">Barcha xizmatlar</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="washer_id">
                    <option value="">Barcha yuvuvchilar</option>
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
                    title="Dan"
                />
            </div>
            <div class="flex items-center gap-2">
                <x-input 
                    type="date" 
                    name="date_to" 
                    value="{{ request('date_to') }}" 
                    title="Gacha"
                    class="w-full"
                />
                <x-button type="submit" variant="secondary" class="h-10 px-3">
                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </x-button>
                @if(request()->hasAny(['search', 'status', 'category_id', 'washer_id', 'date_from', 'date_to']))
                    <a href="{{ route('orders.index') }}" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Filtrni tozalash">
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
        <x-table :empty="$orders->isEmpty()" emptyText="Zakazlar ro'yxati bo'sh">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Mashina raqami</th>
                <th class="px-4 py-3">Xizmat turi</th>
                <th class="px-4 py-3">Summa</th>
                <th class="px-4 py-3">Xodimlar</th>
                <th class="px-4 py-3">Filial</th>
                <th class="px-4 py-3">Holati</th>
                <th class="px-4 py-3">Vaqti</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </x-slot>

            @foreach($orders as $order)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3 text-xs font-semibold text-slate-400">#{{ $order->id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center font-mono font-bold tracking-wider px-2 py-0.5 rounded bg-slate-900 text-amber-400 text-xs border border-slate-700 shadow-xs">
                                {{ $order->plate_number }}
                            </span>
                            @if($order->car_id)
                                <span class="text-xs text-blue-600" title="Kameradan aniqlangan">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $order->category->name }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">{{ number_format((float)$order->price, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3 text-xs text-slate-600">
                        <p><span class="text-slate-400">Yuvuvchi:</span> <strong class="text-slate-700">{{ $order->washer->name }}</strong></p>
                        <p><span class="text-slate-400">Operator:</span> {{ $order->operator->name }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">{{ $order->region->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $order->status->badgeClass() }}">
                            {{ $order->status->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                        <p>{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        @if($order->finished_at)
                            <p class="text-[11px] text-emerald-600 font-medium">Bajarildi: {{ $order->finished_at->format('H:i') }}</p>
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
                                    <x-button type="submit" variant="secondary" size="sm" class="text-amber-700 hover:bg-amber-100 hover:border-amber-300">
                                        Boshlash
                                    </x-button>
                                </form>
                            @elseif($order->status === \App\Enums\OrderStatus::IN_PROGRESS)
                                <form method="POST" action="{{ route('orders.status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::DONE->value }}">
                                    <x-button type="submit" variant="success" size="sm">
                                        Tugatish
                                    </x-button>
                                </form>
                            @endif

                            @if($order->status !== \App\Enums\OrderStatus::DONE && $order->status !== \App\Enums\OrderStatus::CANCELLED)
                                <form method="POST" action="{{ route('orders.status', $order->id) }}" onsubmit="return confirm('Zakazni bekor qilishga ishonchingiz komilmi?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::CANCELLED->value }}">
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Bekor qilish">
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
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
