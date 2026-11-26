<x-app-layout>
    <x-slot name="header">Detail Monitoring Siswa</x-slot>

    <div class="mb-6">
        <a href="{{ route('kaprog.monitoring_siswa.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Monitoring Siswa
        </a>
    </div>

    {{-- Profile Header Card --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $siswa->nama_lengkap }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $siswa->nis }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ $siswa->kelas }} ({{ $siswa->konsentrasiKeahlian?->kode }})</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                    <span><strong>DUDI:</strong> {{ $siswa->dudi?->nama ?? 'Belum Ditempatkan' }}</span>
                    <span><strong>Pembimbing Sekolah:</strong> {{ $siswa->pembimbingSekolah?->nama_lengkap ?? '-' }}</span>
                    <span><strong>Pembimbing DUDI:</strong> {{ $siswa->pembimbingDudi?->nama_lengkap ?? $siswa->pembimbing_dudi_nama ?? '-' }}</span>
                </div>
            </div>

            @if($warning)
                <div class="p-3 rounded-xl {{ $warning['severity'] === 'critical' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300' }} text-xs">
                    <span class="font-extrabold uppercase block mb-0.5">Early Warning</span>
                    @foreach($warning['reasons'] as $reason)
                        <span class="block">&bull; {{ $reason }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-5 pt-5 border-t border-slate-200/60 dark:border-slate-700/60 text-center">
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                <span class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 block">Hadir</span>
                <span class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ $attendanceCounts->get('hadir', 0) }}</span>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20">
                <span class="text-[10px] font-bold uppercase text-blue-600 dark:text-blue-400 block">Izin</span>
                <span class="text-xl font-black text-blue-700 dark:text-blue-300">{{ $attendanceCounts->get('izin', 0) }}</span>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                <span class="text-[10px] font-bold uppercase text-amber-600 dark:text-amber-400 block">Sakit</span>
                <span class="text-xl font-black text-amber-700 dark:text-amber-300">{{ $attendanceCounts->get('sakit', 0) }}</span>
            </div>
            <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-900/20">
                <span class="text-[10px] font-bold uppercase text-sky-600 dark:text-sky-400 block">Libur</span>
                <span class="text-xl font-black text-sky-700 dark:text-sky-300">{{ $attendanceCounts->get('libur', 0) }}</span>
            </div>
            <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20">
                <span class="text-[10px] font-bold uppercase text-red-600 dark:text-red-400 block">Alpha</span>
                <span class="text-xl font-black text-red-700 dark:text-red-300">{{ $attendanceCounts->get('alpha', 0) }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs: Jurnal Harian & Riwayat Absensi --}}
    <div x-data="{ tab: '{{ request()->has('absensi_page') ? 'absensi' : 'jurnal' }}' }">
        <div class="flex gap-2 mb-4 border-b border-slate-200 dark:border-slate-800">
            <button type="button" @click="tab = 'jurnal'"
                    :class="tab === 'jurnal' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-3 text-sm transition-colors cursor-pointer">
                Riwayat Jurnal Harian ({{ $jurnals->total() }})
            </button>
            <button type="button" @click="tab = 'absensi'"
                    :class="tab === 'absensi' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-3 text-sm transition-colors cursor-pointer">
                Riwayat Absensi ({{ $absensis->total() }})
            </button>
        </div>

        {{-- Tab 1: Jurnal --}}
        <div x-show="tab === 'jurnal'" class="space-y-4">
            @forelse($jurnals as $j)
                <div class="glass-card p-5">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ \Carbon\Carbon::parse($j->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $j->isRejected() ? 'bg-red-100 text-red-700' : (($j->approval_status === 'approved' || $j->status === 'valid') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $j->isRejected() ? 'Ditolak' : (($j->approval_status === 'approved' || $j->status === 'valid') ? 'Valid' : 'Pending') }}
                                </span>
                            </div>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $j->deskripsi_pekerjaan }}</h4>
                            @if($j->catatan)
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">{!! nl2br(e($j->catatan)) !!}</p>
                            @endif
                            @if($j->approval_notes || $j->catatan_pembimbing)
                                <p class="text-[11px] text-slate-500 italic mt-2">
                                    Catatan Pembimbing: "{{ $j->approval_notes ?: $j->catatan_pembimbing }}"
                                </p>
                            @endif
                        </div>
                        @if($j->foto_path)
                            <a href="{{ asset('storage/' . $j->foto_path) }}" target="_blank" class="w-full sm:w-28 h-28 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 block">
                                <img src="{{ asset('storage/' . $j->foto_path) }}" alt="Foto Jurnal" class="w-full h-full object-cover">
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass-card p-10 text-center text-xs text-slate-400 italic">Siswa ini belum mengisi jurnal harian.</div>
            @endforelse
            <div>{{ $jurnals->links() }}</div>
        </div>

        {{-- Tab 2: Absensi --}}
        <div x-show="tab === 'absensi'" x-cloak class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-700/60 text-slate-500 uppercase text-[10px] font-bold">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Jam Masuk</th>
                            <th class="px-4 py-3">Jam Pulang</th>
                            <th class="px-4 py-3">Alasan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($absensis as $abs)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-100 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($abs->tanggal)->isoFormat('dddd, D MMM YYYY') }}
                                </td>
                                <td class="px-4 py-3 uppercase font-bold">{{ $abs->status }}</td>
                                <td class="px-4 py-3 font-mono">{{ $abs->waktu_datang ?: '-' }}</td>
                                <td class="px-4 py-3 font-mono">{{ $abs->waktu_pulang ?: '-' }}</td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $abs->alasan ?: $abs->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">Belum ada riwayat absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $absensis->links() }}</div>
        </div>
    </div>
</x-app-layout>
