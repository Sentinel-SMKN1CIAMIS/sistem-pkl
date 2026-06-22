@include('layouts.partials.navigation-data')

@php
    // Find and separate 'Pesan'
    $pesanIndex = -1;
    foreach($navItems as $k => $v) {
        if(($v['route'] ?? '') === 'pesan.index') {
            $pesanIndex = $k;
            break;
        }
    }
    
    $pesanItem = null;
    if ($pesanIndex !== -1) {
        $pesanItem = $navItems[$pesanIndex];
        unset($navItems[$pesanIndex]);
        $navItems = array_values($navItems);
    }

    // Assign Left, Center, and More items
    $leftItems = array_slice($navItems, 0, 2);
    $centerItem = isset($navItems[2]) ? $navItems[2] : null;
    $moreItems = array_slice($navItems, 3);
    
    // For Pembimbing Sekolah, the more items are moved to dashboard quick actions on mobile
    if (auth()->user()?->role === 'pembimbing_sekolah') {
        $moreItems = [];
    }
@endphp

<!-- Bottom Navigation Bar (Mobile Only) -->
<nav class="fixed bottom-0 left-0 right-0 z-50 glass-card border-t border-slate-200/50 dark:border-slate-700/50 pb-[env(safe-area-inset-bottom)] lg:hidden" id="bottom-nav">
    <div class="flex justify-between items-center h-16 px-4 relative">
        
        <!-- Left Items -->
        <div class="flex w-2/5 justify-around">
            @foreach($leftItems as $item)
                @php
                    $isActive = false;
                    if (isset($item['route']) && request()->routeIs($item['route'])) {
                        $isActive = true;
                    } elseif (isset($item['children'])) {
                        foreach ($item['children'] as $child) {
                            if (isset($child['route']) && request()->routeIs($child['route'])) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                    $hasChildren = isset($item['children']) && count($item['children']) > 0;
                @endphp
                @if($hasChildren)
                    <button @click="$dispatch('open-submenu-{{ Str::slug($item['name']) }}')"
                       class="flex flex-col items-center justify-center space-y-1 relative group {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }} focus:outline-none cursor-pointer">
                        <div class="p-1 rounded-xl transition-all duration-300 {{ $isActive ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6 {{ $isActive ? 'stroke-[2.5px]' : 'stroke-2' }}"></i>
                        </div>
                        <span class="text-[10px] tracking-wide truncate w-16 text-center {{ $isActive ? 'font-bold' : 'font-medium' }}">
                            {{ Str::limit($item['name'], 12, '') }}
                        </span>
                    </button>
                @else
                    <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}" 
                       class="flex flex-col items-center justify-center space-y-1 relative group {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                        <div class="p-1 rounded-xl transition-all duration-300 {{ $isActive ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6 {{ $isActive ? 'stroke-[2.5px]' : 'stroke-2' }}"></i>
                        </div>
                        <span class="text-[10px] tracking-wide truncate w-16 text-center {{ $isActive ? 'font-bold' : 'font-medium' }}">
                            {{ Str::limit($item['name'], 12, '') }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Center Floating Action Button -->
        @if($centerItem)
            @php
                $isCenterActive = false;
                if (isset($centerItem['route']) && request()->routeIs($centerItem['route'])) {
                    $isCenterActive = true;
                } elseif (isset($centerItem['children'])) {
                    foreach ($centerItem['children'] as $child) {
                        if (isset($child['route']) && request()->routeIs($child['route'])) {
                            $isCenterActive = true;
                            break;
                        }
                    }
                }
                $centerHasChildren = isset($centerItem['children']) && count($centerItem['children']) > 0;
            @endphp
            <div class="absolute left-1/2 -translate-x-1/2 -top-6 flex flex-col items-center">
                @if($centerHasChildren)
                    <button @click="$dispatch('open-submenu-{{ Str::slug($centerItem['name']) }}')" 
                       class="w-14 h-14 bg-blue-600 hover:bg-blue-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-600/30 border-4 border-slate-50 dark:border-slate-950 transition-transform transform active:scale-95 z-50 focus:outline-none cursor-pointer">
                        <i data-lucide="{{ $centerItem['icon'] }}" class="w-6 h-6 stroke-[2.5px]"></i>
                    </button>
                @else
                    <a href="{{ isset($centerItem['route']) ? route($centerItem['route']) : '#' }}" 
                       class="w-14 h-14 bg-blue-600 hover:bg-blue-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-600/30 border-4 border-slate-50 dark:border-slate-950 transition-transform transform active:scale-95 z-50">
                        <i data-lucide="{{ $centerItem['icon'] }}" class="w-6 h-6 stroke-[2.5px]"></i>
                    </a>
                @endif
                <span class="mt-1 text-[10px] font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $centerItem['name'] }}</span>
            </div>
        @endif

        <!-- Right Items -->
        <div class="flex w-2/5 justify-around">
            <!-- Pesan Item -->
            @if($pesanItem)
                @php
                    $isPesanActive = isset($pesanItem['route']) && request()->routeIs($pesanItem['route']);
                @endphp
                <a href="{{ isset($pesanItem['route']) ? route($pesanItem['route']) : '#' }}" 
                   class="flex flex-col items-center justify-center space-y-1 relative group {{ $isPesanActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-all duration-300 {{ $isPesanActive ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}">
                        <i data-lucide="{{ $pesanItem['icon'] }}" class="w-6 h-6 {{ $isPesanActive ? 'stroke-[2.5px]' : 'stroke-2' }}"></i>
                    </div>
                    <span class="text-[10px] tracking-wide truncate w-16 text-center {{ $isPesanActive ? 'font-bold' : 'font-medium' }}">
                        {{ Str::limit($pesanItem['name'], 12, '') }}
                    </span>
                </a>
            @endif

            <!-- Akun Button -->
            <button @click="$dispatch('open-akun-menu')" class="flex flex-col items-center justify-center space-y-1 relative group text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 focus:outline-none">
                <div class="p-1 rounded-xl transition-all duration-300">
                    <i data-lucide="user-circle" class="w-6 h-6 stroke-2"></i>
                </div>
                <span class="text-[10px] font-medium tracking-wide">Akun</span>
            </button>
        </div>
    </div>
</nav>

<!-- Akun Menu Sheet -->
<div x-data="{ openMenu: false, theme: localStorage.theme || 'light' }" 
     @open-akun-menu.window="openMenu = true"
     class="relative z-60 lg:hidden"
     x-cloak>
    
    <!-- Backdrop -->
    <div x-show="openMenu"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         @click="openMenu = false"></div>

    <!-- Bottom Sheet -->
    <div x-show="openMenu"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transform transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed inset-x-0 bottom-0 bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden"
         @click.away="openMenu = false">
        
        <!-- Drag Handle -->
        <div class="w-full flex justify-center pt-3 pb-2 cursor-pointer" @click="openMenu = false">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
        </div>

        <div class="px-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Menu Akun</h3>
            <button @click="openMenu = false" class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Sheet Content -->
        <div class="flex-1 overflow-y-auto p-4 pb-[calc(env(safe-area-inset-bottom)+1rem)] space-y-6">
            
            <!-- User Info -->
            <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                <img src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&background=3b82f6&color=fff' }}" 
                     alt="Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-slate-700">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()?->name ?? 'Guest User' }}</h4>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 tracking-wider uppercase">{{ str_replace('_', ' ', auth()->user()?->role ?? 'Guest') }}</span>
                </div>
            </div>

            <!-- Other Navigation Menus -->
            @if(count($moreItems) > 0)
                <div>
                    <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3 px-2">Menu Lainnya</h4>
                    <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                        @foreach($moreItems as $item)
                            @if(isset($item['children']))
                                <div class="col-span-4">
                                    <h5 class="text-[10px] font-bold text-slate-400 mb-2 px-2">{{ $item['name'] }}</h5>
                                    <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                                        @foreach($item['children'] as $child)
                                            <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}" class="flex flex-col items-center gap-2 group">
                                                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-100 dark:border-slate-700/50 group-hover:border-blue-200 dark:group-hover:bg-blue-50 transition-all">
                                                    <i data-lucide="{{ $child['icon'] ?? 'circle' }}" class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:text-blue-600"></i>
                                                </div>
                                                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300 text-center leading-tight w-full">{{ $child['name'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}" class="flex flex-col items-center gap-2 group">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-100 dark:border-slate-700/50 group-hover:border-blue-200 dark:group-hover:bg-blue-50 transition-all">
                                        <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:text-blue-600"></i>
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300 text-center leading-tight w-full">{{ $item['name'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Account Settings & Preferences -->
            <div>
                <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3 px-2">Pengaturan Akun</h4>
                <div class="space-y-2">
                    
                    @if(auth()->user()?->role === 'siswa')
                        <a href="{{ route('siswa.profile.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1 font-medium text-sm text-slate-700 dark:text-slate-200">Lihat Profil</div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </a>
                    @endif

                    <!-- Tentang Aplikasi Option -->
                    <button @click="openMenu = false; setTimeout(() => document.getElementById('aboutModal').showModal(), 300)" 
                        class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors text-left group">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 font-medium text-sm text-slate-700 dark:text-slate-200">Tentang Aplikasi</div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </button>

                    <!-- Theme Toggle Option -->
                    <button @click="
                            theme = theme === 'light' ? 'dark' : 'light';
                            localStorage.theme = theme;
                            if (theme === 'dark') document.documentElement.classList.add('dark');
                            else document.documentElement.classList.remove('dark');
                        " 
                        class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors text-left">
                        <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg">
                            <i x-show="theme === 'light'" data-lucide="moon" class="w-5 h-5"></i>
                            <i x-show="theme === 'dark'" data-lucide="sun" class="w-5 h-5" x-cloak></i>
                        </div>
                        <div class="flex-1 font-medium text-sm text-slate-700 dark:text-slate-200" x-text="theme === 'light' ? 'Mode Gelap' : 'Mode Terang'"></div>
                    </button>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors text-left group">
                            <div class="p-2 bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-900/40">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1 font-bold text-sm text-red-500">Keluar (Logout)</div>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Submenu Sheets (Dynamic) -->
@foreach(array_merge($leftItems, $centerItem ? [$centerItem] : []) as $item)
    @if(isset($item['children']) && count($item['children']) > 0)
        @php
            $slug = Str::slug($item['name']);
        @endphp
        <!-- Submenu Sheet for {{ $item['name'] }} -->
        <div x-data="{ openMenu: false }" 
             @open-submenu-{{ $slug }}.window="openMenu = true"
             class="relative z-[60] lg:hidden"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="openMenu"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="openMenu = false"></div>

            <!-- Bottom Sheet -->
            <div x-show="openMenu"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 class="fixed inset-x-0 bottom-0 bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
                 @click.away="openMenu = false">
                
                <!-- Drag Handle -->
                <div class="w-full flex justify-center pt-3 pb-2 cursor-pointer" @click="openMenu = false">
                    <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                </div>

                <div class="px-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $item['name'] }}</h3>
                    </div>
                    <button @click="openMenu = false" class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Sheet Content -->
                <div class="flex-1 overflow-y-auto p-6 pb-[calc(env(safe-area-inset-bottom)+1.5rem)]">
                    <div class="grid grid-cols-3 gap-x-2 gap-y-6">
                        @foreach($item['children'] as $child)
                            @php
                                $isChildActive = isset($child['route']) && request()->routeIs($child['route']);
                            @endphp
                            <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}" class="flex flex-col items-center gap-2 group text-center">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border transition-all duration-300
                                    {{ $isChildActive 
                                        ? 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400' 
                                        : 'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:border-blue-200 hover:bg-slate-100 dark:hover:bg-slate-800/80 group-hover:scale-105' }}">
                                    <i data-lucide="{{ $child['icon'] ?? 'circle' }}" class="w-6 h-6 {{ $isChildActive ? 'stroke-[2.5px]' : 'stroke-2' }}"></i>
                                </div>
                                <span class="text-[10px] tracking-wide font-medium leading-tight w-full {{ $isChildActive ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">
                                    {{ $child['name'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<style>
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #334155;
    }
</style>
