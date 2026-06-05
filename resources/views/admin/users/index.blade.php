<x-app-layout>
    <x-slot name="header">Kelola Pengguna Sistem</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Total terdaftar: <span class="text-blue-400 font-bold">{{ $users->total() }}</span> akun.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Role Filter Tabs -->
    <div class="mb-6 flex gap-2 border-b border-slate-200/50 dark:border-slate-700/50 overflow-x-auto">
        <a href="{{ route('admin.users.index', ['filter' => 'all']) }}" 
           class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors {{ $filter === 'all' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Semua
        </a>
        <a href="{{ route('admin.users.index', ['filter' => 'super_admin']) }}" 
           class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors {{ $filter === 'super_admin' ? 'border-b-2 border-red-500 text-red-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Super Admin
        </a>
        <a href="{{ route('admin.users.index', ['filter' => 'guru']) }}" 
           class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors {{ $filter === 'guru' ? 'border-b-2 border-purple-500 text-purple-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Guru Pembimbing
        </a>
        <a href="{{ route('admin.users.index', ['filter' => 'siswa']) }}" 
           class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors {{ $filter === 'siswa' ? 'border-b-2 border-emerald-500 text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Siswa
        </a>
        <a href="{{ route('admin.users.index', ['filter' => 'other']) }}" 
           class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors {{ $filter === 'other' ? 'border-b-2 border-amber-500 text-amber-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Lainnya
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Username</th>
                        <th class="px-6 py-4 whitespace-nowrap">Role</th>
                        <th class="px-6 py-4 whitespace-nowrap">Terdaftar</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm font-normal">
                    @foreach($users as $user)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-800 dark:text-slate-200 font-bold font-mono">{{ $user->username }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleClasses = [
                                        'super_admin' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'pokja' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'siswa' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'pembimbing_sekolah' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'pembimbing_dudi' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-black border {{ $roleClasses[$user->role] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-400' }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div x-data="{ open: false }" class="relative flex justify-end" x-on:click.away="open = false">
                                    <button x-on:click="open = !open" class="p-1.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-8 w-32 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                         style="display: none;">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
