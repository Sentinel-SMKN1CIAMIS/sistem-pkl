<x-app-layout>
    <x-slot name="header">Laporan Akhir PKL</x-slot>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="mb-6">
            <p class="text-slate-600 dark:text-slate-400">Unggah laporan akhir PKL Anda yang telah disetujui oleh pembimbing industri dan sekolah.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Status Card -->
            <div class="md:col-span-1">
                <div class="glass-card p-6 sticky top-8">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 font-mono">Status Laporan</h3>
                    
                    @if($laporan)
                        <div class="space-y-4">
                            <div>
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                                        'submitted' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest border {{ $statusClasses[$laporan->status] }}">
                                    {{ $laporan->status }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                <p>Terakhir diupdate:</p>
                                <p class="text-slate-700 dark:text-slate-300 font-medium">{{ \Carbon\Carbon::parse($laporan->updated_at)->isoFormat('LLL') }}</p>
                            </div>
                            @if(!empty($laporan->link_media_sosial) && is_array($laporan->link_media_sosial))
                                @foreach($laporan->link_media_sosial as $idx => $link)
                                    @if($link)
                                        <a href="{{ $link }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-lg text-sm transition-all mt-2">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                            Lihat Media Sosial {{ count($laporan->link_media_sosial) > 1 ? ($idx + 1) : '' }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="file-warning" class="w-6 h-6 text-slate-600"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic">Belum mengunggah laporan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload Form -->
            <div class="md:col-span-2">
                <div class="glass-card p-8">
                    @if($laporan && $laporan->status === 'approved')
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-2">Laporan Disetujui!</h3>
                            <p class="text-slate-600 dark:text-slate-400">Laporan Anda telah divalidasi dan tidak perlu diubah lagi.</p>
                        </div>
                    @else
                        <form action="{{ route('siswa.laporan.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="judul" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Judul Laporan</label>
                                <input type="text" name="judul" id="judul" value="{{ old('judul', $laporan->judul ?? '') }}" required
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                       placeholder="Contoh: Laporan PKL Maintenance Server di PT. Maju Jaya">
                            </div>

                            <div>
                                <label for="deskripsi" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi Singkat / Ringkasan</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4"
                                          class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                          placeholder="Tuliskan ringkasan singkat hasil PKL Anda...">{{ old('deskripsi', $laporan->deskripsi ?? '') }}</textarea>
                            </div>

                            @php
                                $oldLinks = old('link_media_sosial', $laporan->link_media_sosial ?? ['']);
                                if (!is_array($oldLinks) || empty($oldLinks)) $oldLinks = [''];
                            @endphp
                            <div x-data="{ links: {{ json_encode($oldLinks) }} }">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Link Media Sosial (Opsional)</label>
                                <template x-for="(link, index) in links" :key="index">
                                    <div class="flex gap-2 mb-3">
                                        <input type="url" x-model="links[index]" :name="'link_media_sosial['+index+']'"
                                               class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                               placeholder="Contoh: https://youtube.com/watch?v=... atau https://tiktok.com/...">
                                        <button type="button" @click="links.splice(index, 1)" x-show="links.length > 1" 
                                                class="px-4 py-2.5 bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl hover:bg-red-500/20 transition-all flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="links.push('')" x-show="links.length < 5" class="text-sm text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1 hover:text-blue-700 dark:hover:text-blue-300 transition-colors mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    Tambah Link (Maksimal 5)
                                </button>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 active:scale-95">
                                    <i data-lucide="send" class="w-5 h-5"></i>
                                    {{ $laporan ? 'Update Laporan' : 'Kirim Laporan' }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
