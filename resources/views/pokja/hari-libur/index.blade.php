<x-app-layout>
    <x-slot name="header">Kalender Tanggal Merah Nasional</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="glass-card p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="w-4 h-4 text-red-500"></i>
                    Tambah Tanggal Merah Nasional
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Tanggal yang ditambahkan akan otomatis menandai kalender seluruh siswa &amp; menonaktifkan notif pagi di hari tersebut.
                </p>

                <form action="{{ route('pokja.hari_libur.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal') }}"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Nama Hari Libur</label>
                        <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="Contoh: Hari Kemerdekaan RI"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Cuti Bersama / Libur Nasional"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm transition-colors cursor-pointer">
                        Simpan Tanggal Merah
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="glass-card p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Tanggal Merah Tahun {{ $year }}</h3>
                    <form method="GET" action="{{ route('pokja.hari_libur.index') }}" class="flex items-center gap-2">
                        <input type="number" name="year" value="{{ $year }}" min="2024" max="2035"
                               class="w-24 px-3 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold cursor-pointer">Tampilkan</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700/60 text-slate-500 uppercase text-[10px] font-bold">
                                <th class="py-2.5 pr-3">Tanggal</th>
                                <th class="py-2.5 px-3">Nama Hari Libur</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 pl-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @forelse($holidays as $h)
                                <tr>
                                    <td class="py-3 pr-3 font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                                        {{ $h->tanggal?->isoFormat('dddd, D MMM YYYY') }}
                                    </td>
                                    <td class="py-3 px-3 font-bold text-slate-800 dark:text-slate-100">{{ $h->nama }}</td>
                                    <td class="py-3 px-3 text-slate-500 dark:text-slate-400">{{ $h->keterangan ?: '-' }}</td>
                                    <td class="py-3 pl-3 text-right">
                                        <form action="{{ route('pokja.hari_libur.destroy', $h) }}" method="POST" onsubmit="return confirm('Hapus tanggal merah ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-500 hover:underline cursor-pointer">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 italic">Belum ada tanggal merah terdaftar untuk tahun {{ $year }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
