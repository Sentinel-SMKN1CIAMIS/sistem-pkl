<x-app-layout>
    <x-slot name="header">Monitoring Jurnal Siswa</x-slot>

    <div x-data="{ imageModalOpen: false, modalImageUrl: '' }">
        <div class="mb-6 space-y-4">
            {{-- Info banner berdasarkan tipe guru --}}
            @if($tipe !== 'kejuruan' && $tipe !== 'produktif')
            <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-semibold text-blue-700 dark:text-blue-300">Mode: Guru {{ $tipe === 'umum' ? 'Umum' : ucfirst($tipe) }}</p>
                    <p class="text-blue-600 dark:text-blue-400 text-xs mt-0.5">
                        Menampilkan jurnal yang memiliki CP mengandung:
                        <strong>"{{ $teacher->mapel_cp ?? '-' }}"</strong>.
                        Gunakan pencarian untuk menyaring lebih lanjut.
                    </p>
                </div>
            </div>
            @endif

            {{-- Search bar --}}
            <form method="GET" action="{{ route('pembimbing_sekolah.jurnal.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berdasarkan nama siswa, NIS, atau CP..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl transition-all">
                    Cari
                </button>
                @if(request('search'))
                <a href="{{ route('pembimbing_sekolah.jurnal.index') }}" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-all flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </a>
                @endif
            </form>
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
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-400">
                                {{ $item->kompetensi->nama }}
                            </span>
                            @if($item->tujuanPembelajaran)
                                <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-[10px] text-blue-600 dark:text-blue-300 font-medium">
                                    TP: {{ $item->tujuanPembelajaran->tp ?? $item->tujuanPembelajaran->nama }}
                                </span>
                            @endif
                            @if($item->cp)
                            <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 text-[10px] text-amber-600 dark:text-amber-400">
                                CP: {{ $item->cp }}
                            </span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ $item->deskripsi_pekerjaan }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm italic mb-4">{{ $item->catatan }}</p>

                        @if($item->foto_path)
                            <div class="mb-4">
                                <button type="button" @click="modalImageUrl = '{{ asset('storage/' . $item->foto_path) }}'; imageModalOpen = true" class="inline-flex items-center gap-2 text-xs text-blue-400 hover:underline">
                                    <i data-lucide="image" class="w-4 h-4"></i> Lihat Foto Bukti
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Validasi & ACC/Tolak Actions -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 border-t border-slate-100 dark:border-slate-700/50">
                    <div class="text-xs text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-2">
                        <span>Status Validasi:</span>
                        <span class="font-bold uppercase {{ $item->status === 'valid' ? 'text-emerald-500' : ($item->status === 'invalid' ? 'text-red-500' : 'text-amber-500') }}">{{ $item->status }}</span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span class="italic text-slate-400 dark:text-slate-500">Validasi dilakukan oleh Pembimbing DUDI</span>
                    </div>

                    @if($item->approval_status === 'approved')
                        <div class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-lg mb-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <div>
                                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Jurnal Disetujui</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Oleh: {{ $item->approvedBy->name }} • {{ \Carbon\Carbon::parse($item->approved_at)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                            </div>
                        </div>
                    @elseif($item->approval_status === 'rejected')
                        <div class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg mb-3">
                            <i data-lucide="x-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">Jurnal Ditolak</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mb-1">Oleh: {{ $item->approvedBy->name }} • {{ \Carbon\Carbon::parse($item->approved_at)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                                <p class="text-xs text-red-600 dark:text-red-400 italic">Catatan: {{ $item->approval_notes }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Form untuk ACC/Tolak -->
                        <div x-data="{ rejectModalOpen: false, rejectionNotes: '' }" class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <form action="{{ route('pembimbing_sekolah.jurnal.approve', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white">
                                        <i data-lucide="check" class="w-4 h-4"></i> ACC
                                    </button>
                                </form>
                                <button type="button" @click="rejectModalOpen = true"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white">
                                    <i data-lucide="x" class="w-4 h-4"></i> TOLAK
                                </button>
                            </div>

                            <!-- Reject Modal -->
                            <div x-show="rejectModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
                                <div @click.away="rejectModalOpen = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-md w-full p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Tolak Jurnal</h3>
                                    </div>
                                    <form action="{{ route('pembimbing_sekolah.jurnal.reject', $item) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Catatan Penolakan *</label>
                                            <textarea name="approval_notes" rows="4" required
                                                      class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-red-500 outline-none resize-none transition-all"
                                                      placeholder="Jelaskan alasan penolakan dan saran perbaikan..."></textarea>
                                            @error('approval_notes')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" @click="rejectModalOpen = false"
                                                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold bg-red-600 hover:bg-red-500 text-white transition-all">
                                                Tolak Jurnal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Legacy Saran Guru (hanya jika belum di-ACC/Tolak) -->
                    @if($item->approval_status === 'pending')
                        <form action="{{ route('pembimbing_sekolah.jurnal.update', $item) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3 w-full items-center mt-4 pt-4 border-t border-slate-200 dark:border-slate-700/50">
                            @csrf
                            @method('PATCH')
                            <div class="md:col-span-9 lg:col-span-10">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 block mb-1">Saran Tambahan (Opsional)</label>
                                <textarea name="catatan_guru" rows="1" 
                                          class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all block"
                                          placeholder="Tulis saran atau komentar tambahan untuk siswa...">{{ $item->catatan_guru }}</textarea>
                            </div>
                            <div class="md:col-span-3 lg:col-span-2">
                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-1 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white">
                                    <i data-lucide="save" class="w-4 h-4"></i> SIMPAN
                                </button>
                            </div>
                        </form>
                    @endif
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
