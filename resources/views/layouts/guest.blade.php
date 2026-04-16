<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem PKL') }}</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme Switcher Script (prevent FOUC) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col items-center justify-center relative overflow-hidden" x-data="{}">
    
    <!-- Theme Toggle -->
    <div class="absolute top-6 right-6 z-50">
        <div x-data="{
            theme: localStorage.theme || 'system',
            open: false,
            setTheme(val) {
                this.theme = val;
                localStorage.theme = val;
                if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                if(val === 'system') localStorage.removeItem('theme');
                this.open = false;
            }
        }" class="relative">
            <button @click="open = !open" @click.away="open = false" 
                    class="text-slate-700 dark:text-white relative p-2.5 rounded-full hover:bg-slate-200/50 dark:hover:bg-slate-800/50 transition-colors block glass-card">
                <i x-show="theme === 'light'" data-lucide="sun" class="w-5 h-5" x-cloak></i>
                <i x-show="theme === 'dark'" data-lucide="moon" class="w-5 h-5" x-cloak></i>
                <i x-show="theme === 'system'" data-lucide="monitor" class="w-5 h-5" x-cloak></i>
            </button>
            <div x-show="open"
                 x-transition.opacity.duration.200ms
                 class="absolute right-0 mt-2 w-36 glass-card border border-slate-200/50 dark:border-slate-700/50 py-2 rounded-xl text-sm font-medium z-50 text-slate-700 dark:text-slate-200" x-cloak>
                <button @click="setTheme('light')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="sun" class="w-4 h-4"></i> Light
                </button>
                <button @click="setTheme('dark')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="moon" class="w-4 h-4"></i> Dark
                </button>
                <button @click="setTheme('system')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="monitor" class="w-4 h-4"></i> System
                </button>
            </div>
        </div>
    </div>
    <!-- Background colors -->
    <div class="absolute inset-0 bg-slate-50 dark:bg-slate-950 pointer-events-none"></div>
    
    <div class="w-full relative z-10">
        {{ $slot }}
    </div>
    
    <!-- Lucide Icons mapping -->
</body>
</html>
