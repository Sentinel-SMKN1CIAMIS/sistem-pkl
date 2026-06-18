<x-app-layout>
    <x-slot name="header">Status Pengajuan PKL</x-slot>

    <div class="max-w-2xl mx-auto">
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-start gap-3 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <div>
                    <span class="block font-bold">Peringatan Akses</span>
                    <span class="block mt-1">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2 shadow-sm">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i> {{ session('success') }}
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
                        @if($pengajuan->status === 'menunggu') bg-amber-500/10
                        @elseif($pengajuan->status === 'disetujui_kaprog') bg-blue-500/10
                        @elseif($pengajuan->status === 'disetujui') bg-emerald-500/10
                        @else bg-red-500/10 @endif">
                        @if($pengajuan->status === 'menunggu')
                            <i data-lucide="clock" class="w-7 h-7 text-amber-500"></i>
                        @elseif($pengajuan->status === 'disetujui_kaprog')
                            <i data-lucide="shield-alert" class="w-7 h-7 text-blue-500"></i>
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
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-medium whitespace-nowrap">
                                    <i data-lucide="loader" class="w-3 h-3 animate-spin"></i> Menunggu Persetujuan Kaprog
                                </span>
                            @elseif($pengajuan->status === 'disetujui_kaprog')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-xs font-medium animate-pulse whitespace-nowrap">
                                    <i data-lucide="loader" class="w-3 h-3 animate-spin"></i> Menunggu Validasi Pokja
                                </span>
                            @elseif($pengajuan->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-medium whitespace-nowrap">
                                    <i data-lucide="check" class="w-3 h-3"></i> {{ $pengajuan->dudi_id ? 'Disetujui' : 'Disetujui Pokja' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-medium whitespace-nowrap">
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

                @if($pengajuan->status === 'disetujui')
                    @if($pengajuan->dudi_id)
                        <!-- Alur DUDI Terdaftar: Tanpa Surat Pengantar & Bukti Balasan -->
                        <div class="mt-6 p-5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-2xl shadow-sm">
                            <div class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0"></i>
                                <div>
                                    <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 mb-1">Pengajuan Disetujui</h3>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-400/90 leading-relaxed">
                                        Tempat PKL Anda di <strong>{{ $pengajuan->nama_perusahaan }}</strong> telah disetujui secara final. Anda tidak memerlukan surat pengantar atau mengunggah bukti balasan karena perusahaan ini merupakan mitra terdaftar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->siswa?->status_pkl === 'belum_mulai')
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-2xl shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0"></i>
                                    <div>
                                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Menunggu Pemetaan Guru Pembimbing</h3>
                                        <p class="text-sm text-blue-700 dark:text-blue-400/90 leading-relaxed">
                                            Tim Pokja akan segera memetakan <strong>Guru Pembimbing Sekolah</strong> untuk Anda. Anda baru dapat mengakses fitur Jurnal, Absensi, dan Laporan setelah proses pemetaan ini selesai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Alur DUDI Baru/Manual: Perlu Surat Pengantar & Bukti Balasan -->
                        <div class="mt-6 p-5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-2xl shadow-sm flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0"></i>
                                <div>
                                    <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 mb-1">Pengajuan Disetujui Pokja</h3>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-400/90 leading-relaxed">
                                        Pengajuan Tempat PKL Anda telah <strong>disetujui</strong> oleh Pokja. Silakan cetak Surat Pengantar Anda secara mandiri di bawah ini dan serahkan ke perusahaan/DUDI tujuan.
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-start">
                                <a href="{{ route('siswa.pengajuan_pkl.print') }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg cursor-pointer">
                                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Surat Pengantar
                                </a>
                            </div>
                        </div>

                        <!-- Bukti Penerimaan Perusahaan Section -->
                        <div class="mt-4 p-5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-blue-500/10 rounded-xl text-blue-500">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Bukti Penerimaan Perusahaan</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Setelah surat pengantar diserahkan ke perusahaan/DUDI dan disetujui, silakan unggah bukti balasan/penerimaan resmi dari perusahaan untuk mempermudah pemetaan Guru Pembimbing.
                                    </p>
                                </div>
                            </div>

                            @if($pengajuan->bukti_balasan)
                                <div class="p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 text-emerald-700 dark:text-emerald-400 font-semibold">
                                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                        <span>Bukti Penerimaan Berhasil Diunggah!</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $pengajuan->bukti_balasan) }}" target="_blank"
                                           class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold transition-all flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat File
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('siswa.pengajuan_pkl.upload_bukti') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label for="bukti_balasan" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                        {{ $pengajuan->bukti_balasan ? 'Unggah Ulang Bukti Penerimaan (Jika salah berkas)' : 'Pilih Berkas Bukti (PDF, PNG, JPG, JPEG - Maksimal 2MB)' }}
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="file" name="bukti_balasan" id="bukti_balasan" required accept=".pdf,image/png,image/jpeg,image/jpg"
                                               class="flex-1 text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-500/10 file:text-blue-500 hover:file:bg-blue-500/20 file:transition-all file:cursor-pointer border border-slate-200 dark:border-slate-800 rounded-xl p-1 bg-white dark:bg-slate-900">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer">
                                            Unggah
                                        </button>
                                    </div>
                                    @error('bukti_balasan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </form>
                        </div>

                        @if(auth()->user()->siswa?->status_pkl === 'belum_mulai')
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-2xl shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0"></i>
                                    <div>
                                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Menunggu Pemetaan Guru Pembimbing</h3>
                                        <p class="text-sm text-blue-700 dark:text-blue-400/90 leading-relaxed">
                                            Setelah bukti penerimaan diunggah, Tim Pokja akan segera memetakan <strong>Guru Pembimbing Sekolah</strong> untuk Anda. 
                                            Anda baru dapat mengakses fitur Jurnal, Absensi, dan Laporan setelah proses pemetaan ini selesai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif

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
