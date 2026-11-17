<x-app-layout :breadcrumbs="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Zakazlar', 'url' => route('orders.index')],
    ['label' => 'Yangi zakaz'],
]">
    <x-slot name="header">
        Yangi zakaz qabul qilish
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Yangi zakaz ochish</h2>
                <p class="text-xs text-slate-500">Kameradan o'tgan yoki qo'lda kiritilgan avtomobilga zakaz rasmiylashtirish</p>
            </div>
            <a href="{{ route('orders.index') }}">
                <x-button variant="secondary" size="sm" class="gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Orqaga qaytish</span>
                </x-button>
            </a>
        </div>

        <x-card x-data="orderCreateForm()">
            <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                @csrf

                <!-- Mashina raqamini kiritish usuli (Tab / Switcher) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        1. Avtomobil raqamini kiritish usuli
                    </label>
                    <div class="grid grid-cols-2 gap-3 p-1 bg-slate-100 rounded-xl">
                        <button 
                            type="button" 
                            @click="plateMode = 'camera'"
                            :class="plateMode === 'camera' ? 'bg-white text-blue-600 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Kameradan aniqlangan (<span x-text="recentCars.length"></span>)</span>
                        </button>
                        <button 
                            type="button" 
                            @click="plateMode = 'manual'"
                            :class="plateMode === 'manual' ? 'bg-white text-blue-600 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Qo'lda kiritish</span>
                        </button>
                    </div>
                    <input type="hidden" name="plate_mode" :value="plateMode">
                </div>

                <!-- Usul 1: Kameradan o'tgan mashinalar ro'yxati -->
                <div x-show="plateMode === 'camera'" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-medium text-slate-700">Oxirgi kelgan mashinalar</label>
                        <input 
                            type="text" 
                            x-model="carSearch" 
                            placeholder="Ro'yxatdan qidirish..." 
                            class="text-xs px-2.5 py-1 rounded-lg border border-slate-200 focus:outline-none focus:border-blue-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-56 overflow-y-auto p-1 border border-slate-200 rounded-xl bg-slate-50">
                        <template x-for="car in filteredCars" :key="car.id">
                            <button
                                type="button"
                                @click="selectedCarId = car.id"
                                :class="selectedCarId == car.id ? 'border-blue-600 bg-blue-50/80 ring-2 ring-blue-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                class="p-3 rounded-lg border text-left transition-all cursor-pointer"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-mono font-bold text-sm text-slate-900" x-text="car.plate_number"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="car.time"></span>
                                </div>
                                <span class="text-[10px] text-slate-500" x-text="car.camera_id"></span>
                            </button>
                        </template>

                        <div x-show="filteredCars.length === 0" class="col-span-full py-8 text-center text-xs text-slate-400">
                            Mos mashina topilmadi. Agar mashina kamerada ko'rinmagan bo'lsa, "Qo'lda kiritish" rejimidan foydalaning.
                        </div>
                    </div>
                    <input type="hidden" name="car_id" :value="selectedCarId">
                    @error('car_id')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Usul 2: Qo'lda raqam kiritish -->
                <div x-show="plateMode === 'manual'" class="space-y-2">
                    <x-input 
                        label="Avtomobil davlat raqami" 
                        name="plate_number" 
                        x-model="manualPlate" 
                        placeholder="Masalan: 01A777AA yoki 10B123BA" 
                        class="uppercase font-mono text-base tracking-wider"
                    />
                    <p class="text-xs text-slate-400">Raqamni katta harflarda va bo'shliqlarsiz kiriting.</p>
                </div>

                <hr class="border-slate-100">

                <!-- 2. Xizmat turi va Narx -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <x-select 
                            label="Yuvish xizmati turi" 
                            name="category_id" 
                            x-model="selectedCategory"
                            required
                        >
                            <option value="">Tanlang...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ $cat->name }} ({{ number_format((float)$cat->price, 0, '.', ' ') }} so'm)
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Avtomatik Narx Snapshot ko'rsatgichi -->
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Hisoblangan narx</label>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-3.5 py-2 text-right">
                            <span class="text-base font-bold text-blue-700" x-text="currentPrice">0 so'm</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Yuvuvchi xodimni tanlash -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-select 
                            label="Yuvuvchi xodim (Washer)" 
                            name="washer_id" 
                            required
                        >
                            <option value="">Yuvuvchini tanlang...</option>
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
                                label="Filial (Hudud)" 
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
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Filial (Avtomatik)</label>
                            <div class="bg-slate-100 border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-slate-700 font-medium">
                                {{ $operatorRegion?->name ?? 'Asosiy filial' }}
                            </div>
                            <input type="hidden" name="region_id" value="{{ $operatorRegion?->id ?? 1 }}">
                        @endif
                    </div>
                </div>

                <!-- Amallar -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('orders.index') }}">
                        <x-button variant="secondary">Bekor qilish</x-button>
                    </a>
                    <x-button type="submit" variant="primary" class="gap-2 px-6">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Zakazni rasmiylashtirish</span>
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
