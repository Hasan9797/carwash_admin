<!DOCTYPE html>
@php
    $availableThemes = array_keys(config('themes.available', []));
    $defaultTheme = config('themes.default', 'light');

    $loginImage = 'images/blue-car-wash-stockcake.jpg';
    if (!file_exists(public_path($loginImage))) {
        $discovered = glob(public_path('images/*.{jpg,jpeg,png,webp,svg,JPG,JPEG,PNG,WEBP}'), GLOB_BRACE) ?: [];
        if (!empty($discovered)) {
            $loginImage = 'images/' . basename($discovered[0]);
        }
    }
@endphp
<html lang="{{ html_lang() }}" class="h-full bg-bg" data-theme="{{ $defaultTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-FOUC Theme Initialization (Login sahifasi uchun) -->
    <script>
        (function() {
            const availableThemes = @json($availableThemes);
            let theme = @json($defaultTheme);
            let localTheme = null;
            try { localTheme = localStorage.getItem('theme'); } catch (e) {}
            if (localTheme && availableThemes.includes(localTheme)) {
                theme = localTheme;
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches && availableThemes.includes('dark')) {
                theme = 'dark';
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <title>{{ __('auth.page_title') }} - {{ config('app.name', 'Carwash Pro') }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-bg text-content relative">
    <!-- Asosiy Split Karta (Desktopda 2 ustun, mobilda 1 ustun) -->
    <div class="w-full max-w-md lg:max-w-5xl mx-auto bg-surface rounded-2xl shadow-xl border border-border overflow-hidden grid lg:grid-cols-2">
        <!-- Chap ustun: Rasm va brend banner (Desktop) -->
        <div class="relative hidden lg:flex flex-col justify-end overflow-hidden bg-surface-2 min-h-[620px] select-none">
            <!-- Rasm -->
            <img 
                src="{{ asset($loginImage) }}" 
                alt="Carwash Pro" 
                loading="eager"
                fetchpriority="high"
                class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 hover:scale-105"
            >

            <!-- Gradient overlay: pastdan yuqoriga quyuq to'q gradient, primary tus bilan -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent opacity-95"></div>
            <div class="absolute inset-0 bg-primary/10 mix-blend-multiply pointer-events-none"></div>

            <!-- Overlay ustidagi yozuvlar -->
            <div class="relative z-10 p-8 lg:p-10 text-white space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-white text-xs font-semibold border border-white/20 tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ __('auth.banner_badge') }}</span>
                </div>
                <h3 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-md">
                    {{ __('auth.banner_title') }}
                </h3>
                <p class="text-sm text-slate-200/90 leading-relaxed drop-shadow-sm max-w-sm">
                    {{ __('auth.banner_subtitle') }}
                </p>
            </div>
        </div>

        <!-- O'ng ustun: Forma -->
        <div class="flex flex-col justify-center p-8 sm:p-10 lg:p-12 bg-surface">
            <div class="w-full max-w-sm mx-auto space-y-6">
                <!-- Logotip & Sarlavha -->
                <div>
                    <div class="inline-flex items-center gap-3 mb-6">
                        <img src="{{ asset('favicons/favicon.svg') }}" alt="Carwash Pro" width="48" height="48" class="w-12 h-12 shrink-0 shadow-md shadow-primary/20">
                        <span class="text-xl font-bold text-content tracking-tight">Carwash <span class="text-primary">Pro</span></span>
                    </div>

                    <h2 class="text-2xl font-bold text-content tracking-tight">{{ __('auth.app_title') }}</h2>
                    <p class="text-sm text-muted mt-1.5 leading-relaxed">{{ __('auth.app_subtitle') }}</p>
                </div>

                <!-- Xato xabarlari -->
                @if(session('error'))
                    <x-alert type="danger">{{ session('error') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    <x-input 
                        label="{{ __('auth.email') }}" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}" 
                        placeholder="namuna@example.com"
                        required 
                        autofocus 
                        class="h-11 sm:h-12"
                    />

                    <x-input 
                        label="{{ __('auth.password_label') }}" 
                        name="password" 
                        type="password" 
                        placeholder="••••••••" 
                        required 
                        class="h-11 sm:h-12"
                    />

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" value="1" class="rounded border-border text-primary focus:ring-primary/20 bg-surface">
                            <span class="text-muted font-medium">{{ __('auth.remember_me') }}</span>
                        </label>
                    </div>

                    <x-button type="submit" variant="primary" class="w-full h-11 sm:h-12 text-sm sm:text-base font-semibold shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 transition-all">
                        {{ __('auth.sign_in') }}
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
