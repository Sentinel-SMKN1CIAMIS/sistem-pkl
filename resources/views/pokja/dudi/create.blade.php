<x-app-layout>
    <x-slot name="header">Tambah Data DUDI</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.dudi.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('pokja.dudi.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konsentrasi Keahlian</label>
                        <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="" disabled selected>Pilih Konsentrasi Keahlian</option>
                            @foreach($concentrations as $item)
                                <option value="{{ $item->id }}" {{ old('konsentrasi_keahlian_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }} ({{ $item->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('konsentrasi_keahlian_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama DUDI / Perusahaan</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                               placeholder="Nama industri">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" required
                                  class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                                  placeholder="Alamat lengkap industri">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kota" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kota / Kabupaten</label>
                        <input type="text" name="kota" id="kota" value="{{ old('kota') }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Contoh: Bandung">
                        @error('kota')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. Telepon / WA Perusahaan</label>
                        <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Opsional">
                        @error('no_telepon')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                         <div>
                            <label for="nama_pimpinan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Pimpinan / HRD</label>
                            <input type="text" name="nama_pimpinan" id="nama_pimpinan" value="{{ old('nama_pimpinan') }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Nama pimpinan">
                        </div>
                        <div>
                            <label for="bidang_usaha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bidang Usaha</label>
                            <input type="text" name="bidang_usaha" id="bidang_usaha" value="{{ old('bidang_usaha') }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Contoh: IT, Kuliner, Otomotif">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Data DUDI
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
