@props(['earlyWarning' => null, 'detailUrl' => null])

@php
    $ew = $earlyWarning ?? ['total_at_risk' => 0, 'critical_count' => 0, 'warning_count' => 0, 'items' => collect()];
    $items = $ew['items'] ?? collect();
    $totalAtRisk = $ew['total_at_risk'] ?? 0;
    $criticalCount = $ew['critical_count'] ?? 0;
    $warningCount = $ew['warning_count'] ?? 0;
@endphp

<div class="glass-card p-6 mb-6 border-l-4 {{ $criticalCount > 0 ? 'border-red-500' : ($totalAtRisk > 0 ? 'border-amber-500' : 'border-emerald-500') }}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $criticalCount > 0 ? 'bg-red-500 text-white' : ($totalAtRisk > 0 ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white') }} flex items-center justify-center shrink-0">
                <i data-lucide="{{ $totalAtRisk > 0 ? 'shield-alert' : 'shield-check' }}" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    Deteksi Dini Siswa Berisiko (Early Warning)
                    @if($totalAtRisk > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $criticalCount > 0 ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                            {{ $totalAtRisk }} Siswa
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Pantauan otomatis siswa yang tidak absen &ge; 3 hari kerja berturut-turut atau belum isi jurnal &gt; 5 hari.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($criticalCount > 0)
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">
                    Absen Kritis: {{ $criticalCount }}
                </span>
            @endif
            @if($warningCount > 0)
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                    Jurnal Tertunggak: {{ $warningCount }}
                </span>
            @endif
            @if($detailUrl)
                <a href="{{ $detailUrl }}" class="px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition-opacity">
                    Lihat Rekap Lengkap &rarr;
                </a>
            @endif
        </div>
    </div>

    @if($items->isEmpty())
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/60 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
            <span>Semua siswa dalam pantauan saat ini berstatus aman (tidak ada yang bolos &ge; 3 hari kerja atau menunggak jurnal &gt; 5 hari).</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold">
                        <th class="py-2.5 pr-3">Siswa</th>
                        <th class="py-2.5 px-3">Kelas / Jurusan</th>
                        <th class="py-2.5 px-3">DUDI</th>
                        <th class="py-2.5 px-3">Indikator Risiko</th>
                        <th class="py-2.5 pl-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($items as $row)
                        @php $s = $row['siswa']; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="py-2.5 pr-3 font-bold text-slate-800 dark:text-slate-100 whitespace-nowrap">
                                {{ $s->nama_lengkap }}
                                <span class="block text-[10px] font-mono font-normal text-slate-400">{{ $s->nis }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $s->kelas }}
                                <span class="text-[10px] text-slate-400">({{ $s->konsentrasiKeahlian?->kode ?? '-' }})</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-300 max-w-[200px] truncate">
                                {{ $s->dudi?->nama ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($row['reasons'] as $reason)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ str_contains($reason, 'absen') ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-2.5 pl-3 text-right whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $row['severity'] === 'critical' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                                    {{ $row['severity'] === 'critical' ? 'Kritis' : 'Perlu Pantau' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
