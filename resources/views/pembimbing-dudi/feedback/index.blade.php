<x-app-layout>
    <x-slot name="header">Feedback untuk Sekolah</x-slot>

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-slate-600 dark:text-slate-400">
                Kirimkan feedback umum terkait siswa PKL dan penyelenggaraan program PKL ke pihak sekolah secara periodik (mingguan/bulanan).
            </p>
        </div>
        <div>
            <a href="{{ route('pembimbing_dudi.feedback.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Kirim Feedback
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Tanggal Kirim</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Periode Rekap</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Uraian Evaluasi & Saran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50">
                    @forelse($feedbacks as $item)
                        <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                {{ $item->created_at->translatedFormat('d F Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-xs text-blue-400 font-semibold">
                                    {{ $item->periode }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800 dark:text-slate-200">
                                <div class="mb-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase block mb-1">Evaluasi:</span>
                                    <p class="whitespace-pre-wrap">{{ $item->isi_feedback }}</p>
                                </div>
                                @if($item->saran)
                                    <div>
                                        <span class="text-xs font-bold text-slate-500 uppercase block mb-1">Saran:</span>
                                        <p class="whitespace-pre-wrap italic">{{ $item->saran }}</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada feedback yang dikirimkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
