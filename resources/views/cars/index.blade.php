<x-app-layout>
    <x-slot name="header">
        Kameradan aniqlangan mashinalar
    </x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kameradagi avtomobillar</h2>
            <p class="text-xs text-slate-500">Avtomatik kamera tizimidan kelgan so'nggi transport vositalari</p>
        </div>
    </div>

    <!-- Qidiruv paneli -->
    <x-card>
        <form method="GET" action="{{ route('cars.index') }}" class="flex items-center gap-3">
            <div class="flex-1">
                <x-input name="search" placeholder="Mashina raqami bo'yicha qidiruv (masalan: 01A777AA)..." value="{{ request('search') }}" />
            </div>
            <x-button type="submit" variant="secondary">Qidirish</x-button>
            @if(request('search'))
                <a href="{{ route('cars.index') }}" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Tozalash">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>
    </x-card>

    <x-card class="p-0">
        <x-table :empty="$cars->isEmpty()" emptyText="Mashinalar topilmadi">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Mashina raqami</th>
                <th class="px-4 py-3">Kamera ID</th>
                <th class="px-4 py-3">Rasm</th>
                <th class="px-4 py-3">Aniqlangan vaqt</th>
                <th class="px-4 py-3">Zakaz holati</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </x-slot>

            @foreach($cars as $car)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-xs text-slate-400">#{{ $car->id }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center font-mono font-bold tracking-wider px-2.5 py-1 rounded bg-slate-900 text-amber-400 text-xs border border-slate-700 shadow-xs">
                            {{ $car->plate_number }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-mono text-slate-600">{{ $car->camera_id ?? 'Noma\'lum' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        @if($car->image_path)
                            <a href="{{ asset('storage/' . $car->image_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                Rasmni ko'rish
                            </a>
                        @else
                            <span class="text-slate-400">Rasm yo'q</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">
                        {{ $car->detected_at ? $car->detected_at->format('d.m.Y H:i:s') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($car->latestOrder)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $car->latestOrder->status->badgeClass() }}">
                                Zakaz #{{ $car->latestOrder->id }}: {{ $car->latestOrder->status->label() }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                Zakaz berilmagan
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(! $car->latestOrder)
                                <a href="{{ route('orders.create', ['car_id' => $car->id]) }}">
                                    <x-button variant="primary" size="sm">
                                        Zakaz ochish
                                    </x-button>
                                </a>
                            @endif
                            <a href="{{ route('cars.show', $car->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Batafsil">
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
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $cars->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
