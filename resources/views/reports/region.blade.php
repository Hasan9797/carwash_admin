<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Hisobotlar', 'url' => route('reports.category')],
    ['label' => 'Filiallar bo\'yicha'],
]">
    <x-slot name="header">
        Hisobotlar: Filiallar bo'yicha
    </x-slot>

    <!-- Hisobot bo'limlari navigatsiyasi (Tabs) -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto">
        <a href="{{ route('reports.category') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.category') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Xizmat turlari
        </a>
        <a href="{{ route('reports.region') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.region') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Filiallar bo'yicha
        </a>
        <a href="{{ route('reports.operator') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.operator') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Operatorlar bo'yicha
        </a>
        <a href="{{ route('reports.washer') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reports.washer') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Yuvuvchilar bo'yicha
        </a>
    </div>

    <!-- Filtr va Eksport paneli -->
    <x-card>
        <form method="GET" action="{{ route('reports.region') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <x-input type="date" label="Dan (Boshlanish sanasi)" name="date_from" value="{{ request('date_from') }}" />
            </div>
            <div>
                <x-input type="date" label="Gacha (Tugash sanasi)" name="date_to" value="{{ request('date_to') }}" />
            </div>
            <div>
                <x-select label="Filial" name="region_id">
                    <option value="">Barcha filiallar</option>
                    @foreach($regions as $r)
                        <option value="{{ $r->id }}" {{ request('region_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" class="w-full">Filtrlash</x-button>
                <a href="{{ route('reports.region.export', request()->query()) }}" class="inline-flex">
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
        <x-stat-card title="Jami zakazlar soni" :value="$totalOrders . ' ta'" color="blue" />
        <x-stat-card title="Jami tushum" :value="number_format((float)$totalAmount, 0, '.', ' ') . ' so\'m'" color="emerald" />
        <x-stat-card 
            title="O'rtacha chek" 
            :value="number_format($totalOrders > 0 ? (float)($totalAmount / $totalOrders) : 0, 0, '.', ' ') . ' so\'m'" 
            color="indigo" 
        />
    </div>

    <!-- Jadval -->
    <x-card class="p-0">
        <x-table :empty="$data->isEmpty()" emptyText="Ushbu oraliqda hisobot ma'lumotlari topilmadi">
            <x-slot name="head">
                <th class="px-4 py-3">Filial nomi</th>
                <th class="px-4 py-3 text-right">Zakazlar soni</th>
                <th class="px-4 py-3 text-right">Jami summa</th>
                <th class="px-4 py-3 text-right">O'rtacha chek</th>
            </x-slot>

            @foreach($data as $row)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $row->title }}</td>
                    <td class="px-4 py-3 text-right font-medium text-slate-700">{{ $row->orders_count }} ta</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-900 whitespace-nowrap">{{ number_format((float)$row->total_amount, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3 text-right font-mono text-slate-600 whitespace-nowrap">{{ number_format((float)$row->average_amount, 0, '.', ' ') }} so'm</td>
                </tr>
            @endforeach

            @if($data->isNotEmpty())
                <tr class="bg-slate-50 font-bold text-slate-900 border-t-2 border-slate-200">
                    <td class="px-4 py-3 text-xs uppercase tracking-wider">JAMI</td>
                    <td class="px-4 py-3 text-right">{{ $totalOrders }} ta</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap text-blue-700">{{ number_format((float)$totalAmount, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3 text-right font-mono whitespace-nowrap">{{ number_format($totalOrders > 0 ? (float)($totalAmount / $totalOrders) : 0, 0, '.', ' ') }} so'm</td>
                </tr>
            @endif
        </x-table>
    </x-card>
</x-app-layout>
