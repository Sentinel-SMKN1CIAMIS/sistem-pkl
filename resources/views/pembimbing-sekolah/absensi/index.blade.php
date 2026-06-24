<x-app-layout>
    <x-slot name="header">Kehadiran Siswa Bimbingan</x-slot>

    <div x-data="{ isModalOpen: false }">
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <p class="text-slate-600 dark:text-slate-400">Monitoring absensi harian siswa bimbingan Anda selama PKL.</p>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button type="button" @click="isModalOpen = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 justify-center">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                    Ubah/Tambah Absensi
                </button>
                <a href="{{ route('pembimbing_sekolah.absensi.export', request()->all()) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 justify-center">
                    <i data-lucide="download-cloud" class="w-5 h-5"></i>
                    Ekspor Rekapan (PDF)
                </a>
            </div>
        </div>

    <!-- Filters -->
    <div class="glass-card p-6 mb-8">
        <form action="{{ route('pembimbing_sekolah.absensi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Pilih Siswa</label>
                <select name="siswa_id" class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('siswa_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-700 transition-all text-sm">
                    Filter
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'siswa_id']))
                    <a href="{{ route('pembimbing_sekolah.absensi.index') }}" class="p-2 text-slate-500 hover:text-red-400 transition-colors" title="Reset Filter">
                        <i data-lucide="x-circle" class="w-6 h-6"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Masuk</th>
                        <th class="px-6 py-4 whitespace-nowrap">Pulang</th>
                        <th class="px-6 py-4 whitespace-nowrap">GPS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                {{ $row->siswa->nama_lengkap }}
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $row->siswa->dudi->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'label' => 'Hadir'],
                                        'izin' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'label' => 'Izin'],
                                        'sakit' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'label' => 'Sakit'],
                                        'alpha' => ['bg' => 'bg-gray-500/10', 'text' => 'text-gray-400', 'label' => 'Alpa'],
                                    ];
                                    $config = $statusConfig[$row->status] ?? $statusConfig['hadir'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['text'] }}/20">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                @if($row->status === 'hadir' && $row->alasan)
                                    <div x-data="{ expanded: false }" class="mt-1 relative min-w-[150px] max-w-[200px]">
                                        <button @click="expanded = !expanded" type="button" class="inline-flex items-center gap-1 text-[10px] text-orange-500 hover:text-orange-600 dark:hover:text-orange-400 font-medium transition-colors text-left outline-none focus:ring-1 focus:ring-orange-500/50 rounded px-1 -ml-1">
                                            <i data-lucide="info" class="w-3 h-3 shrink-0"></i>
                                            <span x-show="!expanded">{{ Str::limit($row->alasan, 20) }}</span>
                                            <span x-show="expanded" style="display: none;" class="text-orange-600 dark:text-orange-400 font-bold">Sembunyikan</span>
                                        </button>
                                        <div x-show="expanded" 
                                             style="display: none;"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="mt-1.5 p-2.5 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 rounded-lg text-xs text-orange-800 dark:text-orange-200 whitespace-normal leading-relaxed shadow-sm">
                                            {{ $row->alasan }}
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->latitude)
                                    <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" 
                                       class="text-blue-400 hover:text-blue-300">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada riwayat kehadiran dari siswa bimbingan Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-[100] overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
             
            <!-- Background overlay -->
            <div x-show="isModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="isModalOpen = false" aria-hidden="true"></div>

            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0 relative z-10">
                <!-- Modal panel -->
                <div x-show="isModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop
                     class="relative bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                
                <form action="{{ route('pembimbing_sekolah.absensi.store-manual') }}" method="POST">
                    @csrf
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="calendar-edit" class="h-6 w-6 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-slate-100" id="modal-title">
                                    Tambah / Ubah Absensi Manual
                                </h3>
                                <div class="mt-4 space-y-4 text-left">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa</label>
                                        <select name="siswa_id" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($students as $s)
                                                <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->dudi->nama ?? 'Belum ada DUDI' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                                        <input type="date" name="tanggal" required max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                               class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran</label>
                                        <select name="status" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="hadir">Hadir</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="izin">Izin</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan (Opsional)</label>
                                        <textarea name="keterangan" rows="2" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Contoh: Sakit tipes (surat menyusul)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-2 border-t border-slate-100 dark:border-slate-700/50">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition-colors">
                            Simpan Data
                        </button>
                        <button type="button" @click="isModalOpen = false" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    </div> <!-- Close the x-data wrap -->
</x-app-layout>
