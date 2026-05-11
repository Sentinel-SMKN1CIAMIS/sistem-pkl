<x-app-layout>
    <x-slot name="header">Daftar Siswa Bimbingan</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('pokja.pembimbing_sekolah.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar
            </a>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $pembimbing_sekolah->nama_lengkap }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 uppercase tracking-widest font-semibold mt-1">
                {{ $pembimbing_sekolah->tipe }} — {{ $pembimbing_sekolah->konsentrasiKeahlian->nama }}
            </p>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                Siswa yang Dibimbing ({{ $students->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Nama Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">NIS</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Industri (DUDI)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Status PKL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                    @forelse($students as $siswa)
                        <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-900 dark:text-slate-100 block">{{ $siswa->nama_lengkap }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $siswa->kelas }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 font-mono">
                                {{ $siswa->nis }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($siswa->dudi)
                                    <span class="text-slate-900 dark:text-slate-200 font-medium">{{ $siswa->dudi->nama }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum ditempatkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'belum_pkl' => 'bg-slate-500/10 text-slate-500 border-slate-500/20',
                                        'sedang_pkl' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'selesai_pkl' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $statusColors[$siswa->status_pkl] ?? 'bg-slate-500/10 text-slate-500 border-slate-500/20' }}">
                                    {{ str_replace('_', ' ', $siswa->status_pkl) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada siswa yang ditugaskan ke pembimbing ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
