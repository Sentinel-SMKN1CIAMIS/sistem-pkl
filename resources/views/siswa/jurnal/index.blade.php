<x-app-layout>
    <x-slot name="header">Jurnal Kegiatan PKL</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-400">Catat setiap aktivitas pengerjaan atau pembelajaran di industri sesuai format resmi.</p>
        <div class="flex gap-3">
            <a href="{{ route('siswa.jurnal.export') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl border border-slate-700 transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Jurnal
            </a>
            <a href="{{ route('siswa.jurnal.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Tambah Jurnal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden group">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-sm font-bold text-blue-400">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
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
                             <h3 class="text-lg font-black text-slate-100 mb-2 uppercase tracking-wide decoration-blue-500 underline underline-offset-8 decoration-2">
                                {{ $item->kegiatan }}
                             </h3>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-[10px] text-slate-400">
                                    {{ $item->kompetensi->nama }}
                                </span>
                            </div>
                            <p class="text-slate-400 text-sm line-clamp-2">{{ $item->catatan }}</p>
                        </div>
                        
                        @if($item->foto_path)
                            <div class="w-full md:w-32 h-32 rounded-xl overflow-hidden border border-slate-700">
                                <img src="{{ asset('storage/' . $item->foto_path) }}" alt="Foto Kegiatan" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="flex flex-row md:flex-col justify-end gap-2">
                            @if($item->status === 'pending')
                                <form action="{{ route('siswa.jurnal.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus jurnal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2 text-slate-500 hover:text-red-400 transition-colors">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    @if($item->catatan_pembimbing)
                        <div class="mt-4 p-3 rounded-lg bg-slate-900/50 border border-slate-700/50 italic text-sm text-slate-400">
                            <span class="font-bold text-slate-300 not-italic">Komentar Pembimbing:</span> {{ $item->catatan_pembimbing }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center">
                <div class="w-16 h-16 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="book-open" class="w-8 h-8 text-slate-600"></i>
                </div>
                <h3 class="text-slate-300 font-medium mb-1">Belum Ada Jurnal</h3>
                <p class="text-slate-500 text-sm">Mulai catat aktivitas harianmu sekarang.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>
</x-app-layout>
