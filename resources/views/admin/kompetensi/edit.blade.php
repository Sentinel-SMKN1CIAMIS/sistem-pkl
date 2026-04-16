<x-app-layout>
    <x-slot name="header">Edit Elemen Kompetensi</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.kompetensi.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-3xl text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('admin.kompetensi.update', $kompetensi) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-300 mb-2">Jurusan</label>
                        <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                            @foreach($concentrations as $item)
                                <option value="{{ $item->id }}" {{ $kompetensi->konsentrasi_keahlian_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-medium text-slate-300 mb-2">Elemen Kompetensi</label>
                        <textarea name="nama" id="nama" rows="4" required
                                  class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 font-mono text-sm">{{ old('nama', $kompetensi->nama) }}</textarea>
                    </div>

                    <div>
                        <label for="kategori" class="block text-sm font-medium text-slate-300 mb-2">Kategori</label>
                        <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $kompetensi->kategori) }}"
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
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
