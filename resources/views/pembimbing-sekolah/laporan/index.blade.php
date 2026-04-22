<x-app-layout>
    <x-slot name="header">Evaluasi Laporan Akhir PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Review dan berikan persetujuan untuk laporan akhir siswa bimbingan Anda.</p>
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
                    <tr class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Judul & Deskripsi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Media & Bukti</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $laporan->siswa->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tracking-wider">{{ $laporan->siswa->nis }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $laporan->judul }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md truncate" title="{{ $laporan->deskripsi }}">{{ $laporan->deskripsi ?? '-' }}</p>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 block">{{ \Carbon\Carbon::parse($laporan->updated_at)->isoFormat('LLL') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    @if(!empty($laporan->link_media_sosial) && is_array($laporan->link_media_sosial))
                                        @foreach($laporan->link_media_sosial as $idx => $link)
                                            @if($link)
                                                <a href="{{ $link }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded text-xs transition-all w-max">
                                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                                    Link Media {{ count($laporan->link_media_sosial) > 1 ? ($idx + 1) : '' }}
                                                </a>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-xs text-slate-500 italic">Tidak ada tautan media</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                                        'submitted' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                        'approved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$laporan->status] ?? $statusClasses['draft'] }}">
                                    {{ $laporan->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($laporan->status !== 'approved')
                                        <form action="{{ route('pembimbing_sekolah.laporan.update', $laporan) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" onclick="return confirm('Yakin ingin menyetujui laporan ini?')" class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors" title="Setujui">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($laporan->status !== 'rejected')
                                        <form action="{{ route('pembimbing_sekolah.laporan.update', $laporan) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" onclick="return confirm('Yakin ingin menolak laporan ini?')" class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors" title="Tolak">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada laporan yang disubmit oleh siswa bimbingan Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($laporans->hasPages())
            <div class="p-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
