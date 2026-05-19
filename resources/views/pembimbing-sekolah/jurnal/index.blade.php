<x-app-layout>
    <x-slot name="header">Monitoring Jurnal Siswa</x-slot>

    <div class="mb-6 flex justify-between items-start">
        <p class="text-slate-600 dark:text-slate-400">Pantau aktivitas harian siswa bimbingan Anda di industri.</p>
        
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" title="Opsi Lainnya">
                <i data-lucide="more-vertical" class="w-5 h-5"></i>
            </button>
            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 glass-card border border-slate-200/50 dark:border-slate-700/50 py-1 rounded-xl text-sm z-10" x-cloak>
                <form action="{{ route('pembimbing_sekolah.jurnal.validasi_semua') }}" method="POST" onsubmit="return confirm('PERHATIAN: Anda yakin ingin memvalidasi SEMUA jurnal yang berstatus pending tanpa membacanya satu per satu?');">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-2 transition-colors">
                        <i data-lucide="check-check" class="w-4 h-4"></i> Validasi Semua Jurnal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-400 font-bold">
                            {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ $item->siswa->nama_lengkap }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
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
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400 mb-2 inline-block">
                            {{ $item->kompetensi->nama }}
                        </span>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>

                        @if($item->foto_path)
                            <div class="mb-4">
                                <a href="{{ asset('storage/' . $item->foto_path) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-blue-400 hover:underline">
                                    <i data-lucide="image" class="w-4 h-4"></i> Lihat Foto Bukti
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar Action Validation -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 border-t border-slate-100 dark:border-slate-700/50">
                    <form action="{{ route('pembimbing_sekolah.jurnal.update', $item) }}" method="POST" class="flex flex-col lg:flex-row items-start lg:items-center gap-4 w-full">
                        @csrf
                        @method('PATCH')
                        
                        <div class="w-full lg:flex-1">
                            <textarea name="catatan_pembimbing" rows="1" 
                                      class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all"
                                      placeholder="Berikan saran atau alasan penolakan (opsional)...">{{ $item->catatan_pembimbing }}</textarea>
                        </div>
                        
                        <div class="flex gap-3 w-full lg:w-auto shrink-0">
                            <button type="submit" name="status" value="valid" 
                                    class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-6 py-2.5 rounded-xl text-sm font-bold transition-all border {{ $item->status == 'valid' ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white' }}">
                                <i data-lucide="check" class="w-4 h-4"></i> VALIDASI
                            </button>
                            <button type="submit" name="status" value="invalid" 
                                    class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-6 py-2.5 rounded-xl text-sm font-bold transition-all border {{ $item->status == 'invalid' ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white' }}">
                                <i data-lucide="x" class="w-4 h-4"></i> TOLAK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
             <div class="glass-card p-12 text-center text-slate-500 dark:text-slate-400 italic">
                Belum ada aktivitas jurnal dari siswa bimbingan Anda.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>
</x-app-layout>
