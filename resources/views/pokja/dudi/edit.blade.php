<x-app-layout>
    <x-slot name="header">Edit Data DUDI</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.dudi.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8 text-slate-700 dark:text-slate-300">
            <form action="{{ route('pokja.dudi.update', $dudi) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-slate-700 dark:text-slate-300">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konsentrasi Keahlian</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-100 dark:bg-slate-900/50 p-4 border border-slate-200/50 dark:border-slate-700/50 rounded-xl">
                            @foreach($concentrations as $item)
                                <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-lg cursor-pointer border border-transparent hover:border-blue-500/50 transition-all">
                                    <input type="checkbox" name="konsentrasi_keahlian_ids[]" value="{{ $item->id }}"
                                           {{ in_array($item->id, old('konsentrasi_keahlian_ids', $selectedConcentrationIds)) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600">
                                    <span class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item->nama }} ({{ $item->kode }})</span>
                                </label>
                            @endforeach
                        </div>
                        @error('konsentrasi_keahlian_ids')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Perusahaan</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $dudi->nama) }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" required
                                  class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">{{ old('alamat', $dudi->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kota" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kota / Kabupaten</label>
                        <input type="text" name="kota" id="kota" value="{{ old('kota', $dudi->kota) }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        @error('kota')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. Telepon</label>
                        <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $dudi->no_telepon) }}"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                         <div>
                            <label for="nama_pimpinan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Pimpinan / HRD</label>
                            <input type="text" name="nama_pimpinan" id="nama_pimpinan" value="{{ old('nama_pimpinan', $dudi->nama_pimpinan) }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Nama pimpinan (Opsional)">
                        </div>
                        <div>
                            <label for="bidang_usaha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bidang Usaha</label>
                            <input type="text" name="bidang_usaha" id="bidang_usaha" value="{{ old('bidang_usaha', $dudi->bidang_usaha) }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Contoh: IT, Kuliner, Otomotif">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
