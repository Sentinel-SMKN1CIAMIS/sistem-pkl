<x-app-layout>
    <x-slot name="header">Pengajuan Tempat PKL</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-7 h-7 text-blue-500"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Daftarkan Tempat PKL Kamu</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Isi data perusahaan tempat kamu akan melaksanakan PKL. Pengajuan akan ditinjau oleh Guru Pembimbing.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('siswa.pengajuan_pkl.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="nama_perusahaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Nama Perusahaan / Instansi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Contoh: PT. Maju Bersama">
                    @error('nama_perusahaan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="pimpinan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Pimpinan / HRD</label>
                    <input type="text" name="pimpinan" id="pimpinan" value="{{ old('pimpinan') }}"
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Opsional">
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all resize-none"
                              placeholder="Jl. ...">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="no_telp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. Telepon Perusahaan</label>
                    <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}"
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Opsional">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
