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

    <div class="mb-6 jurnal-header-container">
        <p class="text-slate-600 dark:text-slate-400 max-w-xl">Catat setiap aktivitas pengerjaan atau pembelajaran di industri sesuai format resmi.</p>
        <div class="jurnal-btn-container">
            <a href="{{ route('siswa.jurnal.export') }}" class="jurnal-btn px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl border border-slate-200 dark:border-slate-700 transition-all gap-2 text-sm md:text-base">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Jurnal
            </a>
            <a href="{{ route('siswa.jurnal.create') }}" class="jurnal-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2 text-sm md:text-base">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Tambah Jurnal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
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
                                    {{ $item->kompetensi->nama }}
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
                            <div class="jurnal-photo-container border border-slate-200 dark:border-slate-700 shadow-sm">
                                <img src="{{ asset('storage/' . $item->foto_path) }}" alt="Foto Kegiatan" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="flex flex-row md:flex-col justify-end gap-2">
                            @if($item->status === 'pending')
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
</x-app-layout>
