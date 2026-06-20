<x-app-layout>
    <x-slot name="header">Kelola Program Keahlian</x-slot>

    <style>
        .admin-header-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .admin-btn {
            width: 100% !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        @media (min-width: 768px) {
            .admin-header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .admin-btn {
                width: auto !important;
            }
        }
    </style>

    <div class="mb-6 admin-header-container">
        <div>
            <p class="text-slate-600 dark:text-slate-400">Daftar semua program keahlian yang terdaftar di sistem.</p>
            <p class="text-xs text-blue-500 dark:text-blue-400 mt-1 flex items-center gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5"></i> Drag handle <i data-lucide="grip-vertical" class="w-3.5 h-3.5 inline"></i> untuk mengurutkan data program keahlian secara dinamis.
            </p>
        </div>
        @if(auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('admin.program_keahlian.create') }}" class="admin-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Tambah Program
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="w-12 px-6 py-4"></th>
                        @endif
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Kode</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Program</th>
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="sortable-tbody" class="divide-y divide-slate-700/50">
                    @forelse($programs as $program)
                        <tr data-id="{{ $program->id }}" class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <td class="px-6 py-4 whitespace-nowrap text-slate-400 cursor-grab drag-handle w-12 text-center">
                                <i data-lucide="grip-vertical" class="w-4 h-4 mx-auto"></i>
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono text-blue-400">
                                    {{ $program->kode }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                {{ $program->nama }}
                            </td>
                            @if(auth()->user()->role !== 'kepala_sekolah')
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
                                        <a href="{{ route('admin.program_keahlian.edit', $program) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.program_keahlian.destroy', $program) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program {{ addslashes($program->nama_program) }}?')">
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
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 2 : 4 }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data program keahlian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination removed for drag-and-drop sorting -->
    </div>

    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(auth()->user()->role !== 'kepala_sekolah')
            const el = document.getElementById('sortable-tbody');
            if (el) {
                new Sortable(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-500/10',
                    dragClass: 'opacity-50',
                    onEnd: function() {
                        const ids = [];
                        el.querySelectorAll('tr[data-id]').forEach(tr => {
                            ids.push(tr.dataset.id);
                        });

                        fetch('{{ route("admin.program_keahlian.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: ids })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (window.showToast) {
                                    window.showToast(data.message, 'success');
                                } else {
                                    alert(data.message);
                                }
                            } else {
                                if (window.showToast) {
                                    window.showToast('Gagal memperbarui urutan.', 'error');
                                } else {
                                    alert('Gagal memperbarui urutan.');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (window.showToast) {
                                window.showToast('Terjadi kesalahan jaringan.', 'error');
                            } else {
                                alert('Terjadi kesalahan jaringan.');
                            }
                        });
                    }
                });
            }
            @endif
        });
    </script>
</x-app-layout>
