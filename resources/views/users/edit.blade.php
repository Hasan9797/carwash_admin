<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.system_administration')],
    ['label' => __('common.users'), 'url' => route('users.index')],
    ['label' => __('users.editing', ['name' => $user->name])],
]">

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-content">{{ __('users.edit_title') }}</h2>
                <p class="text-xs text-muted">#{{ $user->id }} — {{ $user->name }}</p>
            </div>
            <a href="{{ route('users.index') }}">
                <x-button variant="secondary" size="sm">{{ __('common.back') }}</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input label="{{ __('users.full_name') }}" name="name" :value="$user->name" required />

                <x-input label="{{ __('users.email') }}" name="email" type="email" :value="$user->email" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="{{ __('users.role') }}" name="role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ (string) old('role', $user->role->value) === (string) $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="{{ __('users.phone') }}" name="phone" :value="$user->phone" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="{{ __('users.branch') }}" name="region_id">
                        <option value="">{{ __('users.unassigned_branch') }}</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ old('region_id', $user->region_id) == $reg->id ? 'selected' : '' }}>
                                {{ $reg->name }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="{{ __('users.password_edit_hint') }}" name="password" type="password" placeholder="••••••••" />
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-border text-primary focus:ring-primary/20 bg-surface">
                    <label for="is_active" class="text-xs font-medium text-content">{{ __('users.is_active') }}</label>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"><x-button variant="secondary">{{ __('common.cancel') }}</x-button></a>
                    <x-button type="submit" variant="primary">{{ __('users.save_changes') }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
