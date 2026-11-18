<x-app-layout :title="__('dashboard.title')">
    <x-slot name="header">
        {{ __('dashboard.title') }}
    </x-slot>

    <!-- Yuqori qism: Xush kelibsiz va tezkor harakatlar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-content">{{ __('dashboard.title') }}</h2>
            <p class="text-xs text-muted">{{ __('dashboard.greeting', ['name' => auth()->user()->name]) }} {{ __('dashboard.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('dashboard.open_order') }}</span>
                </x-button>
            </a>
            <a href="{{ route('cars.index') }}">
                <x-button variant="outline" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('dashboard.camera') }}</span>
                </x-button>
            </a>
        </div>
    </div>

    <!-- 6 ta Asosiy ko'rsatkich kartalari -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Barcha zakazlar soni -->
        <x-stat-card 
            :title="__('dashboard.total_orders')" 
            :value="$stats['total_orders_count'] . ' ' . __('dashboard.pcs')"
            color="primary"
            :subtext="__('dashboard.total_orders_subtext', ['count' => $stats['done_orders_count'] ?? 0])"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </x-slot>
        </x-stat-card>

        <!-- 2. Bugun yuvilgan mashinalar -->
        <x-stat-card 
            :title="__('dashboard.today_orders')" 
            :value="$stats['today_washed_count'] . ' ' . __('dashboard.pcs')"
            color="success"
            :subtext="__('dashboard.today_orders_subtext')"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-stat-card>

        <!-- 3. Umumiy tushum (Done) -->
        <x-stat-card 
            :title="__('dashboard.total_revenue')" 
            :value="format_money($stats['total_amount_done'])"
            color="primary"
            :subtext="__('dashboard.total_revenue_subtext')"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-stat-card>

        <!-- 4. Yuvuvchi xodimlar soni -->
        <x-stat-card 
            :title="__('dashboard.active_washers')" 
            :value="$stats['washers_count'] . ' ' . __('dashboard.persons')"
            color="success"
            :subtext="__('dashboard.active_washers_subtext')"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </x-slot>
        </x-stat-card>

        <!-- 5. Operatorlar soni -->
        <x-stat-card 
            :title="__('dashboard.operators_count')" 
            :value="$stats['operators_count'] . ' ' . __('dashboard.persons')"
            color="info"
            :subtext="__('dashboard.operators_subtext')"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </x-slot>
        </x-stat-card>

        <!-- 6. Adminlar soni -->
        <x-stat-card 
            :title="__('dashboard.admins_count')" 
            :value="$stats['admins_count'] . ' ' . __('dashboard.persons')"
            color="warning"
            :subtext="__('dashboard.admins_subtext')"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </x-slot>
        </x-stat-card>
    </div>

    <!-- Grafik: Oxirgi 7 kunlik yuvilgan mashinalar dinamikasi -->
    <x-card :title="__('dashboard.last_7_days_stats')">
        <div class="h-80 w-full" x-data="{
            chartData: {{ json_encode($chartData) }},
            init() {
                const ctx = this.$refs.canvas.getContext('2d');
                const computed = getComputedStyle(document.documentElement);
                const rawPrimary = (computed.getPropertyValue('--primary') || '37 99 235').trim();
                const primaryColor = 'rgb(' + rawPrimary + ')';
                const primaryChannels = rawPrimary.includes(',') ? rawPrimary : rawPrimary.split(/\s+/).join(', ');
                const primaryFill = 'rgba(' + primaryChannels + ', 0.12)';
                const rawBorder = (computed.getPropertyValue('--border') || '226 232 240').trim();
                const borderChannels = rawBorder.includes(',') ? rawBorder : rawBorder.split(/\s+/).join(', ');
                const gridColor = 'rgba(' + borderChannels + ', 0.6)';
                const rawMuted = (computed.getPropertyValue('--text-muted') || '100 116 139').trim();
                const tickColor = 'rgb(' + rawMuted + ')';

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.chartData.labels,
                        datasets: [{
                            label: '{{ addslashes(__('dashboard.washed_cars_chart_label')) }}',
                            data: this.chartData.values,
                            backgroundColor: primaryFill,
                            borderColor: primaryColor,
                            borderWidth: 2.5,
                            tension: 0.35,
                            fill: true,
                            pointBackgroundColor: primaryColor,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' {{ addslashes(__('dashboard.chart_cars_unit')) }}';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    color: tickColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            x: {
                                ticks: {
                                    color: tickColor
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }">
            <canvas x-ref="canvas"></canvas>
        </div>
    </x-card>
</x-app-layout>
