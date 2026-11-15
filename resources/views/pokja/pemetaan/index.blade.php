<x-app-layout>
    @php
        $getUniqueBadgeClass = function($name) {
            if (!$name) return 'background-color: rgba(148, 163, 184, 0.1); color: rgb(148, 163, 184); border-color: rgba(148, 163, 184, 0.2);';
            $hash = crc32($name);
            $hue = abs($hash) % 360;
            return "--hue: {$hue};";
        };
    @endphp
    <x-slot name="header">Pemetaan Siswa PKL</x-slot>

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-slate-600 dark:text-slate-400">Monitoring status penempatan siswa ke Industri dan Pembimbing.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('shared.pemetaan.maps') }}" class="px-4 py-2 text-sm whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4"></i> Lihat Peta
            </a>
            <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest leading-none mb-1">Terpetakan</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $terpetakan }} / {{ $totalSiswa }}</p>
            </div>
            <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <p class="text-[10px] text-amber-400 font-black uppercase tracking-widest leading-none mb-1">Belum Lengkap</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $totalSiswa - $terpetakan }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="glass-card mb-6 p-4">
        <form action="{{ route('pokja.pemetaan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white/50 dark:bg-slate-800/50 border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-800 dark:text-slate-200">
            </div>
            <div class="flex items-center gap-2">
                <label for="status" class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Status:</label>
                <select name="status" id="status" class="px-3 py-2 text-sm border border-slate-200/50 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                    <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="lengkap" {{ request('status') === 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                    <option value="belum-lengkap" {{ request('status') === 'belum-lengkap' ? 'selected' : '' }}>Belum Lengkap</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label for="konsentrasi_id" class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Program:</label>
                <select name="konsentrasi_id" id="konsentrasi_id" class="px-3 py-2 text-sm border border-slate-200/50 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                    <option value="semua">Semua</option>
                    @foreach($konsentrasiList as $k)
                        <option value="{{ $k->id }}" {{ request('konsentrasi_id') == $k->id ? 'selected' : '' }}>{{ $k->kode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Baris per Halaman:</label>
                <select name="per_page" id="per_page" class="px-3 py-2 text-sm border border-slate-200/50 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                FILTER
            </button>
        </form>
    </div>

    <div x-data="{
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.pemetaan-siswa-checkbox:not(:disabled)')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const checkable = document.querySelectorAll('.pemetaan-siswa-checkbox:not(:disabled)');
            this.selectAll = checkable.length > 0 && this.selectedIds.length === checkable.length;
        },
        confirmBulkDelete(e) {
            if (e) e.preventDefault();
            if (this.selectedIds.length === 0) return;
            const selectedBoxes = Array.from(document.querySelectorAll('.pemetaan-siswa-checkbox:checked'));
            const names = selectedBoxes.map(el => el.getAttribute('data-name') || ('Siswa #' + el.value));
            window.confirmDoubleDeletion({
                items: names,
                entityName: 'siswa',
                onConfirm: () => {
                    this.$refs.bulkDeleteForm.submit();
                }
            });
        },
        confirmBulkReset(e) {
            if (e) e.preventDefault();
            if (this.selectedIds.length === 0) return;
            const selectedBoxes = Array.from(document.querySelectorAll('.pemetaan-siswa-checkbox:checked'));
            const names = selectedBoxes.map(el => el.getAttribute('data-name') || ('Siswa #' + el.value));
            window.confirmDoubleDeletion({
                items: names,
                entityName: 'penempatan siswa',
                onConfirm: () => {
                    this.$refs.bulkResetForm.submit();
                }
            });
        }
    }">
        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-transition.opacity.duration.200ms class="flex flex-wrap items-center gap-3 mb-4" x-cloak>
            @if(auth()->user()->role !== 'kepala_sekolah')
                <!-- Hapus Siswa Form -->
                <form action="{{ route('pokja.pemetaan.bulk_destroy') }}" method="POST" x-ref="bulkDeleteForm" @submit="confirmBulkDelete">
                    @csrf
                    <input type="hidden" name="action" value="delete">
                    <template x-for="id in selectedIds" :key="'del-' + id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="px-4 py-2 text-sm whitespace-nowrap bg-red-600 hover:bg-red-500 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all gap-2 flex items-center transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Hapus Siswa Terpilih (<span x-text="selectedIds.length"></span>)
                    </button>
                </form>

                <!-- Reset Penempatan Form -->
                <form action="{{ route('pokja.pemetaan.bulk_destroy') }}" method="POST" x-ref="bulkResetForm" @submit="confirmBulkReset">
                    @csrf
                    <input type="hidden" name="action" value="reset_pemetaan">
                    <template x-for="id in selectedIds" :key="'rst-' + id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="px-4 py-2 text-sm whitespace-nowrap bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all gap-2 flex items-center transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset Penempatan (<span x-text="selectedIds.length"></span>)
                    </button>
                </form>
            @endif
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/50 dark:bg-slate-800/50 border-b border-slate-200/50 dark:border-slate-700/50">
                            @if(auth()->user()->role !== 'kepala_sekolah')
                                <th class="w-12 px-6 py-4">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                                </th>
                            @endif
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Siswa</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Tempat PKL (DUDI)</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Pembimbing Sekolah</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Pembimbing DUDI</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Status</th>
                            @if(auth()->user()->role !== 'kepala_sekolah')
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($siswas as $siswa)
                            <tr class="hover:bg-white dark:bg-slate-800/30 transition-colors">
                                @if(auth()->user()->role !== 'kepala_sekolah')
                                    <td class="px-6 py-4 whitespace-nowrap w-12">
                                        <input type="checkbox" 
                                               value="{{ $siswa->id }}" 
                                               data-name="{{ $siswa->nama_lengkap }}"
                                               x-model="selectedIds" 
                                               @change="updateSelectAll" 
                                               class="pemetaan-siswa-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $siswa->nama_lengkap }}</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tracking-wider">{{ $siswa->nis }} | {{ $siswa->konsentrasiKeahlian->nama ?? '-' }}</p>
                                        </div>
                                        @if($siswa->pengajuanPkl && $siswa->pengajuanPkl->bukti_balasan)
                                            <a href="{{ asset('storage/' . $siswa->pengajuanPkl->bukti_balasan) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold hover:bg-emerald-500/20 transition-all cursor-pointer" 
                                               title="Lihat Bukti Penerimaan Perusahaan">
                                                <i data-lucide="file-check" class="w-3 h-3"></i> Bukti DUDI
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($siswa->dudi)
                                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $siswa->dudi->nama }}</span>
                                    @else
                                        <span class="text-slate-600 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($siswa->pembimbingSekolah)
                                            <span style="{{ $getUniqueBadgeClass($siswa->pembimbingSekolah->nama_lengkap) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border dynamic-badge">
                                                KJ: {{ $siswa->pembimbingSekolah->nama_lengkap }}
                                            </span>
                                        @endif
                                        @if($siswa->pembimbingSekolahUmum)
                                            <span style="{{ $getUniqueBadgeClass($siswa->pembimbingSekolahUmum->nama_lengkap) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border dynamic-badge">
                                                UM: {{ $siswa->pembimbingSekolahUmum->nama_lengkap }}
                                            </span>
                                        @endif
                                        @if(!$siswa->pembimbingSekolah && !$siswa->pembimbingSekolahUmum)
                                            <span class="text-xs text-slate-500 dark:text-slate-400 italic">Belum ditentukan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($siswa->pembimbingDudi)
                                        <span style="{{ $getUniqueBadgeClass($siswa->pembimbingDudi->nama_lengkap) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border dynamic-badge">
                                            {{ $siswa->pembimbingDudi->nama_lengkap }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500 dark:text-slate-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $isComplete = $siswa->dudi_id && $siswa->pembimbing_sekolah_id && $siswa->pembimbing_sekolah_umum_id && $siswa->pembimbing_dudi_id;
                                    @endphp
                                    @if($isComplete)
                                        <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[9px] font-black border border-emerald-500/20 uppercase tracking-tighter">Lengkap</span>
                                    @else
                                        <span class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 text-[9px] font-black border border-red-500/20 uppercase tracking-tighter">Belum Lengkap</span>
                                    @endif
                                </td>
                                @if(auth()->user()->role !== 'kepala_sekolah')
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('pokja.siswa.edit', $siswa->id) }}" title="Edit Penempatan & Data" class="inline-flex items-center justify-center p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-600 dark:text-slate-400 hover:text-blue-500">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('pokja.siswa.destroy', $siswa->id) }}" method="POST" data-confirm-delete="{{ $siswa->nama_lengkap }}" data-entity="siswa" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Siswa" class="inline-flex items-center justify-center p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors text-slate-600 dark:text-slate-400 hover:text-red-500 cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 6 : 7 }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="users" class="w-12 h-12 text-slate-700"></i>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data siswa ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($siswas->hasPages())
                <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                    {{ $siswas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
