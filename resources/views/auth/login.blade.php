<!DOCTYPE html>
@php
    $availableThemes = array_keys(config('themes.available', []));
    $defaultTheme = config('themes.default', 'light');
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
<body class="h-full flex items-center justify-center p-4 bg-bg text-content relative">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('favicons/favicon.svg') }}" alt="Carwash Pro" width="60" height="60" class="w-[60px] h-[60px] mx-auto mb-3 shrink-0 shadow-md shadow-primary/20">
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
