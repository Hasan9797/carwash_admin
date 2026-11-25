<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.system_administration')],
    ['label' => __('common.users')],
]">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-content">{{ __('users.list_title') }}</h2>
            <p class="text-xs text-muted">{{ __('users.list_subtitle') }}</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('users.new_user') }}</span>
                </x-button>
            </a>
        </div>
    </div>

    <!-- Filtr -->
    <x-card>
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <x-input name="search" placeholder="{{ __('users.search_placeholder') }}" value="{{ request('search') }}" />
            </div>
            <div>
                <x-select name="role">
                    <option value="">{{ __('common.all_roles') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ (string) request('role') === (string) $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="region_id">
                    <option value="">{{ __('common.all_branches') }}</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}" {{ request('region_id') == $reg->id ? 'selected' : '' }}>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary" class="w-full">{{ __('common.filter') }}</x-button>
                @if(request()->hasAny(['search', 'role', 'region_id']))
                    <a href="{{ route('users.index') }}" class="p-2 text-muted hover:text-danger rounded-lg hover:bg-danger-soft transition-colors" title="{{ __('common.clear') }}">
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
        <x-table :empty="$users->isEmpty()" emptyText="{{ __('users.empty_users') }}">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">{{ __('common.name') }}</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">{{ __('users.role') }}</th>
                <th class="px-4 py-3">{{ __('users.phone') }}</th>
                <th class="px-4 py-3">{{ __('reports.branch_select') }}</th>
                <th class="px-4 py-3">{{ __('common.status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
            </x-slot>

            @foreach($users as $u)
                <tr class="hover:bg-table-hover transition-colors">
                    <td class="px-4 py-3 text-xs text-muted">#{{ $u->id }}</td>
                    <td class="px-4 py-3 font-semibold text-content">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-sm text-muted">{{ $u->email }}</td>
                    <td class="px-4 py-3">
                        <x-badge :color="$u->isAdmin() ? 'indigo' : ($u->isOperator() ? 'blue' : 'slate')">
                            {{ $u->role->label() }}
                        </x-badge>
                    </td>
                    <td class="px-4 py-3 text-xs text-muted">
                        <p>{{ $u->phone ?? '—' }}</p>
                        @if($u->chat_id)
                            <p class="text-[11px] text-primary/80 font-mono">TG: {{ $u->chat_id }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-muted">{{ $u->region?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $u->is_active ? 'text-success' : 'text-muted' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-success' : 'bg-muted/40' }}"></span>
                            {{ $u->is_active ? __('common.active') : __('common.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $u->id) }}" class="p-1.5 text-muted hover:text-primary rounded-lg hover:bg-surface-2 transition-colors" title="{{ __('common.edit') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @if(auth()->id() !== $u->id)
                                <button 
                                    type="button" 
                                    @click="$dispatch('open-delete-modal', { action: '{{ route('users.destroy', $u->id) }}', title: '{{ __('users.delete_user_title') }}', message: '{{ __('users.delete_user_confirm', ['name' => addslashes($u->name)]) }}' })"
                                    class="p-1.5 text-muted hover:text-danger rounded-lg hover:bg-danger-soft transition-colors" 
                                    title="{{ __('common.delete') }}"
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
            <div class="px-6 py-4 border-t border-border">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
