<x-app-layout>
    <x-slot name="header">Kelola Buku Panduan</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Unggah dan kelola buku panduan untuk siswa, DUDI, atau umum.</p>
        <a href="{{ route('admin.panduan.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
            <i data-lucide="upload-cloud" class="w-5 h-5"></i>
            Unggah Panduan
        </a>
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
                this.selectedIds = Array.from(document.querySelectorAll('.panduan-checkbox:not(:disabled)')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const checkable = document.querySelectorAll('.panduan-checkbox:not(:disabled)');
            this.selectAll = checkable.length > 0 && this.selectedIds.length === checkable.length;
        },
        confirmBulkDelete(e) {
            if (e) e.preventDefault();
            if (this.selectedIds.length === 0) return;
            const selectedBoxes = Array.from(document.querySelectorAll('.panduan-checkbox:checked'));
            const names = selectedBoxes.map(el => el.getAttribute('data-name') || ('Panduan #' + el.value));
            window.confirmDoubleDeletion({
                items: names,
                entityName: 'buku panduan',
                onConfirm: () => {
                    this.$refs.bulkDeleteForm.submit();
                }
            });
        }
    }">
        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-transition.opacity.duration.200ms class="flex items-center gap-3 mb-4" x-cloak>
            <form action="{{ route('admin.panduan.bulk-destroy') }}" method="POST" x-ref="bulkDeleteForm" @submit="confirmBulkDelete">
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
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                            <th class="w-12 px-6 py-4">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-4 whitespace-nowrap">Judul Panduan</th>
                            <th class="px-6 py-4 whitespace-nowrap">Tipe / Sasaran</th>
                            <th class="px-6 py-4 whitespace-nowrap">Konsentrasi Keahlian</th>
                            <th class="px-6 py-4 whitespace-nowrap">Tgl Upload</th>
                            <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50 text-sm">
                        @forelse($panduans as $item)
                            <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap w-12">
                                    <input type="checkbox" 
                                           value="{{ $item->id }}" 
                                           data-name="{{ $item->judul }}"
                                           x-model="selectedIds" 
                                           @change="updateSelectAll" 
                                           class="panduan-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-red-500/10 rounded-lg">
                                            <i data-lucide="file-text" class="w-5 h-5 text-red-400"></i>
                                        </div>
                                        <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $item->judul }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400">
                                        {{ $item->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $item->konsentrasiKeahlian?->nama ?? 'Semua Konsentrasi Keahlian' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y') }}
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
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 text-blue-500"></i>
                                                Lihat
                                            </a>
                                            <form action="{{ route('admin.panduan.destroy', $item) }}" method="POST" data-confirm-delete="{{ $item->judul }}" data-entity="buku panduan">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Belum ada buku panduan yang diunggah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
