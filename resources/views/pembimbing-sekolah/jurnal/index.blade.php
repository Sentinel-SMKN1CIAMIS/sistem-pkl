<x-app-layout>
    <x-slot name="header">Monitoring Jurnal Siswa</x-slot>

    <div x-data="{ imageModalOpen: false, modalImageUrl: '', filterOpen: false }">
        <div class="mb-6 space-y-4">
            {{-- Info banner berdasarkan tipe guru --}}
            @if($tipe !== 'kejuruan' && $tipe !== 'produktif')
            <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-semibold text-blue-700 dark:text-blue-300">Mode: Guru {{ $tipe === 'umum' ? 'Umum' : ucfirst($tipe) }}</p>
                    <p class="text-blue-600 dark:text-blue-400 text-xs mt-0.5">
                        Menampilkan jurnal yang memiliki CP mengandung:
                        <strong>"{{ $teacher->mapel_cp ?? '-' }}"</strong>.
                        Gunakan pencarian untuk menyaring lebih lanjut.
                    </p>
                </div>
            </div>
            @endif

            {{-- Search bar and Filter Button --}}
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" form="searchForm"
                           placeholder="Cari berdasarkan nama siswa, NIS, atau CP..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <button type="submit" form="searchForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl transition-all">
                    Cari
                </button>
                
                {{-- Filter Toggle Button --}}
                <button type="button" @click="filterOpen = !filterOpen"
                        class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2"
                        :class="filterOpen ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600'">
                    <i data-lucide="filter" class="w-4 h-4"></i> 
                    Filter
                    @if(request()->hasAny(['status', 'tanggal_dari', 'tanggal_sampai']))
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>

                @if(request()->hasAny(['search', 'status', 'tanggal_dari', 'tanggal_sampai']))
                <a href="{{ route('pembimbing_sekolah.jurnal.index') }}" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-all flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </a>
                @endif
            </div>

            {{-- Hidden Search Form --}}
            <form id="searchForm" method="GET" action="{{ route('pembimbing_sekolah.jurnal.index') }}" class="hidden">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
            </form>

            {{-- Filter Panel (Collapsible) --}}
            <div x-show="filterOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 style="display: none;"
                 class="glass-card p-4">
                <form id="filterForm" method="GET" action="{{ route('pembimbing_sekolah.jurnal.index') }}">
                    {{-- Preserve search value --}}
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Filter Status --}}
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 block mb-2">Status Approval</label>
                            <select name="status" 
                                    onchange="document.getElementById('filterForm').submit()"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Filter Tanggal Dari --}}
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 block mb-2">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                                   onchange="document.getElementById('filterForm').submit()"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>

                        {{-- Filter Tanggal Sampai --}}
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 block mb-2">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                                   onchange="document.getElementById('filterForm').submit()"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-400 font-bold">
                            {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ $item->siswa->nama_lengkap }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>
                        <div class="ml-auto">
                             @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    'valid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'invalid' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $statusClasses[$item->status] }}">
                                {{ $item->status }}
                            </span>
                        </div>
                    </div>

                    <div class="pl-14">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400">
                                {{ $item->kompetensi?->nama ?? 'Tidak Ada Kompetensi' }}
                            </span>
                            @if($item->tujuanPembelajaran)
                                <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-[10px] text-blue-600 dark:text-blue-300 font-medium">
                                    TP: {{ $item->tujuanPembelajaran->tp ?? $item->tujuanPembelajaran->nama }}
                                </span>
                            @endif
                            @if($item->cp)
                            <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 text-[10px] text-amber-600 dark:text-amber-400">
                                CP: {{ $item->cp }}
                            </span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>

                        @if($item->foto_path)
                            <div class="mb-4">
                                <button type="button" @click="modalImageUrl = '{{ asset('storage/' . $item->foto_path) }}'; imageModalOpen = true" class="inline-flex items-center gap-2 text-xs text-blue-400 hover:underline">
                                    <i data-lucide="image" class="w-4 h-4"></i> Lihat Foto Bukti
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Validasi & ACC/Tolak Actions -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 border-t border-slate-100 dark:border-slate-700/50">
                    <div class="text-xs text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-2">
                        <span>Status Validasi:</span>
                        <span class="font-bold uppercase {{ $item->status === 'valid' ? 'text-emerald-500' : ($item->status === 'invalid' ? 'text-red-500' : 'text-amber-500') }}">{{ $item->status }}</span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span class="italic text-slate-400 dark:text-slate-500">
                            @if($item->approval_status === 'approved' || $item->approval_status === 'rejected')
                                Validasi dilakukan oleh Guru Pembimbing
                            @else
                                Validasi dilakukan oleh Pembimbing DUDI
                            @endif
                        </span>
                    </div>

                    @if($item->approval_status === 'approved')
                        <div class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-lg mb-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <div>
                                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Jurnal Disetujui</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Oleh: {{ $item->approvedBy->name }} • {{ \Carbon\Carbon::parse($item->approved_at)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                            </div>
                        </div>
                    @elseif($item->approval_status === 'rejected')
                        <div class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg mb-3">
                            <i data-lucide="x-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">Jurnal Ditolak</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mb-1">Oleh: {{ $item->approvedBy->name }} • {{ \Carbon\Carbon::parse($item->approved_at)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                                <p class="text-xs text-red-600 dark:text-red-400 italic">Catatan: {{ $item->approval_notes }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Form untuk ACC/Tolak -->
                        <div x-data="{ rejectModalOpen: false, rejectionNotes: '' }" class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <form action="{{ route('pembimbing_sekolah.jurnal.approve', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white">
                                        <i data-lucide="check" class="w-4 h-4"></i> ACC
                                    </button>
                                </form>
                                <button type="button" @click="rejectModalOpen = true"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white">
                                    <i data-lucide="x" class="w-4 h-4"></i> TOLAK
                                </button>
                            </div>

                            <!-- Reject Modal -->
                            <div x-show="rejectModalOpen" style="display: none;" class="fixed inset-0 z-100 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
                                <div @click.away="rejectModalOpen = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-md w-full p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Tolak Jurnal</h3>
                                    </div>
                                    <form action="{{ route('pembimbing_sekolah.jurnal.reject', $item) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Catatan Penolakan *</label>
                                            <textarea name="approval_notes" rows="4" required
                                                      class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-red-500 outline-none resize-none transition-all"
                                                      placeholder="Jelaskan alasan penolakan dan saran perbaikan..."></textarea>
                                            @error('approval_notes')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" @click="rejectModalOpen = false"
                                                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold bg-red-600 hover:bg-red-500 text-white transition-all">
                                                Tolak Jurnal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Legacy Saran Guru (hanya jika belum di-ACC/Tolak) -->
                    @if($item->approval_status === 'pending')
                        <form action="{{ route('pembimbing_sekolah.jurnal.update', $item) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3 w-full items-center mt-4 pt-4 border-t border-slate-200 dark:border-slate-700/50">
                            @csrf
                            @method('PATCH')
                            <div class="md:col-span-9 lg:col-span-10">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 block mb-1">Saran Tambahan (Opsional)</label>
                                <textarea name="catatan_guru" rows="1" 
                                          class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all block"
                                          placeholder="Tulis saran atau komentar tambahan untuk siswa...">{{ $item->catatan_guru }}</textarea>
                            </div>
                            <div class="md:col-span-3 lg:col-span-2 md:mt-6">
                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-1 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white">
                                    <i data-lucide="send" class="w-4 h-4"></i> KIRIM
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @empty
             <div class="glass-card p-12 text-center text-slate-500 dark:text-slate-400 italic">
                Belum ada aktivitas jurnal dari siswa bimbingan Anda.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>

    <!-- Image Modal -->
    <template x-teleport="body">
        <div x-show="imageModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;" 
             class="fixed inset-0 z-100 flex flex-col items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6 md:p-8 cursor-zoom-out"
             @click="imageModalOpen = false"
             @keydown.escape.window="imageModalOpen = false">
            
            <div @click.stop
                 class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center cursor-default">
                 
                <!-- Close Button -->
                <button @click="imageModalOpen = false" 
                        class="absolute -top-12 right-0 md:-top-4 md:-right-12 z-50 p-2.5 text-white bg-slate-800/80 hover:bg-red-600 border border-slate-700/50 rounded-full shadow-xl transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500"
                        title="Tutup (Esc)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Image Wrapper -->
                <div class="w-full h-full flex items-center justify-center bg-slate-900/20 border border-white/5 rounded-3xl p-2 shadow-2xl overflow-hidden">
                    <img :src="modalImageUrl" 
                         class="max-w-full max-h-[75vh] md:max-h-[80vh] rounded-2xl object-contain shadow-inner selection:bg-transparent"
                         alt="Foto Bukti Kegiatan">
                </div>

                <!-- Footer Actions -->
                <div class="mt-4 flex gap-3">
                    <a :href="modalImageUrl" 
                       target="_blank" 
                       class="px-4 py-2 bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold rounded-xl border border-slate-700/50 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Buka di Tab Baru
                    </a>
                </div>
            </div>
        </div>
    </template>
    </div>
</x-app-layout>
