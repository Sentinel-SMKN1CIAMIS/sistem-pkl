<x-app-layout>
    <x-slot name="header">Monitoring Siswa {{ $program?->kode ?? '' }}</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">
                Monitoring Langsung Siswa — {{ $program?->nama ?? 'Program Keahlian' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pantau kehadiran, jurnal harian, dan status risiko seluruh siswa di jurusan Anda secara langsung tanpa harus membuka guru pembimbing satu per satu.
            </p>
        </div>
        <a href="{{ route('kaprog.rekap_kehadiran.index') }}"
           class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shrink-0 inline-flex items-center gap-1.5 w-fit">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-500"></i>
            Rekap Kehadiran Bulanan
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="glass-card p-5 border-l-4 border-blue-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Siswa Jurusan</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $summary['total_siswa'] }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-emerald-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Hadir Hari Ini</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $summary['hadir_hari_ini'] }}</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-red-500">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Perlu Perhatian (Early Warning)</p>
            <p class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">{{ $summary['berisiko'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-5 mb-6">
        <form method="GET" action="{{ route('kaprog.monitoring_siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Siswa / NIS / DUDI</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama, NIS, atau DUDI..."
                       class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Konsentrasi</label>
                <select name="konsentrasi_keahlian_id" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua">Semua</option>
                    @foreach($concentrations as $c)
                        <option value="{{ $c->id }}" {{ (string)$selectedConcentration === (string)$c->id ? 'selected' : '' }}>{{ $c->kode }}</option>
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
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Absen Hari Ini</label>
                <select name="status_hari_ini" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua" {{ $todayStatusFilter === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="hadir" {{ $todayStatusFilter === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="belum_absen" {{ $todayStatusFilter === 'belum_absen' ? 'selected' : '' }}>Belum Absen</option>
                    <option value="izin_sakit_libur" {{ $todayStatusFilter === 'izin_sakit_libur' ? 'selected' : '' }}>Izin/Sakit/Libur</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Warning</label>
                <select name="risk" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="semua" {{ $riskFilter === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="berisiko" {{ $riskFilter === 'berisiko' ? 'selected' : '' }}>Berisiko</option>
                    <option value="aman" {{ $riskFilter === 'aman' ? 'selected' : '' }}>Aman</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer">Filter</button>
                <a href="{{ route('kaprog.monitoring_siswa.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">Reset</a>
            </div>
        </form>
    </div>

    {{-- Students Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-700/60 text-slate-500 uppercase text-[10px] font-bold">
                        <th class="px-4 py-3.5">Siswa</th>
                        <th class="px-4 py-3.5">Kelas</th>
                        <th class="px-4 py-3.5">DUDI &amp; Pembimbing</th>
                        <th class="px-4 py-3.5 text-center">Hari Ini</th>
                        <th class="px-4 py-3.5 text-center">Jurnal (Valid/Total)</th>
                        <th class="px-4 py-3.5">Early Warning</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($students as $s)
                        @php
                            $todayAbs = $todayAbsensiBySiswa->get($s->id);
                            $warn = $warningsBySiswa->get($s->id);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $s->nama_lengkap }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $s->nis }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                {{ $s->kelas }}
                                <span class="block text-[10px] text-slate-400">{{ $s->konsentrasiKeahlian?->kode ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-[230px]">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block truncate">{{ $s->dudi?->nama ?? 'Belum Ditempatkan' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">Pembimbing: {{ $s->pembimbingSekolah?->nama_lengkap ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($todayAbs)
                                    @php
                                        $badgeColors = [
                                            'hadir' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                            'izin' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                            'sakit' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                            'libur' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300',
                                            'alpha' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badgeColors[$todayAbs->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $todayAbs->status }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                        Belum Absen
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $s->valid_jurnal_count }}</span>
                                <span class="text-slate-400">/</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $s->total_jurnal_count }}</span>
                                @if($s->pending_jurnal_count > 0)
                                    <span class="block text-[10px] text-amber-500 font-semibold">{{ $s->pending_jurnal_count }} pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($warn)
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold {{ $warn['severity'] === 'critical' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                                        {{ $warn['reasons'][0] ?? 'Perlu Pantau' }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('kaprog.monitoring_siswa.show', $s) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Pantau
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 italic">Tidak ada data siswa yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $students->links() }}</div>
    </div>
</x-app-layout>
