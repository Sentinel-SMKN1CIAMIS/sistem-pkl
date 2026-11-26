<x-app-layout>
    <x-slot name="header">Rekap Kunjungan Monitoring Lapangan</x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="glass-card p-5 border-l-4 border-blue-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kunjungan Tercatat</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalKunjungan }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-emerald-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kunjungan Bulan Ini</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalBulanIni }}</p>
        </div>
    </div>

    <div class="glass-card p-5 mb-6">
        <form method="GET" action="{{ route('pokja.kunjungan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Guru / DUDI / catatan..."
                       class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Guru Pembimbing</label>
                <select name="pembimbing_sekolah_id" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua">Semua Guru</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ (string)$pembimbingId === (string)$t->id ? 'selected' : '' }}>{{ $t->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">DUDI</label>
                <select name="dudi_id" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua">Semua DUDI</option>
                    @foreach($dudis as $d)
                        <option value="{{ $d->id }}" {{ (string)$dudiId === (string)$d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan</label>
                <input type="month" name="month" value="{{ $month }}"
                       class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer">Filter</button>
                <a href="{{ route('pokja.kunjungan.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-700/60 text-slate-500 uppercase text-[10px] font-bold">
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Guru Pembimbing</th>
                        <th class="px-4 py-3.5">DUDI</th>
                        <th class="px-4 py-3.5">Hasil Monitoring &amp; Kendala</th>
                        <th class="px-4 py-3.5">Bukti / GPS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($kunjungans as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20">
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-800 dark:text-slate-100">
                                {{ $item->tanggal_kunjungan?->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-800 dark:text-slate-100 block">{{ $item->pembimbingSekolah?->nama_lengkap ?? '-' }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $item->pembimbingSekolah?->nip ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-semibold text-slate-700 dark:text-slate-300">
                                {{ $item->dudi?->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 max-w-md">
                                <p class="text-slate-700 dark:text-slate-300">{{ $item->catatan_monitoring }}</p>
                                @if($item->kendala)
                                    <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1"><strong>Kendala:</strong> {{ $item->kendala }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($item->foto_path)
                                        <a href="{{ asset('storage/' . $item->foto_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold hover:bg-blue-50 hover:text-blue-600">
                                            Foto
                                        </a>
                                    @endif
                                    @if($item->latitude && $item->longitude)
                                        <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                            Maps
                                        </a>
                                    @endif
                                    @if(!$item->foto_path && !$item->latitude)
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">Belum ada data kunjungan monitoring lapangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $kunjungans->links() }}</div>
    </div>
</x-app-layout>
