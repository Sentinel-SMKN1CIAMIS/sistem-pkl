<x-app-layout>
    <x-slot name="header">Buku Panduan PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Baca buku panduan dan unduh dokumen teknis yang relevan dengan PKL Anda.</p>
    </div>

    <div class="glass-card mb-8 p-4 md:p-6 flex flex-col" style="height: 80vh; min-height: 600px;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-blue-500"></i>
                Buku Pedoman PKL 2025-2026
            </h3>
            <a href="{{ asset('BUKU%20PEDOMAN%20PKL%202025-2026.pdf') }}" download
               class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                Unduh PDF
            </a>
        </div>
        <div class="w-full flex-grow rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900" style="height: 100%; min-height: 500px;">
            <iframe src="{{ asset('BUKU%20PEDOMAN%20PKL%202025-2026.pdf') }}#toolbar=0&navpanes=0&scrollbar=0" style="width: 100%; height: 100%; min-height: 500px;" title="Buku Pedoman PKL" frameborder="0">
                Browser Anda tidak mendukung tampilan PDF. Silakan unduh PDF untuk melihatnya: <a href="{{ asset('BUKU%20PEDOMAN%20PKL%202025-2026.pdf') }}">Unduh PDF</a>
            </iframe>
        </div>
    </div>

    @if($panduans->count() > 0)
    <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Dokumen Panduan Tambahan</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($panduans as $item)
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 text-red-400">
                        <div class="p-3 bg-red-400/10 rounded-2xl">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Document</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2 leading-tight">{{ $item->judul }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-6 line-clamp-3 italic">{{ $item->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                </div>
                
                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" 
                   class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    UNDUH PDF
                </a>
            </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
