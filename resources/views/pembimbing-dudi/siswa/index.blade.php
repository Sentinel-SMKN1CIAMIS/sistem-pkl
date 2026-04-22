<x-app-layout>
    <x-slot name="header">Daftar Siswa PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Daftar seluruh siswa yang sedang melaksanakan PKL di perusahaan Anda.</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Pembimbing Sekolah</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Statistik</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50">
                    @forelse($students as $item)
                        <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $item->nis }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-800 dark:text-slate-200 block">{{ $item->konsentrasiKeahlian->nama }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Kelas {{ $item->kelas }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->pembimbingSekolah)
                                    <span class="text-sm text-slate-700 dark:text-slate-300 block font-medium">{{ $item->pembimbingSekolah->nama_lengkap }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $item->pembimbingSekolah->no_hp }}</span>
                                @else
                                    <span class="text-xs text-slate-500 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-slate-500 uppercase font-bold">Jurnal</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $item->jurnal_count }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-slate-500 uppercase font-bold">Absensi</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $item->absensi_count }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $hariIni = strtolower($item->status_hari_ini);
                                    if ($hariIni === 'masuk kerja') {
                                        $statusClass = 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                    } elseif ($hariIni === 'pulang kerja') {
                                        $statusClass = 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20';
                                    } elseif ($hariIni === 'selesai') {
                                        $statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                    } elseif ($hariIni === 'dibatalkan') {
                                        $statusClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                    } elseif ($hariIni === 'belum absen') {
                                        $statusClass = 'bg-slate-500/10 text-slate-500 border-slate-500/20';
                                    } else {
                                        $statusClass = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
                                    }
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $item->status_hari_ini }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada siswa PKL yang terdaftar di perusahaan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
