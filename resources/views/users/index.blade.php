<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Tizim ma\'muriyati'],
    ['label' => 'Foydalanuvchilar'],
]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Xodimlar va foydalanuvchilar</h2>
            <p class="text-xs text-slate-500">Tizim administratorlari, operatorlar va yuvuvchilar ro'yxati</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi foydalanuvchi</span>
                </x-button>
            </a>
        </div>
    </div>

    <!-- Filtr -->
    <x-card>
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <x-input name="search" placeholder="Ism, email yoki telefon..." value="{{ request('search') }}" />
            </div>
            <div>
                <x-select name="role">
                    <option value="">Barcha rollar</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ (string) request('role') === (string) $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="region_id">
                    <option value="">Barcha filiallar</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}" {{ request('region_id') == $reg->id ? 'selected' : '' }}>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" class="w-full">Filtrlash</x-button>
                @if(request()->hasAny(['search', 'role', 'region_id']))
                    <a href="{{ route('users.index') }}" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Tozalash">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Jadval -->
    <x-card class="p-0">
        <x-table :empty="$users->isEmpty()" emptyText="Foydalanuvchilar topilmadi">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Ism</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Roli</th>
                <th class="px-4 py-3">Telefon</th>
                <th class="px-4 py-3">Filial</th>
                <th class="px-4 py-3">Holati</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </x-slot>

            @foreach($users as $u)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-xs text-slate-400">#{{ $u->id }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $u->email }}</td>
                    <td class="px-4 py-3">
                        <x-badge :color="$u->isAdmin() ? 'indigo' : ($u->isOperator() ? 'blue' : 'slate')">
                            {{ $u->role->label() }}
                        </x-badge>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">{{ $u->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-600">{{ $u->region?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $u->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            {{ $u->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $u->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Tahrirlash">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @if(auth()->id() !== $u->id)
                                <button 
                                    type="button" 
                                    @click="$dispatch('open-delete-modal', { action: '{{ route('users.destroy', $u->id) }}', title: 'Foydalanuvchini o\'chirish', message: 'Haqiqatan ham «{{ addslashes($u->name) }}» foydalanuvchisini o\'chirmoqchimisiz?' })"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" 
                                    title="O'chirish"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
