@include('layouts.partials.navigation-data')

<nav class="space-y-2">
    @foreach ($navItems as $item)
        @if (isset($item['children']))
            @php
                // Check if any child is active
                $isActiveDropdown = false;
                foreach ($item['children'] as $child) {
                    if (request()->routeIs($child['route'])) {
                        $isActiveDropdown = true;
                        break;
                    }
                }
            @endphp
            <div x-data="{ open: {{ $isActiveDropdown ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        class="{{ $isActiveDropdown
                             ? 'text-blue-600 dark:text-blue-400 bg-blue-600/5 dark:bg-blue-500/5 font-semibold border-blue-500/10'
                             : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border-transparent'
                        }} w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group border">
                    <div class="flex items-center gap-3">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActiveDropdown ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-blue-500 transition-colors' }}"></i>
                        <span>{{ $item['name'] }}</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-800 space-y-1 pt-1">
                    @foreach ($item['children'] as $child)
                        @php
                            $isChildActive = request()->routeIs($child['route']);
                        @endphp
                        <a href="{{ route($child['route']) }}" 
                           class="{{ $isChildActive 
                                ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border border-transparent' 
                           }} flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition-all duration-200">
                            @if(isset($child['icon']))
                                <i data-lucide="{{ $child['icon'] }}" class="w-3.5 h-3.5 {{ $isChildActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}"></i>
                            @endif
                            <span>{{ $child['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}" 
               class="{{ $isActive 
                    ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-semibold' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border border-transparent' 
               }} flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-blue-500 transition-colors' }}"></i>
                <span>{{ $item['name'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
