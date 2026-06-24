<x-app-layout>
    <x-slot name="header">Import & Scan Buku Pedoman (PDF)</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.kompetensi.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Kelola TP
        </a>
    </div>

    <div class="max-w-4xl mx-auto space-y-8" x-data="{ loading: false }">
        {{-- Loading Screen --}}
        <div x-show="loading" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-9999 flex flex-col items-center justify-center text-white" style="display: none;">
            <div class="relative w-24 h-24 mb-6">
                <!-- Outer scanning ring -->
                <div class="absolute inset-0 rounded-full border-4 border-t-blue-500 border-r-emerald-500 border-b-purple-500 border-l-transparent animate-spin"></div>
                <!-- Inner file icon with radar scan line -->
                <div class="absolute inset-4 rounded-full bg-slate-900 flex items-center justify-center shadow-inner">
                    <i data-lucide="file-search" class="w-8 h-8 text-blue-400 animate-pulse"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-2 tracking-wide bg-gradient-to-r from-blue-400 via-emerald-400 to-purple-400 bg-clip-text text-transparent">Memindai Buku Pedoman PKL...</h3>
            <p class="text-xs text-slate-400 max-w-sm text-center px-4 leading-relaxed">
                Harap tunggu, algoritma AI sedang membaca struktur PDF, memisahkan Elemen Kompetensi, mengekstrak CP, dan mengelompokkan Tujuan Pembelajaran (TP) per jurusan.
            </p>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Default PDF Scan Card --}}
            <div class="glass-card p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center mb-4">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Scan File Pedoman Default</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                        Gunakan file buku pedoman default `BUKU PEDOMAN PKL 2025-2026.pdf` yang telah diletakkan di dalam folder public sistem.
                    </p>

                    @if($defaultPdfExists)
                        <div class="p-3.5 rounded-xl bg-emerald-500/5 border border-emerald-500/10 mb-6 flex items-center gap-3 text-xs text-emerald-400">
                            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                            <div>
                                <p class="font-semibold text-emerald-300">File default terdeteksi</p>
                                <p class="text-[10px] text-emerald-500/80">Ukuran: {{ $defaultPdfSize }} MB</p>
                            </div>
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/10 mb-6 flex items-center gap-3 text-xs text-amber-400">
                            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                            <div>
                                <p class="font-semibold text-amber-300">File default tidak ditemukan</p>
                                <p class="text-[10px] text-amber-500/80">Unggah file pedoman baru di samping kanan.</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if($defaultPdfExists)
                    <form action="{{ route('pokja.kompetensi.import-pdf.parse') }}" method="POST" @submit="loading = true">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i data-lucide="zap" class="w-5 h-5"></i>
                            Mulai Scan Otomatis
                        </button>
                    </form>
                @else
                    <button disabled class="w-full py-3 bg-slate-700 text-slate-500 font-bold rounded-xl border border-slate-600/50 cursor-not-allowed flex items-center justify-center gap-2">
                        <i data-lucide="zap-off" class="w-5 h-5"></i>
                        Scan Default Dinonaktifkan
                    </button>
                @endif
            </div>

            {{-- Custom Upload Card --}}
            <div class="glass-card p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-4">
                        <i data-lucide="upload-cloud" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Unggah PDF Baru</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                        Pilih atau seret file PDF Pedoman PKL dari penyimpanan lokal komputer Anda jika ingin memproses dokumen kurikulum edisi lain.
                    </p>
                </div>

                <form action="{{ route('pokja.kompetensi.import-pdf.parse') }}" method="POST" enctype="multipart/form-data" @submit="loading = true">
                    @csrf
                    <div class="mb-5">
                        <label for="pdf_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 dark:border-slate-700/50 border-dashed rounded-xl cursor-pointer bg-slate-100/50 dark:bg-slate-900/10 hover:bg-slate-200/50 dark:hover:bg-slate-900/30 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="file-up" class="w-8 h-8 text-slate-400 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Pilih file PDF</p>
                                <p class="text-[10px] text-slate-500 mt-1">Maksimal 15MB</p>
                            </div>
                            <input id="pdf_file" name="pdf_file" type="file" accept=".pdf" class="hidden" required @change="if ($event.target.files.length) { $el.closest('form').submit(); loading = true; }" />
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i data-lucide="upload" class="w-5 h-5"></i>
                        Unggah & Pindai
                    </button>
                </form>
            </div>
        </div>

        {{-- Guide Card --}}
        <div class="glass-card p-6 border-l-4 border-l-blue-500">
            <h4 class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                Bagaimana Cara Kerja Scan Cerdas Ini?
            </h4>
            <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-400 text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">1</span>
                    <span>Sistem membaca isi teks PDF secara asinkron di server.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-400 text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">2</span>
                    <span>Algoritma secara cerdas mendeteksi 7 program keahlian dan memisahkan datanya.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-400 text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">3</span>
                    <span>Data dikelompokkan bertingkat ke dalam 4 Elemen Kompetensi utama.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-400 text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">4</span>
                    <span>Setelah scan selesai, Anda akan diarahkan ke halaman <b>Review & Map</b> untuk memetakan jurusan ke database, menyunting redaksi teks, dan memilih TP mana saja yang akan di-impor.</span>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
