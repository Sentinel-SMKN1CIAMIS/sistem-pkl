<x-app-layout>
    <x-slot name="header">Kehadiran Siswa Bimbingan</x-slot>

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <p class="text-slate-600 dark:text-slate-400">Monitoring absensi harian siswa bimbingan Anda selama PKL.</p>
        <a href="{{ route('pembimbing_sekolah.absensi.export', request()->all()) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
            <i data-lucide="download-cloud" class="w-5 h-5"></i>
            Ekspor Rekapan (PDF)
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card p-6 mb-8">
        <form action="{{ route('pembimbing_sekolah.absensi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Pilih Siswa</label>
                <select name="siswa_id" class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('siswa_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-700 transition-all text-sm">
                    Filter
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'siswa_id']))
                    <a href="{{ route('pembimbing_sekolah.absensi.index') }}" class="p-2 text-slate-500 hover:text-red-400 transition-colors" title="Reset Filter">
                        <i data-lucide="x-circle" class="w-6 h-6"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Masuk</th>
                        <th class="px-6 py-4 whitespace-nowrap">Pulang</th>
                        <th class="px-6 py-4 whitespace-nowrap">GPS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                {{ $row->siswa->nama_lengkap }}
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $row->siswa->dudi->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'label' => 'Hadir'],
                                        'izin' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'label' => 'Izin'],
                                        'sakit' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'label' => 'Sakit'],
                                        'alpha' => ['bg' => 'bg-gray-500/10', 'text' => 'text-gray-400', 'label' => 'Alpa'],
                                    ];
                                    $config = $statusConfig[$row->status] ?? $statusConfig['hadir'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['text'] }}/20">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                @if($row->status === 'hadir' && $row->alasan)
                                    <br><span class="text-[10px] text-orange-500 font-medium" title="{{ $row->alasan }}"><i data-lucide="info" class="w-3 h-3 inline"></i> {{ Str::limit($row->alasan, 20) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
