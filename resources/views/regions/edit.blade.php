<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.system_administration')],
    ['label' => __('common.branches'), 'url' => route('regions.index')],
    ['label' => __('regions.editing', ['name' => $region->name])],
]">
    <x-slot name="header">
        {{ __('regions.edit_title') }}
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-content">{{ __('regions.edit_title') }}</h2>
                <p class="text-xs text-muted">#{{ $region->id }} — {{ $region->name }}</p>
            </div>
            <a href="{{ route('regions.index') }}">
                <x-button variant="secondary" size="sm">{{ __('common.back') }}</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('regions.update', $region->id) }}" class="space-y-5" x-data="{ activeTab: 'uz' }">
                @csrf
                @method('PUT')

                <!-- Language Tabs (UZ | ЎЗ | RU) -->
                <div>
                    <div class="flex items-center gap-2 border-b border-border mb-4 pb-2 overflow-x-auto">
                        <button type="button" @click="activeTab = 'uz'" :class="activeTab === 'uz' ? 'bg-primary-soft text-primary border-primary font-semibold' : 'text-muted hover:text-content border-transparent'" class="px-3 py-1.5 text-xs rounded-lg border transition-colors flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <span>🇺🇿 O'zbekcha (UZ)</span>
                            <span class="text-[10px] text-danger font-bold">*</span>
                        </button>
                        <button type="button" @click="activeTab = 'uz_Cyrl'" :class="activeTab === 'uz_Cyrl' ? 'bg-primary-soft text-primary border-primary font-semibold' : 'text-muted hover:text-content border-transparent'" class="px-3 py-1.5 text-xs rounded-lg border transition-colors flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <span>🇺🇿 Ўзбекча (ЎЗ)</span>
                        </button>
                        <button type="button" @click="activeTab = 'ru'" :class="activeTab === 'ru' ? 'bg-primary-soft text-primary border-primary font-semibold' : 'text-muted hover:text-content border-transparent'" class="px-3 py-1.5 text-xs rounded-lg border transition-colors flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <span>🇷🇺 Русский (RU)</span>
                        </button>
                    </div>

                    <!-- UZ Inputs -->
                    <div x-show="activeTab === 'uz'" class="space-y-4">
                        <x-input label="{{ __('regions.region_name') }} (UZ)" name="name_uz" :value="old('name_uz', old('name.uz', $region->name_uz))" required placeholder="{{ __('regions.region_name_placeholder') }}" />
                    </div>

                    <!-- UZ_CYRL Inputs -->
                    <div x-show="activeTab === 'uz_Cyrl'" style="display: none;" class="space-y-4">
                        <p class="text-[11px] text-muted italic">Кириллча номи киритилмаса, автоматик тарзда лотинчаси (UZ) кўрсатилади.</p>
                        <x-input label="{{ __('regions.region_name') }} (ЎЗ)" name="name_uz_Cyrl" :value="old('name_uz_Cyrl', old('name_crl', old('name.uz_Cyrl', $region->name_crl)))" placeholder="Масалан: Сергели филиали" />
                    </div>

                    <!-- RU Inputs -->
                    <div x-show="activeTab === 'ru'" style="display: none;" class="space-y-4">
                        <p class="text-[11px] text-muted italic">Русча номи киритилмаса, автоматик тарзда лотинчаси (UZ) кўрсатилади.</p>
                        <x-input label="{{ __('regions.region_name') }} (RU)" name="name_ru" :value="old('name_ru', old('name.ru', $region->name_ru))" placeholder="Например: Сергелийский филиал" />
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $region->is_active) ? 'checked' : '' }} class="rounded border-border text-primary focus:ring-primary/20 bg-surface">
                    <label for="is_active" class="text-xs font-medium text-content">{{ __('regions.is_active') }}</label>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <a href="{{ route('regions.index') }}"><x-button variant="secondary">{{ __('common.cancel') }}</x-button></a>
                    <x-button type="submit" variant="primary">{{ __('regions.save_changes') }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
