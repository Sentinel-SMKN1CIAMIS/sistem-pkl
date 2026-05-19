<x-app-layout>
    <x-slot name="header">Edit Konsentrasi Keahlian</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.konsentrasi_keahlian.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-3xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('admin.konsentrasi_keahlian.update', $konsentrasi_keahlian) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="program_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Program Keahlian</label>
                        <select name="program_keahlian_id" id="program_keahlian_id" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ $prog->id == $konsentrasi_keahlian->program_keahlian_id ? 'selected' : '' }}>
                                    {{ $prog->nama }} ({{ $prog->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_keahlian_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kode" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kode Konsentrasi</label>
                        <input type="text" name="kode" id="kode" value="{{ old('kode', $konsentrasi_keahlian->kode) }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        @error('kode')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Konsentrasi</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $konsentrasi_keahlian->nama) }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="durasi_pkl_bulan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Durasi PKL (Bulan)</label>
                        <input type="number" name="durasi_pkl_bulan" id="durasi_pkl_bulan" value="{{ old('durasi_pkl_bulan', $konsentrasi_keahlian->durasi_pkl_bulan) }}" required min="1"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        @error('durasi_pkl_bulan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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
