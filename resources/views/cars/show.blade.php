<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Kameradagi mashinalar', 'url' => route('cars.index')],
    ['label' => $car->plate_number],
]">
    <x-slot name="header">
        Avtomobil tafsilotlari
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center font-mono font-bold tracking-wider px-3 py-1 rounded bg-slate-900 text-amber-400 text-base border border-slate-700 shadow-sm">
                    {{ $car->plate_number }}
                </span>
                <span class="text-xs text-slate-500">Kamera: {{ $car->camera_id ?? 'Noma\'lum' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.create', ['car_id' => $car->id]) }}">
                    <x-button variant="primary" size="sm">Yangi zakaz ochish</x-button>
                </a>
                <a href="{{ route('cars.index') }}">
                    <x-button variant="secondary" size="sm">Orqaga</x-button>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Asosiy ma'lumotlar -->
            <x-card title="Kamera ma'lumotlari">
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-slate-500">ID:</dt>
                        <dd class="font-medium text-slate-900">#{{ $car->id }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-slate-500">Davlat raqami:</dt>
                        <dd class="font-mono font-bold text-slate-900">{{ $car->plate_number }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-slate-500">Kamera ID:</dt>
                        <dd class="font-mono text-slate-700">{{ $car->camera_id ?? '—' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-slate-500">Aniqlangan sana va vaqt:</dt>
                        <dd class="font-medium text-slate-900">{{ $car->detected_at ? $car->detected_at->format('d.m.Y H:i:s') : '—' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="text-slate-500">Tizimga yozilgan vaqti:</dt>
                        <dd class="text-slate-700">{{ $car->created_at->format('d.m.Y H:i:s') }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Kamera Raw Payload -->
            <x-card title="Raw Payload (JSON)">
                <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-xs font-mono overflow-x-auto max-h-64">{{ json_encode($car->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </x-card>
        </div>

        <!-- Ushbu avtomobil zakazlari tarixi -->
        <x-card title="Ushbu avtomobil zakazlari tarixi" class="p-0">
            <x-table :empty="$car->orders->isEmpty()" emptyText="Hali bu mashinaga zakaz ochilmagan">
                <x-slot name="head">
                    <th class="px-4 py-3">Zakaz ID</th>
                    <th class="px-4 py-3">Xizmat turi</th>
                    <th class="px-4 py-3">Summa</th>
                    <th class="px-4 py-3">Operator</th>
                    <th class="px-4 py-3">Yuvuvchi</th>
                    <th class="px-4 py-3">Holati</th>
                    <th class="px-4 py-3">Sana</th>
                </x-slot>

                @foreach($car->orders as $ord)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-xs font-semibold text-slate-400">#{{ $ord->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $ord->category->name }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ number_format((float)$ord->price, 0, '.', ' ') }} so'm</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $ord->operator->name }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $ord->washer->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $ord->status->badgeClass() }}">
                                {{ $ord->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $ord->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>
</x-app-layout>
