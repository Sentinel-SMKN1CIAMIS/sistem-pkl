<x-app-layout>
    <x-slot name="header">Rekap Kehadiran Seluruh Siswa {{ $program?->kode ?? 'Jurusan' }}</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">
                Program Keahlian: {{ $program?->nama ?? '-' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Menampilkan rekap kehadiran &amp; status early warning seluruh siswa pada program keahlian Anda (tidak terbatas pada siswa bimbingan pribadi).
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-card p-5 border-l-4 border-blue-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Siswa Jurusan</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $summaryStats['total_siswa'] }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-red-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Siswa Berisiko (Warning)</p>
            <p class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">{{ $summaryStats['total_berisiko'] }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-emerald-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Hadir (Bulan Ini)</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $summaryStats['total_hadir'] }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-amber-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Alpha (Bulan Ini)</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $summaryStats['total_alpha'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-5 mb-6">
        <form method="GET" action="{{ route('kaprog.rekap_kehadiran.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Siswa / DUDI</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NIS / DUDI..."
                       class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Konsentrasi</label>
                <select name="konsentrasi_keahlian_id" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua">Semua Konsentrasi</option>
                    @foreach($concentrations as $c)
                        <option value="{{ $c->id }}" {{ (string)$selectedConcentration === (string)$c->id ? 'selected' : '' }}>{{ $c->kode }} - {{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kelas</label>
                <select name="kelas" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua">Semua Kelas</option>
                    @foreach($availableClasses as $kls)
                        <option value="{{ $kls }}" {{ $selectedKelas === $kls ? 'selected' : '' }}>{{ $kls }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan &amp; Tahun</label>
                <div class="flex gap-1.5">
                    <select name="month" class="w-2/3 px-2 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                        @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $mNum => $mLabel)
                            <option value="{{ $mNum }}" {{ $month === $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="year" value="{{ $year }}" min="2024" max="2030"
                           class="w-1/3 px-2 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Warning</label>
                <select name="risk" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua" {{ $riskFilter === 'semua' ? 'selected' : '' }}>Semua Siswa</option>
                    <option value="berisiko" {{ $riskFilter === 'berisiko' ? 'selected' : '' }}>Hanya Berisiko</option>
                    <option value="aman" {{ $riskFilter === 'aman' ? 'selected' : '' }}>Aman</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('kaprog.rekap_kehadiran.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold">
                        <th class="px-4 py-3.5">Siswa</th>
                        <th class="px-4 py-3.5">Kelas</th>
                        <th class="px-4 py-3.5">DUDI &amp; Pembimbing</th>
                        <th class="px-3 py-3.5 text-center">Hadir</th>
                        <th class="px-3 py-3.5 text-center">Izin</th>
                        <th class="px-3 py-3.5 text-center">Sakit</th>
                        <th class="px-3 py-3.5 text-center">Libur</th>
                        <th class="px-3 py-3.5 text-center">Alpha</th>
                        <th class="px-3 py-3.5 text-center">% Hadir</th>
                        <th class="px-4 py-3.5">Early Warning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($rows as $row)
                        @php $s = $row['siswa']; $w = $row['warning']; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $s->nama_lengkap }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $s->nis }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                {{ $s->kelas }}
                                <span class="block text-[10px] text-slate-400">{{ $s->konsentrasiKeahlian?->kode ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-[220px]">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block truncate">{{ $s->dudi?->nama ?? 'Belum Ditempatkan' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">Pembimbing: {{ $s->pembimbingSekolah?->nama_lengkap ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row['hadir'] }}</td>
                            <td class="px-3 py-3 text-center font-semibold text-blue-600 dark:text-blue-400">{{ $row['izin'] }}</td>
                            <td class="px-3 py-3 text-center font-semibold text-amber-600 dark:text-amber-400">{{ $row['sakit'] }}</td>
                            <td class="px-3 py-3 text-center font-semibold text-sky-600 dark:text-sky-400">{{ $row['libur'] }}</td>
                            <td class="px-3 py-3 text-center font-bold text-red-600 dark:text-red-400">{{ $row['alpha'] }}</td>
                            <td class="px-3 py-3 text-center font-black {{ $row['rate'] < 75 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $row['rate'] }}%
                            </td>
                            <td class="px-4 py-3">
                                @if($w)
                                    <div class="flex flex-col gap-1">
                                        @foreach($w['reasons'] as $reason)
                                            <span class="inline-flex w-fit px-2 py-0.5 rounded-md text-[10px] font-bold {{ $w['severity'] === 'critical' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                                                {{ $reason }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-slate-400 italic">
                                Tidak ada data siswa yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
