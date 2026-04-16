<x-app-layout>
    <x-slot name="header">Tambah Jurnal Harian</x-slot>

    <div class="mb-6">
        <a href="{{ route('siswa.jurnal.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('siswa.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-slate-300 mb-2">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                    </div>

                    <div>
                        <label for="kompetensi_id" class="block text-sm font-medium text-slate-300 mb-2">Elemen Kompetensi</label>
                        <select name="kompetensi_id" id="kompetensi_id" required
                                class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                            <option value="" disabled selected>Pilih Kompetensi</option>
                            @foreach($kompetensis as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="kegiatan" class="block text-sm font-medium text-slate-300 mb-2">Unit Kerja / Pekerjaan</label>
                        <input type="text" name="kegiatan" id="kegiatan" required
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all font-mono"
                               placeholder="Sebutkan unit kerja dan apa yang Anda kerjakan hari ini...">
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-sm font-medium text-slate-300 mb-2">Catatan Detail / Kendala</label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all"
                                  placeholder="Ceritakan proses pengerjaan atau kendala yang dihadapi..."></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Foto Bukti Kegiatan</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="foto" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-900/20 hover:bg-slate-900/40 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="camera" class="w-8 h-8 text-slate-500 mb-2"></i>
                                    <p class="text-xs text-slate-400">Klik untuk unggah foto (JPG, PNG, max 2MB)</p>
                                </div>
                                <input id="foto" name="foto" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
