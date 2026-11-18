<!DOCTYPE html>
<html lang="{{ html_lang() }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $title ?? null;
        if (! $pageTitle && ! empty($breadcrumbs) && is_array($breadcrumbs)) {
            $lastBreadcrumb = end($breadcrumbs);
            $pageTitle = $lastBreadcrumb['label'] ?? null;
        }
        $pageTitle = $pageTitle ?? ($header ?? __('common.dashboard'));
    @endphp
    <title>{{ $pageTitle }} - {{ config('app.name', 'Carwash') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.i18n = {
            locale: "{{ app()->getLocale() }}",
            currency: "{{ __('common.sum') }}",
            delete_confirm_title: "{{ __('common.confirm_delete_title') }}",
            delete_confirm_message: "{{ __('common.confirm_delete_message') }}",
            delete_confirm_warning: "{{ __('common.confirm_delete_warning') }}",
            delete_confirm_btn: "{{ __('common.confirm_delete_btn') }}",
            cancel: "{{ __('common.cancel') }}"
        };
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Mobil Sidebar Backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs lg:hidden"
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col"
        >
            <!-- Logo & Brand -->
            <div class="h-16 px-6 border-b border-border flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold text-lg text-content">
                    <div class="w-9 h-9 rounded-lg bg-primary text-primary-contrast flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span>Carwash <span class="text-primary">Pro</span></span>
                </a>
                <button class="lg:hidden p-1 text-muted hover:text-content" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a 
                    href="{{ route('dashboard') }}" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('common.dashboard') }}</span>
                </a>

                <a 
                    href="{{ route('orders.index') }}" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('orders.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('orders.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>{{ __('common.orders') }}</span>
                </a>

                <a 
                    href="{{ route('cars.index') }}" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('cars.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('cars.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('common.camera_cars') }}</span>
                </a>

                <a 
                    href="{{ route('reports.category') }}" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('reports.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>{{ __('common.reports') }}</span>
                </a>

                @if(auth()->user()?->isAdmin())
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-muted">{{ __('common.system_administration') }}</p>
                    </div>

                    <a 
                        href="{{ route('users.index') }}" 
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>{{ __('common.users') }}</span>
                    </a>

                    <a 
                        href="{{ route('categories.index') }}" 
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('categories.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>{{ __('common.categories') }}</span>
                    </a>

                    <a 
                        href="{{ route('regions.index') }}" 
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('regions.*') ? 'bg-primary-soft text-primary font-semibold' : 'text-muted hover:bg-surface-2 hover:text-content' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('regions.*') ? 'text-primary' : 'text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ __('common.regions') }}</span>
                    </a>
                @endif
            </div>

            <!-- Foydalanuvchi qismi (pastda) -->
            <div class="p-4 border-t border-border bg-surface-2/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-soft text-primary font-bold flex items-center justify-center text-sm border border-primary/20">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-content truncate">{{ auth()->user()->name ?? 'Foydalanuvchi' }}</p>
                        <p class="text-xs text-muted truncate">{{ auth()->user()->role?->label() }} {{ auth()->user()->region ? '• ' . auth()->user()->region->name : '' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Asosiy kontent maydoni -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Yuqori panel (Top bar) -->
            <header class="h-16 bg-surface border-b border-border sticky top-0 z-30">
                <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 h-full flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <button class="lg:hidden p-2 text-muted hover:text-content rounded-lg hover:bg-surface-2 shrink-0" @click="sidebarOpen = true">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="hidden sm:flex items-center gap-2 text-xs text-muted bg-surface-2 px-3 py-1.5 rounded-full border border-border/50">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="capitalize">{{ \Carbon\Carbon::now()->locale(carbon_locale())->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Til almashtirish (Language Switcher Dropdown) -->
                        <x-language-switcher />

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="h-9 flex items-center gap-1.5 px-2.5 sm:px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="hidden sm:inline">{{ __('common.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Sahifa asosiy qismi -->
            <main class="flex-1 overflow-y-auto">
                <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-6 space-y-6">
                    <!-- Flash Alerts -->
                    @if(session('success'))
                        <x-alert type="success">
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    @if(session('error'))
                        <x-alert type="danger">
                            {{ session('error') }}
                        </x-alert>
                    @endif

                    @if($errors->any())
                        <x-alert type="danger">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    <!-- Breadcrumbs (Sahifa sarlavhasidan oldin) -->
                    @php
                        $breadcrumbItems = $breadcrumbs ?? [];
                    @endphp
                    @if(View::hasSection('breadcrumbs'))
                        @yield('breadcrumbs')
                    @elseif(is_array($breadcrumbItems) && ! empty($breadcrumbItems))
                        <x-breadcrumbs :items="$breadcrumbItems" />
                    @elseif($breadcrumbItems instanceof \Illuminate\View\ComponentSlot && $breadcrumbItems->isNotEmpty())
                        {{ $breadcrumbItems }}
                    @elseif(is_string($breadcrumbItems) && trim($breadcrumbItems) !== '')
                        {{ $breadcrumbItems }}
                    @endif

                    {{ $slot }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Global Delete Confirm Modal (Alpine.js) -->
    <x-confirm-modal />

    @stack('scripts')
</body>
</html>
