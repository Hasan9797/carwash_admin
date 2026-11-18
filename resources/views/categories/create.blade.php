<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.system_administration')],
    ['label' => __('common.categories'), 'url' => route('categories.index')],
    ['label' => __('categories.create_title')],
]">
    <x-slot name="header">
        {{ __('categories.header_create') }}
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-content">{{ __('categories.create_title') }}</h2>
                <p class="text-xs text-muted">{{ __('categories.create_subtitle') }}</p>
            </div>
            <a href="{{ route('categories.index') }}">
                <x-button variant="secondary" size="sm">{{ __('common.back') }}</x-button>
            </a>
        </div>

        <x-card>
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-5" x-data="{ activeTab: 'uz' }">
                @csrf

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
                        <x-input label="{{ __('categories.category_name') }} (UZ)" name="name_uz" :value="old('name_uz', old('name.uz'))" required placeholder="{{ __('categories.category_name_placeholder') }}" />

                        <div>
                            <label class="block text-xs font-semibold text-content mb-1.5">{{ __('categories.description') }} (UZ)</label>
                            <textarea name="description_uz" rows="3" placeholder="{{ __('categories.description_placeholder') }}" class="w-full text-sm rounded-lg border border-border bg-surface text-content placeholder:text-muted focus:border-primary focus:ring-primary/20 shadow-xs">{{ old('description_uz', old('description.uz')) }}</textarea>
                        </div>
                    </div>

                    <!-- UZ_CYRL Inputs -->
                    <div x-show="activeTab === 'uz_Cyrl'" style="display: none;" class="space-y-4">
                        <p class="text-[11px] text-muted italic">Кириллча номи киритилмаса, автоматик тарзда лотинчаси (UZ) кўрсатилади.</p>
                        <x-input label="{{ __('categories.category_name') }} (ЎЗ)" name="name_crl" :value="old('name_crl', old('name_uz_Cyrl', old('name.uz_Cyrl')))" placeholder="Масалан: Экспресс ювиш" />

                        <div>
                            <label class="block text-xs font-semibold text-content mb-1.5">{{ __('categories.description') }} (ЎЗ)</label>
                            <textarea name="description_uz_Cyrl" rows="3" placeholder="Хизмат ҳақида қисқача маълумот..." class="w-full text-sm rounded-lg border border-border bg-surface text-content placeholder:text-muted focus:border-primary focus:ring-primary/20 shadow-xs">{{ old('description_uz_Cyrl', old('description.uz_Cyrl')) }}</textarea>
                        </div>
                    </div>

                    <!-- RU Inputs -->
                    <div x-show="activeTab === 'ru'" style="display: none;" class="space-y-4">
                        <p class="text-[11px] text-muted italic">Русча номи киритилмаса, автоматик тарзда лотинчаси (UZ) кўрсатилади.</p>
                        <x-input label="{{ __('categories.category_name') }} (RU)" name="name_ru" :value="old('name_ru', old('name.ru'))" placeholder="Например: Экспресс мойка" />

                        <div>
                            <label class="block text-xs font-semibold text-content mb-1.5">{{ __('categories.description') }} (RU)</label>
                            <textarea name="description_ru" rows="3" placeholder="Краткое описание услуги..." class="w-full text-sm rounded-lg border border-border bg-surface text-content placeholder:text-muted focus:border-primary focus:ring-primary/20 shadow-xs">{{ old('description_ru', old('description.ru')) }}</textarea>
                        </div>
                    </div>
                </div>

                <x-input label="{{ __('categories.price') }}" name="price" type="number" step="1000" min="0" required placeholder="{{ __('categories.price_placeholder') }}" />

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-border text-primary focus:ring-primary/20 bg-surface">
                    <label for="is_active" class="text-xs font-medium text-content">{{ __('categories.is_active') }}</label>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <a href="{{ route('categories.index') }}"><x-button variant="secondary">{{ __('common.cancel') }}</x-button></a>
                    <x-button type="submit" variant="primary">{{ __('common.save') }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
