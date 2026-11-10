<x-app-layout>
    <x-slot name="header">
        Foydalanuvchini tahrirlash
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Xodim ma'lumotlarini tahrirlash</h2>
                <p class="text-xs text-slate-500">#{{ $user->id }} — {{ $user->name }}</p>
            </div>
            <a href="{{ route('users.index') }}">
                <x-button variant="secondary" size="sm">Orqaga</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input label="F.I.SH (Ism-familiya)" name="name" :value="$user->name" required />

                <x-input label="Email manzili" name="email" type="email" :value="$user->email" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="Tizimdagi roli" name="role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ (string) old('role', $user->role->value) === (string) $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Telefon raqami" name="phone" :value="$user->phone" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="Filial (Hudud)" name="region_id">
                        <option value="">Filialga biriktirilmagan</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ old('region_id', $user->region_id) == $reg->id ? 'selected' : '' }}>
                                {{ $reg->name }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Yangi parol (O'zgartirmaslik uchun bo'sh qoldiring)" name="password" type="password" placeholder="••••••••" />
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <label for="is_active" class="text-xs font-medium text-slate-700">Foydalanuvchi faol</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"><x-button variant="secondary">Bekor qilish</x-button></a>
                    <x-button type="submit" variant="primary">O'zgarishlarni saqlash</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
