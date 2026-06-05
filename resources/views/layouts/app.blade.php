<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem PKL') }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            height: 100dvh !important;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        [x-cloak] { display: none !important; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased h-dvh overflow-hidden flex" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
         @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-72 glass transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 border-r border-slate-200/50 dark:border-slate-700/50 flex flex-col">
        
        <div class="flex items-center justify-center p-6 border-b border-slate-200/50 dark:border-slate-700/50">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <h1 class="text-2xl font-bold text-gradient tracking-tight">MAS-PKL</h1>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-2">
            @include('layouts.partials.sidebar-menu')
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        <!-- Top Navbar -->
        <header class="glass sticky top-0 z-30 h-16 border-b border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-700 hover:text-slate-900 dark:text-white focus:outline-none">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            
            <div class="ml-auto flex items-center gap-4">
                <!-- Theme Toggle -->
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
                            class="text-slate-700 hover:text-slate-900 dark:text-white relative p-2 rounded-full hover:bg-white/50 dark:hover:bg-slate-800/50 transition-colors block">
                        <i x-show="theme === 'light'" data-lucide="sun" class="w-5 h-5" x-cloak></i>
                        <i x-show="theme === 'dark'" data-lucide="moon" class="w-5 h-5" x-cloak></i>
                        <i x-show="theme === 'system'" data-lucide="monitor" class="w-5 h-5" x-cloak></i>
                    </button>
                    <!-- Dropdown -->
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

                <!-- Notifications -->
                @php
                    $unreadNotificationsCount = \App\Models\Notifikasi::where('to_user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <div class="relative">
                    <a href="{{ route('notifications.index') }}" class="text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white relative p-2 rounded-full hover:bg-white/50 dark:bg-slate-800/50 transition-colors block">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-[9px] font-bold text-slate-900 dark:text-white flex items-center justify-center rounded-full border border-slate-900">
                                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ profileMenuOpen: false }">
                    <button @click="profileMenuOpen = !profileMenuOpen" @click.away="profileMenuOpen = false" 
                            class="flex items-center gap-3 p-1.5 pr-3 rounded-full hover:bg-white/50 dark:hover:bg-slate-800/50 transition-colors duration-200 focus:outline-none group border border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50">
                        <div class="relative">
                            <img src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&background=3b82f6&color=fff' }}" 
                                 alt="Avatar" 
                                 class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700 group-hover:scale-105 transition-transform duration-200">
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                        </div>
                        
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 leading-tight truncate max-w-[150px]">{{ auth()->user()?->name ?? 'Guest User' }}</span>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 tracking-wider uppercase mt-0.5">{{ str_replace('_', ' ', auth()->user()?->role ?? 'Guest') }}</span>
                        </div>
                        
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 group-hover:text-slate-600 dark:group-hover:text-slate-300" :class="profileMenuOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileMenuOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute right-0 mt-2 w-52 glass-card border border-slate-200/50 dark:border-slate-700/50 rounded-2xl overflow-hidden z-50 shadow-xl shadow-slate-100/50 dark:shadow-none" 
                         x-cloak>
                        
                        <div class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-200/50 dark:border-slate-700/50">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()?->name ?? 'Guest User' }}</p>
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 tracking-wider uppercase mt-0.5">{{ str_replace('_', ' ', auth()->user()?->role ?? 'Guest') }}</p>
                        </div>

                        <div class="p-1.5 space-y-1">
                            @if(auth()->user()?->role === 'siswa')
                                <a href="{{ route('siswa.profile.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors group/item">
                                    <i data-lucide="user-circle" class="w-4 h-4 text-slate-400 group-hover/item:text-slate-600 dark:group-hover/item:text-slate-300"></i>
                                    Lihat Profil
                                </a>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none group/item">
                                    <i data-lucide="log-out" class="w-4 h-4 text-red-400 group-hover/item:text-red-500"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl animate-fade-in-up">
                <!-- Page Header -->
                @if (isset($header))
                    <header class="mb-8">
                        <h2 class="text-3xl font-bold text-gradient tracking-tight">
                            {{ $header }}
                        </h2>
                    </header>
                @endif
                
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    <script>
        // Initialize Lucide icons after page load and after any pushed scripts
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Dynamic Ellipsis Dropdown positioning (Drop-Up)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            
            // Only apply to ellipsis dropdown buttons
            if (!btn.querySelector('.lucide-more-vertical') && !btn.querySelector('[data-lucide="more-vertical"]')) return;
            
            const container = btn.closest('.relative');
            if (!container) return;
            
            const dropdown = container.querySelector('.absolute:not(button)');
            if (!dropdown) return;
            
            // Calculate synchronously based on the button's position to prevent split-second scrollbar flicker
            const btnRect = btn.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            
            // Find any scrollable parent container or table that might clip the dropdown
            const scrollParent = container.closest('.overflow-x-auto') || container.closest('.overflow-y-auto') || container.closest('table');
            let shouldDropUp = false;
            const threshold = 160; // Safe estimate for dropdown menu height
            
            if (scrollParent) {
                const parentRect = scrollParent.getBoundingClientRect();
                const spaceBelowParent = parentRect.bottom - btnRect.bottom;
                const spaceBelowViewport = viewportHeight - btnRect.bottom;
                
                if (spaceBelowParent < threshold || spaceBelowViewport < threshold) {
                    shouldDropUp = true;
                }
            } else {
                const spaceBelowViewport = viewportHeight - btnRect.bottom;
                if (spaceBelowViewport < threshold) {
                    shouldDropUp = true;
                }
            }
            
            if (shouldDropUp) {
                dropdown.style.top = 'auto';
                dropdown.style.bottom = '100%';
                dropdown.style.marginTop = '0px';
                dropdown.style.marginBottom = '8px';
            } else {
                dropdown.style.top = '';
                dropdown.style.bottom = '';
                dropdown.style.marginTop = '';
                dropdown.style.marginBottom = '';
            }
        });
        
        // Unsaved changes warning
        const dirtyForms = new Set();
        
        ['input', 'change'].forEach(eventType => {
            document.addEventListener(eventType, (e) => {
                const form = e.target.closest('form');
                if (form && form.method.toUpperCase() === 'POST' && !form.classList.contains('ignore-dirty')) {
                    dirtyForms.add(form);
                }
            });
        });

        document.addEventListener('submit', (e) => {
            if (dirtyForms.has(e.target)) {
                dirtyForms.delete(e.target);
            }
        });

        window.addEventListener('beforeunload', (e) => {
            if (dirtyForms.size > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>
</body>
</html>
