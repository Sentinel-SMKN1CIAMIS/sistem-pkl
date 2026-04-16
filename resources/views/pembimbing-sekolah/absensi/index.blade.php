<x-app-layout>
    <x-slot name="header">Kehadiran Siswa Bimbingan</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Monitoring absensi harian siswa bimbingan Anda selama PKL.</p>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Masuk</th>
                        <th class="px-6 py-4">Pulang</th>
                        <th class="px-6 py-4">GPS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                                {{ $row->siswa->nama_lengkap }}
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $row->siswa->dudi->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hadir</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4">
                                @if($row->latitude)
                                    <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" 
                                       class="text-blue-400 hover:text-blue-300">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada riwayat kehadiran dari siswa bimbingan Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
