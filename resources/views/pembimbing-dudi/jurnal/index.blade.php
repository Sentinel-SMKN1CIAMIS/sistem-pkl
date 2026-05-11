<x-app-layout>
    <x-slot name="header">Validasi Jurnal Siswa</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Review dan berikan feedback pada laporan harian siswa PKL di perusahaan Anda.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden transition-all duration-300 {{ $item->status === 'pending' ? 'border-l-4 border-amber-500' : '' }}">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-400 font-bold">
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

                            <div class="mt-4">
                                <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400 mb-2 inline-block">
                                    {{ $item->kompetensi->nama }}
                                </span>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                                <p class="text-slate-600 dark:text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>
                            </div>

                            @if($item->foto_path)
                                <div class="mb-4">
                                    <a href="{{ asset('storage/' . $item->foto_path) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-blue-400 hover:underline">
                                        <i data-lucide="image" class="w-4 h-4"></i> Lihat Foto Bukti
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Status Jurnal -->
                        <div class="w-full md:w-80 p-4 rounded-xl bg-slate-100 dark:bg-slate-900/30 border border-slate-200/50 dark:border-slate-700/50 flex flex-col justify-center text-center">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status Saat Ini</label>
                            @php
                                $statusClassesSidebar = [
                                    'pending' => 'bg-amber-500 text-slate-900',
                                    'valid' => 'bg-emerald-600 text-slate-900 dark:text-white',
                                    'invalid' => 'bg-red-600 text-slate-900 dark:text-white'
                                ];
                                $statusLabel = [
                                    'pending' => 'MENUNGGU VALIDASI',
                                    'valid' => 'DIVALIDASI (SEKOLAH)',
                                    'invalid' => 'DITOLAK (SEKOLAH)'
                                ];
                            @endphp
                            <div class="px-4 py-3 rounded-xl text-sm font-bold shadow-lg {{ $statusClassesSidebar[$item->status] }}">
                                {{ $statusLabel[$item->status] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
             <div class="glass-card p-12 text-center text-slate-500 dark:text-slate-400 italic">
                Semua jurnal siswa telah diproses atau belum ada data.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>
</x-app-layout>
