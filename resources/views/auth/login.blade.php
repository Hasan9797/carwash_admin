<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tizimga kirish - {{ config('app.name', 'Carwash') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-blue-600 text-white items-center justify-center shadow-md shadow-blue-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Carwash boshqaruv paneli</h2>
            <p class="text-sm text-slate-500 mt-1">Avtomoyka jarayonlarini boshqarish tizimi</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
            @if(session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <x-input 
                    label="Email manzili" 
                    name="email" 
                    type="email" 
                    value="{{ old('email', 'admin@example.com') }}" 
                    placeholder="namuna@example.com"
                    required 
                    autofocus 
                />

                <x-input 
                    label="Parol" 
                    name="password" 
                    type="password" 
                    value="password"
                    placeholder="••••••••" 
                    required 
                />

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                        <span class="text-slate-600 font-medium">Meni eslab qol</span>
                    </label>
                </div>

                <x-button type="submit" variant="primary" class="w-full py-2.5">
                    Kirish
                </x-button>
            </form>

            <!-- Demo hisoblar ma'lumoti -->
            <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1.5">
                <p class="font-semibold text-slate-700">Sinov hisoblari:</p>
                <div class="flex justify-between bg-slate-50 p-2 rounded-lg">
                    <span>Admin: <strong class="text-slate-700">admin@example.com</strong></span>
                    <span>Parol: <strong class="text-slate-700">password</strong></span>
                </div>
                <div class="flex justify-between bg-slate-50 p-2 rounded-lg">
                    <span>Operator: <strong class="text-slate-700">operator1@example.com</strong></span>
                    <span>Parol: <strong class="text-slate-700">password</strong></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
