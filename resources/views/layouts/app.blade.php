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
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-200 antialiased h-screen overflow-hidden flex" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 lg:hidden"
         @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-72 glass transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 border-r border-slate-700/50 flex flex-col">
        
        <div class="flex items-center justify-center p-6 border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i data-lucide="briefcase" class="w-6 h-6 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-300 tracking-tight">Simbiosis</h1>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-2">
            @include('layouts.partials.sidebar-menu')
        </div>

        <div class="p-4 border-t border-slate-700/50">
            <div class="flex items-center gap-3 glass-card p-3 rounded-xl border border-slate-700/50">
                <img src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&background=3b82f6&color=fff' }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-slate-600">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-100 truncate">{{ auth()->user()?->name ?? 'Guest User' }}</p>
                    <p class="text-xs text-slate-400 truncate capitalize">{{ str_replace('_', ' ', auth()->user()?->role ?? 'Guest') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        <!-- Top Navbar -->
        <header class="glass sticky top-0 z-30 h-16 border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-300 hover:text-white focus:outline-none">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            
            <div class="ml-auto flex items-center gap-4">
                <!-- Notifications -->
                @php
                    $unreadNotificationsCount = \App\Models\Notifikasi::where('to_user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <div class="relative">
                    <a href="{{ route('notifications.index') }}" class="text-slate-300 hover:text-white relative p-2 rounded-full hover:bg-slate-800/50 transition-colors block">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-[9px] font-bold text-white flex items-center justify-center rounded-full border border-slate-900">
                                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
                <!-- Page Header -->
                @if (isset($header))
                    <header class="mb-8">
                        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 tracking-tight">
                            {{ $header }}
                        </h2>
                    </header>
                @endif
                
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();
    </script>
</body>
</html>
