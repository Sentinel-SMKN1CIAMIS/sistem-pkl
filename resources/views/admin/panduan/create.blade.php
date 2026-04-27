<x-app-layout>
    <x-slot name="header">Unggah Buku Panduan</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.panduan.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-2xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('admin.panduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Judul Panduan</label>
                    <input type="text" name="judul" id="judul" required
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Contoh: Buku Saku PKL Siswa 2024">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sasaran / Tipe</label>
                        <select name="tipe" id="tipe" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="siswa">Siswa</option>
                            <option value="dudi">DUDI</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>

                    <div>
                        <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konsentrasi Keahlian (Opsional)</label>
                        <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id"
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="">Semua Konsentrasi Keahlian</option>
                            @foreach($concentrations as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi Singkat</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">File Panduan (PDF only, Max 10MB)</label>
                    <input type="file" name="file" accept=".pdf" required
                           class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-slate-900 dark:text-white hover:file:bg-blue-500 cursor-pointer">
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                        Unggah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
