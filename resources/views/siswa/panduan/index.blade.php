<x-app-layout>
    <x-slot name="header">Buku Panduan PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Unduh buku panduan dan dokumen teknis yang relevan dengan PKL Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($panduans as $item)
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
                   class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    UNDUH PDF
                </a>
            </div>
        @empty
            <div class="md:col-span-3 text-center py-12 text-slate-500 dark:text-slate-400 italic">
                Belum ada buku panduan yang tersedia untuk Anda.
            </div>
        @endforelse
    </div>
</x-app-layout>
