<x-app-layout>
    <x-slot name="header">Kelola Pengguna Sistem</x-slot>

    <div x-data="{
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.user-checkbox:not(:disabled)')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const checkable = document.querySelectorAll('.user-checkbox:not(:disabled)');
            this.selectAll = checkable.length > 0 && this.selectedIds.length === checkable.length;
        },
        confirmBulkDelete(e) {
            e.preventDefault();
            if (this.selectedIds.length === 0) return;
            
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan menghapus ' + this.selectedIds.length + ' akun terpilih secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                customClass: {
                    confirmButton: 'px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-red-500/25 text-sm focus:outline-none cursor-pointer mr-3',
                    cancelButton: 'px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none cursor-pointer',
                    popup: 'rounded-2xl border border-slate-200/80 dark:border-slate-800/80 font-sans shadow-2xl',
                    htmlContainer: 'text-sm font-medium leading-relaxed'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$refs.bulkDeleteForm.submit();
                }
            });
        }
    }">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div class="flex flex-wrap items-center gap-4">
            <p class="text-slate-600 dark:text-slate-400">Total terdaftar: <span class="text-blue-400 font-bold">{{ $users->total() }}</span> akun.</p>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2 shadow-lg shadow-blue-500/20">
                <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                Tambah Pengguna
            </a>
        </div>
        
        <!-- Bulk Action Form -->
        <div x-show="selectedIds.length > 0" x-transition.opacity.duration.200ms class="flex items-center gap-3" x-cloak>
            <form action="{{ route('admin.users.bulk-destroy') }}" method="POST" x-ref="bulkDeleteForm" @submit="confirmBulkDelete">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all gap-2 flex items-center transform hover:scale-[1.02] active:scale-[0.98]">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                </button>
            </form>
        </div>
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
                        <th class="w-12 px-6 py-4">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 whitespace-nowrap">Username</th>
                        <th class="px-6 py-4 whitespace-nowrap">Role</th>
                        <th class="px-6 py-4 whitespace-nowrap">Terdaftar</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm font-normal">
                    @foreach($users as $user)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap w-12">
                                <input type="checkbox" 
                                       value="{{ $user->id }}" 
                                       x-model="selectedIds" 
                                       @change="updateSelectAll" 
                                       class="user-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer"
                                       @if($user->id === auth()->id()) disabled title="Anda tidak dapat menghapus akun Anda sendiri" @endif>
                            </td>
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
    </div>
</x-app-layout>
