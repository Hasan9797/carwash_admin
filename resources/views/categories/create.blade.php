<x-app-layout>
    <x-slot name="header">
        Yangi xizmat turi qo'shish
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Yangi xizmat turi</h2>
                <p class="text-xs text-slate-500">Xizmat nomi va belgilangan narxi</p>
            </div>
            <a href="{{ route('categories.index') }}">
                <x-button variant="secondary" size="sm">Orqaga</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-5">
                @csrf

                <x-input label="Xizmat nomi" name="name" required placeholder="Masalan: Ekspress yuvish" />

                <x-input label="Narxi (so'mda)" name="price" type="number" step="1000" min="0" required placeholder="50000" />

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <label for="is_active" class="text-xs font-medium text-slate-700">Xizmat turi faol</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('categories.index') }}"><x-button variant="secondary">Bekor qilish</x-button></a>
                    <x-button type="submit" variant="primary">Saqlash</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
