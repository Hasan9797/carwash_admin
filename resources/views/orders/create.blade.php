<x-app-layout :breadcrumbs="[
    ['label' => __('common.dashboard'), 'url' => route('dashboard')],
    ['label' => __('common.orders'), 'url' => route('orders.index')],
    ['label' => __('orders.new_order')],
]">
    <x-slot name="header">
        {{ __('orders.create_title') }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-content">{{ __('orders.create_title') }}</h2>
                <p class="text-xs text-muted">{{ __('orders.create_subtitle') }}</p>
            </div>
            <a href="{{ route('orders.index') }}">
                <x-button variant="secondary" size="sm" class="gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('common.back_to_list') }}</span>
                </x-button>
            </a>
        </div>

        <x-card x-data="orderCreateForm()">
            <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                @csrf

                <!-- Mashina raqamini kiritish usuli (Tab / Switcher) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-2">
                        {{ __('orders.step_1_plate_input') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3 p-1 bg-surface-2 rounded-xl border border-border">
                        <button 
                            type="button" 
                            @click="plateMode = 'camera'"
                            :class="plateMode === 'camera' ? 'bg-surface text-primary shadow-sm font-semibold border border-border/60' : 'text-muted hover:text-content'"
                            class="py-2.5 px-3 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('orders.select_from_camera') }} (<span x-text="recentCars.length"></span>)</span>
                        </button>
                        <button 
                            type="button" 
                            @click="plateMode = 'manual'"
                            :class="plateMode === 'manual' ? 'bg-surface text-primary shadow-sm font-semibold border border-border/60' : 'text-muted hover:text-content'"
                            class="py-2.5 px-3 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>{{ __('orders.manual_entry') }}</span>
                        </button>
                    </div>
                    <input type="hidden" name="plate_mode" :value="plateMode">
                </div>

                <!-- Usul 1: Kameradan o'tgan mashinalar ro'yxati -->
                <div x-show="plateMode === 'camera'" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-medium text-content">{{ __('orders.camera_select_hint') }}</label>
                        <input 
                            type="text" 
                            x-model="carSearch" 
                            placeholder="{{ __('common.search') }}..." 
                            class="text-xs px-2.5 py-1 rounded-lg border border-border bg-surface text-content placeholder:text-muted focus:outline-none focus:border-primary"
                        />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-56 overflow-y-auto p-1 border border-border rounded-xl bg-surface-2/40">
                        <template x-for="car in filteredCars" :key="car.id">
                            <button
                                type="button"
                                @click="selectedCarId = car.id"
                                :class="selectedCarId == car.id ? 'border-primary bg-primary-soft ring-2 ring-primary/20' : 'border-border bg-surface hover:border-primary/40'"
                                class="p-3 rounded-lg border text-left transition-all cursor-pointer"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-mono font-bold text-sm text-content" x-text="car.plate_number"></span>
                                    <span class="text-[10px] text-muted font-mono" x-text="car.time"></span>
                                </div>
                                <span class="text-[10px] text-muted" x-text="car.camera_id"></span>
                            </button>
                        </template>

                        <div x-show="filteredCars.length === 0" class="col-span-full py-8 text-center text-xs text-muted">
                            {{ __('orders.no_recent_camera_cars') }}
                        </div>
                    </div>
                    <input type="hidden" name="car_id" :value="selectedCarId">
                    @error('car_id')
                        <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Usul 2: Qo'lda raqam kiritish -->
                <div x-show="plateMode === 'manual'" class="space-y-2">
                    <x-input 
                        label="{{ __('orders.car_number_label') }}" 
                        name="plate_number" 
                        x-model="manualPlate" 
                        placeholder="01A777AA" 
                        class="uppercase font-mono text-base tracking-wider"
                    />
                    <p class="text-xs text-muted">{{ __('orders.plate_hint') }}</p>
                </div>

                <hr class="border-border">

                <!-- 2. Xizmat turi va Narx -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <x-select 
                            label="{{ __('orders.service_type') }}" 
                            name="category_id" 
                            x-model="selectedCategory"
                            required
                        >
                            <option value="">{{ __('orders.select_service') }}...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ $cat->name }} ({{ number_format((float)$cat->price, 0, '.', ' ') }} {{ __('common.sum') }})
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Avtomatik Narx Snapshot ko'rsatgichi -->
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">{{ __('orders.order_price') }}</label>
                        <div class="bg-primary-soft border border-primary/30 rounded-lg px-3.5 py-2 text-right">
                            <span class="text-base font-bold text-primary" x-text="currentPrice">0 {{ __('common.sum') }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Yuvuvchi xodimni tanlash -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-select 
                            label="{{ __('orders.washer_worker') }}" 
                            name="washer_id" 
                            required
                        >
                            <option value="">{{ __('orders.select_washer') }}...</option>
                            @foreach($washers as $w)
                                <option value="{{ $w->id }}" {{ old('washer_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }} {{ $w->region ? '(' . $w->region->name . ')' : '' }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Filial (Region) -->
                    <div>
                        @if(auth()->user()->isAdmin())
                            <x-select 
                                label="{{ __('common.regions') }}" 
                                name="region_id" 
                                required
                            >
                                @foreach($regions as $r)
                                    <option value="{{ $r->id }}" {{ old('region_id', auth()->user()->region_id) == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        @else
                            <label class="block text-xs font-medium text-muted mb-1.5">{{ __('common.regions') }}</label>
                            <div class="bg-surface-2 border border-border rounded-lg px-3.5 py-2 text-sm text-content font-medium">
                                {{ $operatorRegion?->name ?? __('common.branches') }}
                            </div>
                            <input type="hidden" name="region_id" value="{{ $operatorRegion?->id ?? 1 }}">
                        @endif
                    </div>
                </div>

                <!-- Amallar -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-border">
                    <a href="{{ route('orders.index') }}">
                        <x-button variant="secondary">{{ __('common.cancel') }}</x-button>
                    </a>
                    <x-button type="submit" variant="primary" class="gap-2 px-6">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ __('orders.save_order') }}</span>
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function orderCreateForm() {
            return {
                plateMode: @json(old('plate_mode', $selectedCarId ? 'camera' : ($recentCars->isNotEmpty() ? 'camera' : 'manual'))),
                selectedCarId: @json(old('car_id', $selectedCarId ?? '')),
                manualPlate: @json(old('plate_number', '')),
                selectedCategory: @json(old('category_id', (string)($categories->first()?->id ?? ''))),
                categoriesPrices: @json($categoriesPrices),
                recentCars: @json($recentCarsData),
                carSearch: '',

                get currentPrice() {
                    if (this.selectedCategory && this.categoriesPrices[this.selectedCategory]) {
                        return new Intl.NumberFormat('uz-UZ').format(this.categoriesPrices[this.selectedCategory]) + ' so\'m';
                    }
                    return '0 so\'m';
                },

                get filteredCars() {
                    if (!this.carSearch) return this.recentCars;
                    const query = this.carSearch.toUpperCase().replace(/\s+/g, '');
                    return this.recentCars.filter(c => c.plate_number.includes(query));
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
