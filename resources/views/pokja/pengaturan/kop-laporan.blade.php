<x-app-layout>
    <x-slot name="header">Pengaturan Kop Laporan PDF</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Info / Panduan Card -->
        <div class="glass-card p-6 border-l-4 border-indigo-500 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-indigo-500"></i> Informasi Kop Laporan
            </h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                Pengaturan di bawah ini digunakan untuk mengatur kepala / KOP surat yang tampil pada laporan ekspor PDF sistem (misalnya: Data Akun Pembimbing DUDI). Perubahan data akan langsung diterapkan secara real-time pada dokumen PDF yang diunduh.
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('pokja.pengaturan.kop_laporan.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Kop Laporan -->
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-3">
                    <i data-lucide="building" class="w-5 h-5 text-indigo-500"></i> Edit Detail Kop Surat Laporan
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="report_kop_baris_1" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Instansi Induk (Baris 1)</label>
                        <input type="text" name="report_kop_baris_1" id="report_kop_baris_1" value="{{ old('report_kop_baris_1', $report_kop_baris_1) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_1')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_2" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Dinas Terkait (Baris 2)</label>
                        <input type="text" name="report_kop_baris_2" id="report_kop_baris_2" value="{{ old('report_kop_baris_2', $report_kop_baris_2) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_2')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_3" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Cabang Dinas / Bidang (Baris 3)</label>
                        <input type="text" name="report_kop_baris_3" id="report_kop_baris_3" value="{{ old('report_kop_baris_3', $report_kop_baris_3) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_3')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_4" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Sekolah / Lembaga (Baris 4 - Cetak Tebal)</label>
                        <input type="text" name="report_kop_baris_4" id="report_kop_baris_4" value="{{ old('report_kop_baris_4', $report_kop_baris_4) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-bold text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_4')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_5" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Alamat Jalan & Telepon (Baris 5)</label>
                        <input type="text" name="report_kop_baris_5" id="report_kop_baris_5" value="{{ old('report_kop_baris_5', $report_kop_baris_5) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_5')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_6" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Faksimile, Website, & Email (Baris 6)</label>
                        <input type="text" name="report_kop_baris_6" id="report_kop_baris_6" value="{{ old('report_kop_baris_6', $report_kop_baris_6) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_6')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_kop_baris_7" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kota & Kode Pos (Baris 7)</label>
                        <input type="text" name="report_kop_baris_7" id="report_kop_baris_7" value="{{ old('report_kop_baris_7', $report_kop_baris_7) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        @error('report_kop_baris_7')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan Kop
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
