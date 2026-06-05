<x-app-layout>
    <x-slot name="header">Status Pengajuan PKL</x-slot>

    <div class="max-w-2xl mx-auto">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i> {{ session('success') }}
            </div>
        @endif

        @if(!$pengajuan)
            <div class="glass-card p-8 text-center">
                <i data-lucide="file-x" class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600"></i>
                <p class="text-slate-500 dark:text-slate-400 mb-4">Belum ada pengajuan tempat PKL.</p>
                <a href="{{ route('siswa.pengajuan_pkl.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Sekarang
                </a>
            </div>
        @else
            <div class="glass-card p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center
                        {{ $pengajuan->status === 'menunggu' ? 'bg-amber-500/10' : ($pengajuan->status === 'disetujui' ? 'bg-emerald-500/10' : 'bg-red-500/10') }}">
                        @if($pengajuan->status === 'menunggu')
                            <i data-lucide="clock" class="w-7 h-7 text-amber-500"></i>
                        @elseif($pengajuan->status === 'disetujui')
                            <i data-lucide="check-circle-2" class="w-7 h-7 text-emerald-500"></i>
                        @else
                            <i data-lucide="x-circle" class="w-7 h-7 text-red-500"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $pengajuan->nama_perusahaan }}</h2>
                        <p class="text-sm mt-0.5">
                            @if($pengajuan->status === 'menunggu')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-medium">
                                    <i data-lucide="loader" class="w-3 h-3 animate-spin"></i> Menunggu Persetujuan
                                </span>
                            @elseif($pengajuan->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-medium">
                                    <i data-lucide="check" class="w-3 h-3"></i> Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-medium">
                                    <i data-lucide="x" class="w-3 h-3"></i> Ditolak
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    @if($pengajuan->pimpinan)
                    <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                        <div><span class="text-slate-400 text-xs block">Pimpinan</span><span class="text-slate-700 dark:text-slate-200">{{ $pengajuan->pimpinan }}</span></div>
                    </div>
                    @endif
                    @if($pengajuan->alamat)
                    <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                        <div><span class="text-slate-400 text-xs block">Alamat</span><span class="text-slate-700 dark:text-slate-200">{{ $pengajuan->alamat }}</span></div>
                    </div>
                    @endif
                    @if($pengajuan->no_telp)
                    <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <i data-lucide="phone" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                        <div><span class="text-slate-400 text-xs block">No. Telp</span><span class="text-slate-700 dark:text-slate-200">{{ $pengajuan->no_telp }}</span></div>
                    </div>
                    @endif
                    <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                        <div><span class="text-slate-400 text-xs block">Diajukan pada</span><span class="text-slate-700 dark:text-slate-200">{{ $pengajuan->created_at->format('d M Y, H:i') }}</span></div>
                    </div>
                </div>

                @if($pengajuan->status === 'ditolak')
                    @if($pengajuan->catatan)
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl">
                        <p class="text-xs text-red-500 font-medium mb-1">Alasan Penolakan:</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $pengajuan->catatan }}</p>
                    </div>
                    @endif
                    <div class="mt-6">
                        <a href="{{ route('siswa.pengajuan_pkl.create') }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Ajukan Ulang
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
