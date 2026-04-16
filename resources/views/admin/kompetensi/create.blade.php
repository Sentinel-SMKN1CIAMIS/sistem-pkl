<x-app-layout>
    <x-slot name="header">Tambah Elemen Kompetensi</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.kompetensi.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-3xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('admin.kompetensi.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Jurusan / Konsentrasi</label>
                        <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="" disabled selected>Pilih Jurusan</option>
                            @foreach($concentrations as $item)
                                <option value="{{ $item->id }}" {{ old('konsentrasi_keahlian_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Elemen Kompetensi / Capaian Pembelajaran</label>
                        <textarea name="nama" id="nama" rows="4" required
                                  class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all font-mono text-sm"
                                  placeholder="Contoh: Menjelaskan dasar-dasar pemrograman prosedural..."></textarea>
                    </div>

                    <div>
                        <label for="kategori" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori (Opsional)</label>
                        <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Contoh: Dasar Dasar Programming">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Kompetensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
