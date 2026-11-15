<x-app-layout>
    <x-slot name="header">Kelola Konsentrasi Keahlian</x-slot>

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
            <p class="text-slate-600 dark:text-slate-400">Daftar konsentrasi keahlian per program keahlian.</p>
            <p class="text-xs text-blue-500 dark:text-blue-400 mt-1 flex items-center gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5"></i> Drag handle <i data-lucide="grip-vertical" class="w-3.5 h-3.5 inline"></i> untuk mengurutkan data konsentrasi keahlian secara dinamis.
            </p>
        </div>
        @if(auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('admin.konsentrasi_keahlian.create') }}" class="admin-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Tambah Konsentrasi
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.konsentrasi-checkbox:not(:disabled)')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const checkable = document.querySelectorAll('.konsentrasi-checkbox:not(:disabled)');
            this.selectAll = checkable.length > 0 && this.selectedIds.length === checkable.length;
        },
        confirmBulkDelete(e) {
            if (e) e.preventDefault();
            if (this.selectedIds.length === 0) return;
            const selectedBoxes = Array.from(document.querySelectorAll('.konsentrasi-checkbox:checked'));
            const names = selectedBoxes.map(el => el.getAttribute('data-name') || ('Konsentrasi #' + el.value));
            window.confirmDoubleDeletion({
                items: names,
                entityName: 'konsentrasi keahlian',
                onConfirm: () => {
                    this.$refs.bulkDeleteForm.submit();
                }
            });
        }
    }">
        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-transition.opacity.duration.200ms class="flex items-center gap-3 mb-4" x-cloak>
            <form action="{{ route('admin.konsentrasi_keahlian.bulk-destroy') }}" method="POST" x-ref="bulkDeleteForm" @submit="confirmBulkDelete">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-4 py-2 text-sm whitespace-nowrap bg-red-600 hover:bg-red-500 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all gap-2 flex items-center transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                </button>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <th class="w-12 px-6 py-4 text-center">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="w-12 px-6 py-4"></th>
                            @endif
                            <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Program Keahlian</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Kode</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Konsentrasi</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Durasi</th>
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="sortable-tbody" class="divide-y divide-slate-700/50">
                        @forelse($concentrations as $item)
                            <tr data-id="{{ $item->id }}" class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                                @if(auth()->user()->role !== 'kepala_sekolah')
                                <td class="px-6 py-4 whitespace-nowrap w-12 text-center">
                                    <input type="checkbox" 
                                           value="{{ $item->id }}" 
                                           data-name="{{ $item->nama }}"
                                           x-model="selectedIds" 
                                           @change="updateSelectAll" 
                                           class="konsentrasi-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-400 cursor-grab drag-handle w-12 text-center">
                                    <i data-lucide="grip-vertical" class="w-4 h-4 mx-auto"></i>
                                </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $item->programKeahlian->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono text-blue-400">
                                        {{ $item->kode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                    {{ $item->nama }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $item->durasi_pkl_bulan }} Bulan
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
                                            <a href="{{ route('admin.konsentrasi_keahlian.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.konsentrasi_keahlian.destroy', $item) }}" method="POST" data-confirm-delete="{{ $item->nama }}" data-entity="konsentrasi keahlian">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left cursor-pointer">
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
                                <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 4 : 7 }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                    Belum ada data konsentrasi keahlian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination removed for drag-and-drop sorting -->
        </div>
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

                        fetch('{{ route("admin.konsentrasi_keahlian.reorder") }}', {
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
