<x-app-layout>
    <x-slot name="header">Validasi Jurnal Siswa</x-slot>

    <div x-data="{ imageModalOpen: false, modalImageUrl: '' }">
        <div class="mb-6 flex justify-between items-center">
            <p class="text-slate-600 dark:text-slate-400">Review dan berikan feedback pada laporan harian siswa PKL di perusahaan Anda.</p>
        </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($jurnals as $item)
            <div class="glass-card overflow-hidden transition-all duration-300 {{ $item->status === 'pending' ? 'border-l-4 border-amber-500' : '' }}">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-400 font-bold">
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

                            <div class="mt-4">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400">
                                        {{ $item->kompetensi->nama }}
                                    </span>
                                    @if($item->tujuanPembelajaran)
                                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-[10px] text-blue-600 dark:text-blue-300 font-medium">
                                            TP: {{ $item->tujuanPembelajaran->tp ?? $item->tujuanPembelajaran->nama }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                                <p class="text-slate-600 dark:text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>
                            </div>

                            @if($item->foto_path)
                                <div class="mb-4">
                                    <button type="button" @click="modalImageUrl = '{{ asset('storage/' . $item->foto_path) }}'; imageModalOpen = true" class="inline-flex items-center gap-2 text-xs text-blue-400 hover:underline">
                                        <i data-lucide="image" class="w-4 h-4"></i> Lihat Foto Bukti
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Sidebar Action Validation -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 border-t border-slate-100 dark:border-slate-700/50">
                    @if($item->status === 'pending')
                        <form action="{{ route('pembimbing_dudi.jurnal.update', $item) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 w-full items-center">
                            @csrf
                            @method('PATCH')
                            
                            <div class="md:col-span-7 lg:col-span-8">
                                <textarea name="catatan_pembimbing" rows="1" 
                                          class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all block"
                                          placeholder="Berikan saran atau alasan penolakan (opsional)...">{{ $item->catatan_pembimbing }}</textarea>
                            </div>
                            
                            <div class="md:col-span-5 lg:col-span-4 flex gap-2 w-full">
                                <button type="submit" name="status" value="valid" 
                                        class="flex-1 flex items-center justify-center gap-1 px-2 py-2.5 rounded-xl text-xs font-bold transition-all border bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white">
                                    <i data-lucide="check" class="w-4 h-4"></i> VALIDASI
                                </button>
                                <button type="submit" name="status" value="invalid" 
                                        class="flex-1 flex items-center justify-center gap-1 px-2 py-2.5 rounded-xl text-xs font-bold transition-all border bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white">
                                    <i data-lucide="x" class="w-4 h-4"></i> TOLAK
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                            Status Jurnal: <span class="font-bold uppercase {{ $item->status === 'valid' ? 'text-emerald-500' : 'text-red-500' }}">{{ $item->status }}</span>
                        </div>
                        <form action="{{ route('pembimbing_dudi.jurnal.update', $item) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 w-full items-center">
                            @csrf
                            @method('PATCH')
                            
                            <div class="md:col-span-9 lg:col-span-10">
                                <textarea name="catatan_pembimbing" rows="1" 
                                          class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all block"
                                          placeholder="Tambahkan atau ubah saran Anda...">{{ $item->catatan_pembimbing }}</textarea>
                            </div>
                            
                            <div class="md:col-span-3 lg:col-span-2">
                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-1 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white">
                                    <i data-lucide="save" class="w-4 h-4"></i> SIMPAN SARAN
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @empty
             <div class="glass-card p-12 text-center text-slate-500 dark:text-slate-400 italic">
                Semua jurnal siswa telah diproses atau belum ada data.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jurnals->links() }}
    </div>

    <!-- Image Modal -->
    <div x-show="imageModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div @click.away="imageModalOpen = false" class="relative max-w-4xl max-h-screen">
            <button @click="imageModalOpen = false" class="absolute -top-4 -right-4 p-2 text-white bg-red-600 rounded-full hover:bg-red-500 shadow-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <img :src="modalImageUrl" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl object-contain">
        </div>
    </div>
    </div>
</x-app-layout>
