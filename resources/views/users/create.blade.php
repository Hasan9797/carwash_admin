<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Tizim ma\'muriyati'],
    ['label' => 'Foydalanuvchilar', 'url' => route('users.index')],
    ['label' => 'Yangi foydalanuvchi'],
]">

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Yangi xodim qo'shish</h2>
                <p class="text-xs text-slate-500">Admin, operator yoki yuvuvchi profilini yaratish</p>
            </div>
            <a href="{{ route('users.index') }}">
                <x-button variant="secondary" size="sm">Orqaga</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <x-input label="F.I.SH (Ism-familiya)" name="name" required placeholder="Masalan: Anvar Aliyev" />

                <x-input label="Email manzili" name="email" type="email" required placeholder="anvar@example.com" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="Tizimdagi roli" name="role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ (string) old('role') === (string) $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Telefon raqami" name="phone" placeholder="+998901234567" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-select label="Filial (Hudud)" name="region_id">
                        <option value="">Filialga biriktirilmagan</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ old('region_id') == $reg->id ? 'selected' : '' }}>
                                {{ $reg->name }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Parol (Login qiluvchilar uchun)" name="password" type="password" placeholder="Kamida 6 belgi" />
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <label for="is_active" class="text-xs font-medium text-slate-700">Foydalanuvchi faol</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"><x-button variant="secondary">Bekor qilish</x-button></a>
                    <x-button type="submit" variant="primary">Saqlash</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
