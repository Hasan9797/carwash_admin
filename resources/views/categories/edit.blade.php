<x-app-layout>
    <x-slot name="header">
        Xizmat turini tahrirlash
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Xizmat turini tahrirlash</h2>
                <p class="text-xs text-slate-500">#{{ $category->id }} — {{ $category->name }}</p>
            </div>
            <a href="{{ route('categories.index') }}">
                <x-button variant="secondary" size="sm">Orqaga</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('categories.update', $category->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input label="Xizmat nomi" name="name" :value="$category->name" required />

                <x-input label="Narxi (so'mda)" name="price" type="number" step="1000" min="0" :value="$category->price" required />

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <label for="is_active" class="text-xs font-medium text-slate-700">Xizmat turi faol</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('categories.index') }}"><x-button variant="secondary">Bekor qilish</x-button></a>
                    <x-button type="submit" variant="primary">O'zgarishlarni saqlash</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
