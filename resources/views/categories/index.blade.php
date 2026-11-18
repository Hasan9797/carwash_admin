<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.system_administration')],
    ['label' => __('common.categories')],
]">
    <x-slot name="header">
        {{ __('categories.header_title') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-content">{{ __('categories.list_title') }}</h2>
            <p class="text-xs text-muted">{{ __('categories.list_subtitle') }}</p>
        </div>
        <div>
            <a href="{{ route('categories.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('categories.new_category') }}</span>
                </x-button>
            </a>
        </div>
    </div>

    <x-card class="p-0">
        <x-table :empty="$categories->isEmpty()" emptyText="{{ __('categories.empty_categories') }}">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">{{ __('categories.category_name') }}</th>
                <th class="px-4 py-3">{{ __('common.price') }}</th>
                <th class="px-4 py-3">{{ __('common.status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
            </x-slot>

            @foreach($categories as $cat)
                <tr class="hover:bg-table-hover transition-colors">
                    <td class="px-4 py-3 text-xs text-muted">#{{ $cat->id }}</td>
                    <td class="px-4 py-3 font-semibold text-content">{{ $cat->name }}</td>
                    <td class="px-4 py-3 font-mono font-semibold text-content">{{ format_money($cat->price) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $cat->is_active ? 'text-success' : 'text-muted' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-success' : 'bg-muted/40' }}"></span>
                            {{ $cat->is_active ? __('common.active') : __('common.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('categories.edit', $cat->id) }}" class="p-1.5 text-muted hover:text-primary rounded-lg hover:bg-surface-2 transition-colors" title="{{ __('common.edit') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button 
                                type="button" 
                                @click="$dispatch('open-delete-modal', { action: '{{ route('categories.destroy', $cat->id) }}', title: '{{ __('categories.delete_title') }}', message: '{{ __('categories.delete_confirm', ['name' => addslashes($cat->name)]) }}' })"
                                class="p-1.5 text-muted hover:text-danger rounded-lg hover:bg-danger-soft transition-colors" 
                                title="{{ __('common.delete') }}"
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
            <div class="px-6 py-4 border-t border-border">
                {{ $categories->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
