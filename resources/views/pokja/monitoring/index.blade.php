<x-app-layout>
    <x-slot name="header">Monitoring Pembimbing</x-slot>

    <div class="mb-6">
        <p class="text-slate-400">Review performa pembimbing sekolah dalam memvalidasi jurnal bimbingan mereka.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($mentors as $mentor)
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-100 leading-tight">{{ $mentor->nama_lengkap }}</h3>
                        <p class="text-[10px] text-slate-500 font-mono tracking-widest uppercase">{{ $mentor->nip }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-700/50 mb-4">
                    <div class="text-center">
                        <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Total Siswa</p>
                        <p class="text-xl font-bold text-slate-200">{{ $mentor->siswa_count }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Status</p>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[9px] font-bold border border-emerald-500/20">AKTIF</span>
                    </div>
                </div>

                <a href="#" class="w-full py-2.5 flex items-center justify-center gap-2 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition-all border border-slate-700/50">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    LIHAT DETAIL BIMBINGAN
                </a>
            </div>
        @endforeach
    </div>
</x-app-layout>
