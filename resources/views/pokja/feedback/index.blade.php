<x-app-layout>
    <x-slot name="header">Feedback dari Pembimbing Industri (DUDI)</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">
            Daftar feedback, saran, masukan, dan evaluasi berkala yang dikirimkan oleh pembimbing industri (DUDI) untuk perbaikan pelaksanaan program PKL.
        </p>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-6 p-4">
        <form action="{{ route('pokja.feedback.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pembimbing atau industri..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white/50 dark:bg-slate-800/50 border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-800 dark:text-slate-200">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                FILTER
            </button>
        </form>
    </div>

    <!-- Data List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($feedbacks as $item)
            <div class="glass-card p-6 border border-slate-200/50 dark:border-slate-700/50 hover:border-blue-500/30 transition-all">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/30 dark:border-slate-700/50 pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                            <i data-lucide="message-square" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">
                                {{ $item->pembimbingDudi->nama_lengkap }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $item->pembimbingDudi->dudi->nama }} ({{ $item->pembimbingDudi->jabatan }})
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-xs text-blue-400 font-bold uppercase tracking-wider">
                            {{ $item->periode }}
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">
                            {{ $item->created_at->translatedFormat('d F Y, H:i') }}
                        </span>
                    </div>
                </div>
                <div class="text-sm text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl whitespace-pre-wrap leading-relaxed">
                    {{ $item->isi_feedback }}
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center text-slate-500 dark:text-slate-400 italic">
                <div class="flex flex-col items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-500"></i>
                    <p class="font-medium">Belum ada feedback yang masuk dari pembimbing industri (DUDI).</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
        <div class="mt-6">
            {{ $feedbacks->links() }}
        </div>
    @endif
</x-app-layout>
