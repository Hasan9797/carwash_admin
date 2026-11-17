<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Tizim ma\'muriyati'],
    ['label' => 'Xizmat turlari'],
]">
    <x-slot name="header">
        Xizmat turlari (Kategoriyalar)
    </x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Yuvish xizmatlari</h2>
            <p class="text-xs text-slate-500">Avtomoyka xizmatlari va narxlari ro'yxati</p>
        </div>
        <div>
            <a href="{{ route('categories.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi xizmat turi</span>
                </x-button>
            </a>
        </div>
    </div>

    <x-card class="p-0">
        <x-table :empty="$categories->isEmpty()" emptyText="Xizmat turlari topilmadi">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Xizmat nomi</th>
                <th class="px-4 py-3">Narxi</th>
                <th class="px-4 py-3">Holati</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </x-slot>

            @foreach($categories as $cat)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-xs text-slate-400">#{{ $cat->id }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $cat->name }}</td>
                    <td class="px-4 py-3 font-mono font-semibold text-slate-900">{{ number_format((float)$cat->price, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $cat->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            {{ $cat->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('categories.edit', $cat->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Tahrirlash">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button 
                                type="button" 
                                @click="$dispatch('open-delete-modal', { action: '{{ route('categories.destroy', $cat->id) }}', title: 'Xizmat turini o\'chirish', message: 'Haqiqatan ham «{{ addslashes($cat->name) }}» xizmat turini o\'chirmoqchimisiz?' })"
                                class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" 
                                title="O'chirish"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
