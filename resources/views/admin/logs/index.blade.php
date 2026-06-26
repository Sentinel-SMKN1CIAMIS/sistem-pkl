<x-app-layout>
    <x-slot name="header">sistem log aktivitas</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Menampilkan riwayat aktivitas sistem terbaru.</p>
    </div>

    <!-- Filters Form -->
    <div class="glass-card p-4 mb-6" x-data="{ showAdvanced: {{ request()->hasAny(['action_type', 'role', 'start_date', 'end_date']) ? 'true' : 'false' }} }">
        <form action="{{ route('admin.logs.index') }}" method="GET" class="space-y-3">
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, IP, lokasi, atau username..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                </div>
                
                <div class="flex gap-2 items-center">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="px-3 py-2.5 text-slate-500 hover:text-blue-500 hover:bg-blue-500/10 rounded-xl transition-colors border border-slate-200/50 dark:border-slate-700/50" title="Filter Lanjutan">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </button>
                    <button type="submit" class="flex-1 sm:flex-initial px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm cursor-pointer">
                        Cari
                    </button>
                    @if(request()->anyFilled(['search', 'action_type', 'role', 'start_date', 'end_date']))
                        <a href="{{ route('admin.logs.index') }}" class="px-3 py-2.5 text-slate-500 hover:text-red-400 flex items-center justify-center transition-colors border border-slate-200/50 dark:border-slate-700 rounded-xl bg-slate-100/30" title="Reset">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Advanced Filters -->
            <div x-show="showAdvanced" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak
                 class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
                 
                <div class="flex items-center gap-2 mb-3 px-1">
                    <i data-lucide="filter" class="w-4 h-4 text-blue-500"></i>
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Filter Lanjutan</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-inner">
                    <!-- Action Type Filter -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tipe Aksi</label>
                        <select name="action_type" onchange="this.form.submit()" 
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                            <option value="">Semua Aksi</option>
                            <option value="LOGIN" {{ request('action_type') == 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                            <option value="LOGOUT" {{ request('action_type') == 'LOGOUT' ? 'selected' : '' }}>LOGOUT</option>
                            <option value="VIEW_PAGE" {{ request('action_type') == 'VIEW_PAGE' ? 'selected' : '' }}>VIEW PAGE</option>
                            <option value="CREATED" {{ request('action_type') == 'CREATED' ? 'selected' : '' }}>CREATED</option>
                            <option value="UPDATED" {{ request('action_type') == 'UPDATED' ? 'selected' : '' }}>UPDATED</option>
                            <option value="DELETED" {{ request('action_type') == 'DELETED' ? 'selected' : '' }}>DELETED</option>
                        </select>
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Role Pengguna</label>
                        <select name="role" onchange="this.form.submit()" 
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                            <option value="">Semua Role</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="pokja" {{ request('role') == 'pokja' ? 'selected' : '' }}>Pokja</option>
                            <option value="kaprog" {{ request('role') == 'kaprog' ? 'selected' : '' }}>Kaprog</option>
                            <option value="pembimbing_sekolah" {{ request('role') == 'pembimbing_sekolah' ? 'selected' : '' }}>Pembimbing Sekolah</option>
                            <option value="pembimbing_dudi" {{ request('role') == 'pembimbing_dudi' ? 'selected' : '' }}>Pembimbing Dudi</option>
                            <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="kepala_sekolah" {{ request('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                            <option value="system" {{ request('role') == 'system' ? 'selected' : '' }}>Sistem</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Rentang Tanggal</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()"
                                   class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                            <span class="text-xs text-slate-400 font-medium">s/d</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
                                   class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Waktu</th>
                        <th class="px-6 py-4 whitespace-nowrap">Pengguna</th>
                        <th class="px-6 py-4 whitespace-nowrap">Aksi</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 whitespace-nowrap">IP & Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors group">
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-mono text-xs whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/30">
                                        <i data-lucide="user" class="w-3 h-3 text-blue-400"></i>
                                    </div>
                                    <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $log->user->username ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $actionClasses = [
                                        'LOGIN' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'LOGOUT' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'VIEW_PAGE' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'CREATED' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'UPDATED' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'DELETED' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-black border {{ $actionClasses[$log->action] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-400' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 italic font-medium leading-relaxed max-w-lg break-words">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-slate-500 dark:text-slate-400 font-mono text-[10px]">{{ $log->ip_address }}</span>
                                    <span class="text-slate-600 dark:text-slate-400 font-medium text-[11px] whitespace-nowrap overflow-hidden text-ellipsis max-w-[150px]" title="{{ $log->location }}">
                                        {{ $log->location }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-12 h-12 text-slate-700"></i>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada riwayat aktivitas yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
