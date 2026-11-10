<x-app-layout>
    <x-slot name="header">
        Filialni tahrirlash
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Filialni tahrirlash</h2>
                <p class="text-xs text-slate-500">#{{ $region->id }} — {{ $region->name }}</p>
            </div>
            <a href="{{ route('regions.index') }}">
                <x-button variant="secondary" size="sm">Orqaga</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('regions.update', $region->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input label="Filial nomi" name="name" :value="$region->name" required />

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $region->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <label for="is_active" class="text-xs font-medium text-slate-700">Filial faol</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('regions.index') }}"><x-button variant="secondary">Bekor qilish</x-button></a>
                    <x-button type="submit" variant="primary">O'zgarishlarni saqlash</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
