<x-app-layout>
    <x-slot name="header">Pengaturan Template Sertifikat PKL</x-slot>

    <div class="max-w-4xl">
        <div class="glass-card p-6 mb-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-500"></i> Informasi Template
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                Gunakan variabel berikut ini di dalam teks untuk menampilkan data dinamis secara otomatis saat dicetak:
            </p>
            <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1 font-mono bg-slate-100 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                <li>[NAMA_SISWA] : Menampilkan nama lengkap siswa</li>
                <li>[NIS] : Menampilkan NIS siswa</li>
                <li>[NAMA_DUDI] : Menampilkan nama perusahaan/DUDI</li>
                <li>[JURUSAN] : Menampilkan jurusan siswa</li>
                <li>[TANGGAL_AWAL] : Menampilkan tanggal mulai PKL</li>
                <li>[TANGGAL_AKHIR] : Menampilkan tanggal selesai PKL</li>
            </ul>
        </div>

        <form action="{{ route('pokja.pengaturan.sertifikat.update') }}" method="POST" class="glass-card p-6">
            @csrf
            
            <div class="mb-6">
                <label for="template" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Teks Paragraf Pembuka Sertifikat</label>
                <textarea name="template" id="template" rows="5" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200 resize-y" required>{{ old('template', $template) }}</textarea>
                @error('template')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Template
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
