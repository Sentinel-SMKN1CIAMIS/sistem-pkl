<x-app-layout>
    <x-slot name="header">Monitoring Jurnal Siswa</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Pantau aktivitas harian siswa bimbingan Anda di industri.</p>
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
                    <form action="{{ route('pembimbing_sekolah.jurnal.update', $item) }}" method="POST" class="flex flex-col md:flex-row items-start md:items-center gap-4">
                        @csrf
                        @method('PATCH')
                        
                        <div class="flex-grow w-full">
                            <textarea name="catatan_pembimbing" rows="1" 
                                      class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-blue-500"
                                      placeholder="Berikan saran atau alasan penolakan...">{{ $item->catatan_pembimbing }}</textarea>
                        </div>
                        
                        <div class="flex gap-2 w-full md:w-auto shrink-0">
                            <button type="submit" name="status" value="valid" 
                                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $item->status == 'valid' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-emerald-500 hover:text-white' }}">
                                VALIDASI
                            </button>
                            <button type="submit" name="status" value="invalid" 
                                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $item->status == 'invalid' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-red-500 hover:text-white' }}">
                                TOLAK
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
