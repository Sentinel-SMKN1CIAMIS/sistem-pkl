<x-app-layout>
    <x-slot name="header">Jurnal Kegiatan PKL</x-slot>

    <style>
        .jurnal-header-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .jurnal-btn-container {
            display: flex !important;
            gap: 0.75rem !important;
            width: 100% !important;
        }
        .jurnal-btn {
            width: 100% !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .jurnal-photo-container {
            width: 100% !important;
            height: 192px !important;
            border-radius: 0.75rem !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
        }
        
        @media (min-width: 768px) {
            .jurnal-header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .jurnal-btn-container {
                width: auto !important;
            }
            .jurnal-btn {
                width: auto !important;
            }
            .jurnal-photo-container {
                width: 192px !important;
                height: 128px !important;
            }
        }
    </style>

    <div x-data="{ imageModalOpen: false, modalImageUrl: '' }">
    <div class="mb-6 jurnal-header-container">
        <p class="text-slate-600 dark:text-slate-400 max-w-xl">Catat setiap aktivitas pengerjaan atau pembelajaran di industri sesuai format resmi.</p>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto mt-4 sm:mt-0">
            <a href="{{ route('siswa.jurnal.export') }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Jurnal
            </a>
            <a href="{{ route('siswa.jurnal.portofolio') }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2">
                <i data-lucide="book" class="w-5 h-5"></i>
                Cetak Portofolio
            </a>
            @if(auth()->user()->siswa?->status_pkl === 'selesai')
            <a href="{{ route('siswa.jurnal.sertifikat') }}" class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all flex items-center justify-center gap-2">
                <i data-lucide="award" class="w-5 h-5"></i>
                Cetak Sertifikat
            </a>
            @endif
            @if($hasAbsenToday)
                <a href="{{ route('siswa.jurnal.create') }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Tambah Jurnal
                </a>
            @else
                <button type="button" disabled class="w-full sm:w-auto px-5 py-2.5 bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 font-medium rounded-xl border border-slate-200 dark:border-slate-600 cursor-not-allowed flex items-center justify-center gap-2" title="Silakan lakukan absensi hari ini terlebih dahulu untuk menambah jurnal">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Tambah Jurnal (Absen Dulu)
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(!$hasAbsenToday)
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 dark:text-amber-400 text-sm flex items-center gap-3">
            <i data-lucide="info" class="w-5 h-5"></i>
            <div>
                <span class="font-bold">Perhatian:</span> Anda belum melakukan absensi hari ini. Silakan melakukan 
                <a href="{{ route('siswa.absensi.index') }}" class="underline font-semibold hover:text-amber-400 dark:hover:text-amber-300">absensi terlebih dahulu</a> 
                agar tombol tambah jurnal diaktifkan.
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden group">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-sm font-bold text-blue-400">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
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
                             <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-2 uppercase tracking-wide decoration-blue-500 underline underline-offset-8 decoration-2">
                                {{ $item->deskripsi_pekerjaan }}
                             </h3>
                            <div class="flex items-center gap-2 mb-4 flex-wrap">
                                <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400">
                                    {{ $item->kompetensi?->nama ?? 'Tidak Ada Kompetensi' }}
                                </span>
                                @if($item->tujuanPembelajaran)
                                    <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-[10px] text-blue-600 dark:text-blue-300 font-medium">
                                        TP: {{ $item->tujuanPembelajaran->tp ?? $item->tujuanPembelajaran->nama }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2">{{ $item->catatan }}</p>
                        </div>
                        
                        @if($item->foto_path)
                            <button type="button" 
                                    @click="modalImageUrl = '{{ asset('storage/' . $item->foto_path) }}'; imageModalOpen = true" 
                                    class="jurnal-photo-container border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden group/photo cursor-zoom-in relative">
                                <img src="{{ asset('storage/' . $item->foto_path) }}" alt="Foto Kegiatan" class="w-full h-full object-cover transition-transform duration-300 group-hover/photo:scale-105">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/photo:opacity-100 transition-opacity flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                    </svg>
                                </div>
                            </button>
                        @endif

                        <div class="flex flex-row md:flex-col justify-end gap-2">
                            @if($item->status === 'pending')
                                <a href="{{ route('siswa.jurnal.edit', $item) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-blue-500 transition-colors">
                                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                                </a>
                                <form action="{{ route('siswa.jurnal.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus jurnal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2 text-slate-500 dark:text-slate-400 hover:text-red-400 transition-colors">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    @if($item->catatan_pembimbing || $item->catatan_guru)
                        <div class="mt-4 space-y-2">
                            @if($item->catatan_pembimbing)
                                <div class="p-3 rounded-lg bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 italic text-sm text-slate-600 dark:text-slate-400">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 not-italic">Komentar Pembimbing DUDI:</span> {{ $item->catatan_pembimbing }}
                                </div>
                            @endif
                            @if($item->catatan_guru)
                                <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/10 border border-blue-200/50 dark:border-blue-700/50 italic text-sm text-blue-800 dark:text-blue-300">
                                    <span class="font-bold text-blue-900 dark:text-blue-200 not-italic">Komentar Pembimbing Sekolah:</span> {{ $item->catatan_guru }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center">
                <div class="w-16 h-16 bg-white/50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="book-open" class="w-8 h-8 text-slate-600"></i>
                </div>
                <h3 class="text-slate-700 dark:text-slate-300 font-medium mb-1">Belum Ada Jurnal</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Mulai catat aktivitas harianmu sekarang.</p>
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
