<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MAS-PKL') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        if (localStorage.theme === 'dark' || (localStorage.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased h-dvh overflow-hidden" x-data="{ sidebarOpen: false }">
    <div class="flex h-full w-full overflow-hidden">
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
           class="fixed inset-y-0 left-0 z-50 w-72 shrink-0 glass transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 border-r border-slate-200/50 dark:border-slate-700/50 flex flex-col">
        
        <div class="flex items-center justify-center p-6 border-b border-slate-200/50 dark:border-slate-700/50">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl">
                <h1 class="text-2xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-blue-600 dark:from-blue-300 dark:to-blue-500 drop-shadow-sm transition-all duration-300 hover:scale-[1.02] hover:drop-shadow-md cursor-default">MAS-PKL</h1>
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
                    theme: localStorage.theme || 'light',
                    open: false,
                    setTheme(val) {
                        this.theme = val;
                        localStorage.theme = val;
                        if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
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
                            
                            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
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

        // Prevent browser caching on back button (BFCache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Custom global override for standard window.alert
        window.alert = function(message) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Informasi',
                text: message,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                buttonsStyling: false,
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                customClass: {
                    confirmButton: 'px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-2xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-sm text-sm focus:outline-none cursor-pointer',
                    popup: 'rounded-3xl border-none font-sans shadow-2xl',
                    title: 'text-lg font-bold text-slate-900 dark:text-slate-100',
                    htmlContainer: 'text-sm font-medium leading-relaxed text-slate-600 dark:text-slate-300'
                }
            });
        };

        // Professional toast wrapper function
        window.showToast = function(message, type = 'success') {
            const isDark = document.documentElement.classList.contains('dark');
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            toast.fire({
                icon: type,
                title: message,
                customClass: {
                    popup: 'rounded-2xl border-none shadow-xl font-sans text-sm font-medium'
                }
            });
        };

        // Automatically convert native confirm() attributes on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', () => {
            // Forms with inline confirm in onsubmit
            document.querySelectorAll('form').forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match) {
                        const message = match[1];
                        form.removeAttribute('onsubmit');
                        form.addEventListener('submit', function(e) {
                            if (form.dataset.swalConfirmed === 'true') {
                                return;
                            }
                            e.preventDefault();
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                buttonsStyling: false,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f1f5f9' : '#1e293b',
                                customClass: {
                                    confirmButton: 'px-6 py-2.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-2xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-sm text-sm focus:outline-none cursor-pointer mr-3',
                                    cancelButton: 'px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-semibold rounded-2xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none cursor-pointer',
                                    popup: 'rounded-3xl border-none font-sans shadow-2xl',
                                    htmlContainer: 'text-sm font-medium leading-relaxed text-slate-600 dark:text-slate-300'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.dataset.swalConfirmed = 'true';
                                    form.submit();
                                }
                            });
                        });
                    }
                }
            });

            // Buttons/links with inline confirm in onclick
            document.querySelectorAll('[onclick]').forEach(el => {
                const onclickAttr = el.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('confirm(')) {
                    const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match) {
                        const message = match[1];
                        el.removeAttribute('onclick');
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                buttonsStyling: false,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f1f5f9' : '#1e293b',
                                customClass: {
                                    confirmButton: 'px-6 py-2.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-2xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-sm text-sm focus:outline-none cursor-pointer mr-3',
                                    cancelButton: 'px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-semibold rounded-2xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none cursor-pointer',
                                    popup: 'rounded-3xl border-none font-sans shadow-2xl',
                                    htmlContainer: 'text-sm font-medium leading-relaxed text-slate-600 dark:text-slate-300'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const form = el.closest('form');
                                    if (form) {
                                        form.dataset.swalConfirmed = 'true';
                                        form.submit();
                                    } else if (el.tagName === 'A') {
                                        window.location.href = el.href;
                                    }
                                }
                            });
                        });
                    }
                }
            });

            // Dynamic Password Show/Hide Toggle
            function initPasswordToggles() {
                document.querySelectorAll('input[type="password"]').forEach(input => {
                    if (input.dataset.passwordToggleInitialized) return;
                    input.dataset.passwordToggleInitialized = 'true';

                    // Check if parent already has a toggle button (e.g. custom layout)
                    const parent = input.parentElement;
                    if (parent && parent.querySelector('button')) {
                        return;
                    }

                    // Add padding-right to prevent text overlap
                    input.classList.add('pr-10');

                    // If parent is not relative, wrap input in a relative wrapper
                    let wrapper = parent;
                    if (!parent.classList.contains('relative')) {
                        wrapper = document.createElement('div');
                        wrapper.className = 'relative w-full';
                        input.parentNode.insertBefore(wrapper, input);
                        wrapper.appendChild(input);
                    }

                    // Create toggle button
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:text-slate-300 transition-colors focus:outline-none';
                    button.innerHTML = '<i data-lucide="eye" class="h-5 w-5"></i>';
                    wrapper.appendChild(button);

                    // Click event to toggle type and icon
                    let show = false;
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        show = !show;
                        input.type = show ? 'text' : 'password';
                        button.innerHTML = show 
                            ? '<i data-lucide="eye-off" class="h-5 w-5"></i>' 
                            : '<i data-lucide="eye" class="h-5 w-5"></i>';
                        
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    });
                });

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            initPasswordToggles();

            // Handle dynamically added password fields (e.g., in modals)
            const observer = new MutationObserver((mutations) => {
                let hasPassword = false;
                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            if (node.matches && node.matches('input[type="password"]')) {
                                hasPassword = true;
                            } else if (node.querySelector && node.querySelector('input[type="password"]')) {
                                hasPassword = true;
                            }
                        }
                    });
                });
                if (hasPassword) {
                    initPasswordToggles();
                }
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ session('success') }}", 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ session('error') }}", 'error');
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ $errors->first() }}", 'error');
            });
        </script>
    @endif
</body>
</html>
