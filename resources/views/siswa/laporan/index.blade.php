<x-app-layout>
    <x-slot name="header">Laporan Akhir PKL</x-slot>

    <div class="max-w-4xl text-slate-300">
        <div class="mb-6">
            <p class="text-slate-400">Unggah laporan akhir PKL Anda yang telah disetujui oleh pembimbing industri dan sekolah.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Status Card -->
            <div class="md:col-span-1">
                <div class="glass-card p-6 sticky top-8">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 font-mono">Status Laporan</h3>
                    
                    @if($laporan)
                        <div class="space-y-4">
                            <div>
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        'submitted' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest border {{ $statusClasses[$laporan->status] }}">
                                    {{ $laporan->status }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500">
                                <p>Terakhir diupdate:</p>
                                <p class="text-slate-300 font-medium">{{ \Carbon\Carbon::parse($laporan->updated_at)->isoFormat('LLL') }}</p>
                            </div>
                            @if($laporan->file_path)
                                <a href="{{ asset('storage/' . $laporan->file_path) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-sm text-slate-300 transition-all">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Lihat File
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="file-warning" class="w-6 h-6 text-slate-600"></i>
                            </div>
                            <p class="text-sm text-slate-500 italic">Belum mengunggah laporan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload Form -->
            <div class="md:col-span-2">
                <div class="glass-card p-8">
                    @if($laporan && $laporan->status === 'approved')
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-100 mb-2">Laporan Disetujui!</h3>
                            <p class="text-slate-400">Laporan Anda telah divalidasi dan tidak perlu diubah lagi.</p>
                        </div>
                    @else
                        <form action="{{ route('siswa.laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <label for="judul" class="block text-sm font-medium text-slate-300 mb-2">Judul Laporan</label>
                                <input type="text" name="judul" id="judul" value="{{ old('judul', $laporan->judul ?? '') }}" required
                                       class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all"
                                       placeholder="Contoh: Laporan PKL Maintenance Server di PT. Maju Jaya">
                            </div>

                            <div>
                                <label for="deskripsi" class="block text-sm font-medium text-slate-300 mb-2">Deskripsi Singkat / Ringkasan</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4"
                                          class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all"
                                          placeholder="Tuliskan ringkasan singkat hasil PKL Anda...">{{ old('deskripsi', $laporan->deskripsi ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">File Laporan (PDF/Docx, Max 5MB)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-900/20 hover:bg-slate-900/40 transition-all">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i data-lucide="file-up" class="w-8 h-8 text-slate-500 mb-2"></i>
                                            <p class="text-xs text-slate-400">Pilih file laporan Anda</p>
                                        </div>
                                        <input id="file" name="file" type="file" class="hidden" accept=".pdf,.doc,.docx" />
                                    </label>
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 active:scale-95">
                                    <i data-lucide="send" class="w-5 h-5"></i>
                                    {{ $laporan ? 'Update Laporan' : 'Kirim Laporan' }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
