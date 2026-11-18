<!DOCTYPE html>
@php
    $availableThemes = array_keys(config('themes.available', []));
@endphp
<html lang="{{ html_lang() }}" class="h-full bg-bg" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-FOUC Theme Initialization (Login sahifasi uchun localStorage'dan) -->
    <script>
        (function() {
            const availableThemes = @json($availableThemes);
            let theme = 'light';
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

    <title>{{ __('auth.page_title') }} - {{ config('app.name', 'Carwash') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 bg-bg text-content relative">
    <!-- Login sahifasida tema tanlagich -->
    <div class="fixed top-4 right-4 z-50">
        <x-theme-switcher />
    </div>

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-primary text-primary-contrast items-center justify-center shadow-md shadow-primary/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-content tracking-tight">{{ __('auth.app_title') }}</h2>
            <p class="text-sm text-muted mt-1">{{ __('auth.app_subtitle') }}</p>
        </div>

        <!-- Login Card -->
        <div class="bg-surface rounded-2xl shadow-sm border border-border p-8 space-y-6">
            @if(session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <x-input 
                    label="{{ __('auth.email') }}" 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    placeholder="namuna@example.com"
                    required 
                    autofocus 
                />

                <x-input 
                    label="{{ __('auth.password_label') }}" 
                    name="password" 
                    type="password" 
                    placeholder="••••••••" 
                    required 
                />

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="rounded border-border text-primary focus:ring-primary/20 bg-surface">
                        <span class="text-muted font-medium">{{ __('auth.remember_me') }}</span>
                    </label>
                </div>

                <x-button type="submit" variant="primary" class="w-full py-2.5">
                    {{ __('auth.sign_in') }}
                </x-button>
            </form>
        </div>
    </div>
</body>
</html>
