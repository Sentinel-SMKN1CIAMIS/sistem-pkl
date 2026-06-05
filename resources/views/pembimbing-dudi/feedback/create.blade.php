<x-app-layout>
    <x-slot name="header">Kirim Feedback untuk Sekolah</x-slot>

    <div class="mb-6">
        <a href="{{ route('pembimbing_dudi.feedback.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar Feedback
        </a>
    </div>

    <div class="max-w-3xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('pembimbing_dudi.feedback.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="periode" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Periode Rekap Kehadiran / Kegiatan</label>
                    <select name="periode" id="periode" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        <option value="" disabled selected>Pilih Periode Feedback</option>
                        <option value="Rekap Mingguan (Minggu 1)" {{ old('periode') == 'Rekap Mingguan (Minggu 1)' ? 'selected' : '' }}>Rekap Mingguan (Minggu 1)</option>
                        <option value="Rekap Mingguan (Minggu 2)" {{ old('periode') == 'Rekap Mingguan (Minggu 2)' ? 'selected' : '' }}>Rekap Mingguan (Minggu 2)</option>
                        <option value="Rekap Mingguan (Minggu 3)" {{ old('periode') == 'Rekap Mingguan (Minggu 3)' ? 'selected' : '' }}>Rekap Mingguan (Minggu 3)</option>
                        <option value="Rekap Mingguan (Minggu 4)" {{ old('periode') == 'Rekap Mingguan (Minggu 4)' ? 'selected' : '' }}>Rekap Mingguan (Minggu 4)</option>
                        <option value="Rekap Bulanan (Bulan 1)" {{ old('periode') == 'Rekap Bulanan (Bulan 1)' ? 'selected' : '' }}>Rekap Bulanan (Bulan 1)</option>
                        <option value="Rekap Bulanan (Bulan 2)" {{ old('periode') == 'Rekap Bulanan (Bulan 2)' ? 'selected' : '' }}>Rekap Bulanan (Bulan 2)</option>
                        <option value="Rekap Bulanan (Bulan 3)" {{ old('periode') == 'Rekap Bulanan (Bulan 3)' ? 'selected' : '' }}>Rekap Bulanan (Bulan 3)</option>
                        <option value="Lainnya" {{ old('periode') == 'Lainnya' ? 'selected' : '' }}>Lainnya / Umum</option>
                    </select>
                    @error('periode')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="isi_feedback" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Uraian Evaluasi PKL</label>
                    <textarea name="isi_feedback" id="isi_feedback" rows="5" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all" placeholder="Tuliskan uraian evaluasi program PKL, perkembangan kompetensi siswa bimbingan secara umum, kendala yang dihadapi industri..."></textarea>
                    @error('isi_feedback')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="saran" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Saran</label>
                    <textarea name="saran" id="saran" rows="4" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all" placeholder="Tuliskan saran perbaikan untuk pelaksanaan PKL berikutnya (Opsional)..."></textarea>
                    @error('saran')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
