<x-app-layout>
    <x-slot name="header">Monitoring Jurnal Siswa</x-slot>

    <div class="mb-6">
        <p class="text-slate-400">Pantau aktivitas harian siswa bimbingan Anda di industri.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-emerald-400 font-bold">
                            {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-100 block">{{ $item->siswa->nama_lengkap }}</span>
                            <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>
                        <div class="ml-auto">
                             @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    'valid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'invalid' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $statusClasses[$item->status] }}">
                                {{ $item->status }}
                            </span>
                        </div>
                    </div>

                    <div class="pl-14">
                        <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-[10px] text-slate-400 mb-2 inline-block">
                            {{ $item->kompetensi->nama }}
                        </span>
                        <h3 class="text-base font-semibold text-slate-200 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                        <p class="text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>

                        @if($item->catatan_pembimbing)
                            <div class="p-3 rounded-lg bg-blue-500/5 border border-blue-500/10 text-xs text-blue-300/80">
                                <span class="font-bold opacity-100">Feedback Mentor Industri:</span> {{ $item->catatan_pembimbing }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
             <div class="glass-card p-12 text-center text-slate-500 italic">
                Belum ada aktivitas jurnal dari siswa bimbingan Anda.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>
</x-app-layout>
