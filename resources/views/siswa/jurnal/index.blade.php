<x-app-layout>
    <x-slot name="header">Jurnal Kegiatan PKL</x-slot>

    <style>
        .jurnal-header-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .jurnal-photo-container {
            width: 100% !important;
            height: 192px !important;
            border-radius: 0.75rem !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
        }
        @media (min-width: 768px) {
            .jurnal-header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .jurnal-photo-container {
                width: 192px !important;
                height: 128px !important;
            }
        }
    </style>

    <div x-data="{ 
            selectedDate: '{{ \Carbon\Carbon::today()->format('Y-m-d') }}',
            imageModalOpen: false, 
            modalImageUrl: '' 
        }">
        
        <!-- Header & Top Actions -->
        <div class="mb-6 jurnal-header-container">
            <p class="text-slate-600 dark:text-slate-400 max-w-xl">Catat setiap aktivitas pengerjaan atau pembelajaran di industri sesuai format resmi.</p>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0 mt-4 sm:mt-0">
                <a href="{{ route('siswa.jurnal.export') }}" target="_blank" class="w-full md:w-auto shrink-0 px-4 py-2 text-sm whitespace-nowrap bg-white dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Jurnal
                </a>
                <a href="{{ route('siswa.jurnal.portofolio') }}" target="_blank" class="w-full md:w-auto shrink-0 px-4 py-2 text-sm whitespace-nowrap bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="book" class="w-4 h-4"></i>
                    Cetak Portofolio
                </a>
                @if(auth()->user()->siswa?->status_pkl === 'selesai')
                <a href="{{ route('siswa.jurnal.sertifikat') }}" class="w-full md:w-auto shrink-0 px-4 py-2 text-sm whitespace-nowrap bg-amber-500 hover:bg-amber-400 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    Cetak Sertifikat
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(!$hasAbsenToday)
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 dark:text-amber-400 text-sm flex items-center gap-3">
                <i data-lucide="info" class="w-4 h-4"></i>
                <div>
                    <span class="font-bold">Perhatian:</span> Anda belum melakukan absensi hari ini. Silakan melakukan 
                    <a href="{{ route('siswa.absensi.index') }}" class="underline font-semibold hover:text-amber-400 dark:hover:text-amber-300">absensi terlebih dahulu</a> 
                    agar tombol tambah jurnal untuk hari ini diaktifkan.
                </div>
            </div>
        @endif

        <!-- Calendar UI -->
        @php
            $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            $currentMonthName = $months[$currentDate->month];
            $currentYear = $currentDate->year;
            
            $prevMonth = $currentDate->copy()->subMonth();
            $nextMonth = $currentDate->copy()->addMonth();
        @endphp

        <div class="glass-card p-4 md:p-6 mb-6">
            <!-- Calendar Navigation -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-blue-500"></i>
                    {{ $currentMonthName }} {{ $currentYear }}
                </h2>
                <div class="flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('siswa.jurnal.index', ['month' => $prevMonth->format('m'), 'year' => $prevMonth->format('Y')]) }}" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg transition-colors">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ route('siswa.jurnal.index') }}" class="px-3 py-2 text-sm font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                        Bulan Ini
                    </a>
                    <a href="{{ route('siswa.jurnal.index', ['month' => $nextMonth->format('m'), 'year' => $nextMonth->format('Y')]) }}" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg transition-colors">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 sm:gap-3 mb-6">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div class="text-center text-xs font-bold text-slate-400 uppercase tracking-wider py-2">
                        {{ $day }}
                    </div>
                @endforeach
                
                @foreach($calendar as $dayData)
                    @if(!$dayData['is_current_month'])
                        <div class="p-2 sm:p-3 rounded-xl bg-slate-50/50 dark:bg-slate-800/20 border border-slate-100 dark:border-slate-800/50 opacity-40"></div>
                    @else
                        @php
                            $statusColors = [
                                'approved' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800',
                                'belum_diisi' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                'akan_datang' => 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-700',
                                'libur' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                'terlewat' => 'bg-slate-200 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 border-slate-300 dark:border-slate-600 border-dashed',
                            ];
                            
                            $icons = [
                                'approved' => 'check-circle',
                                'pending' => 'clock',
                                'rejected' => 'x-circle',
                                'belum_diisi' => 'edit-3',
                                'akan_datang' => 'minus',
                                'libur' => 'user-x',
                                'terlewat' => 'calendar-x',
                            ];
                            
                            $bgClass = $statusColors[$dayData['status']] ?? $statusColors['belum_diisi'];
                            $iconName = $icons[$dayData['status']] ?? $icons['belum_diisi'];
                            
                            $isToday = $dayData['is_today'] ? 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-800 shadow-md transform scale-105' : '';
                        @endphp
                        
                        <button type="button" 
                            @click="selectedDate = '{{ $dayData['date'] }}'; $nextTick(() => { document.getElementById('jurnal-detail-area')?.scrollIntoView({ behavior: 'smooth', block: 'start' }) })"
                            :class="selectedDate === '{{ $dayData['date'] }}' ? 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-800 scale-105 z-10' : ''"
                            class="relative flex flex-col items-center justify-center py-2 px-1 sm:p-3 rounded-xl border {{ $bgClass }} {{ $isToday }} hover:scale-105 hover:shadow-md transition-all duration-200 cursor-pointer">
                            
                            <span class="text-sm sm:text-lg font-black leading-none mb-1 sm:mb-1.5">{{ $dayData['day'] }}</span>
                            <i data-lucide="{{ $iconName }}" class="w-3 h-3 sm:w-4 sm:h-4 opacity-80"></i>
                        </button>
                    @endif
                @endforeach
            </div>
            
            <!-- Legend -->
            <div class="flex flex-wrap justify-center gap-3 sm:gap-5 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700/50">
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-blue-500"></div> Hari Ini</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-emerald-400"></div> Divalidasi</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-yellow-400"></div> Menunggu</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-red-400"></div> Ditolak</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-orange-400"></div> Belum Diisi</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full border border-dashed border-slate-400 bg-slate-200 dark:bg-slate-700"></div> Terlewat</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-blue-300"></div> Izin/Sakit</div>
                <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400"><div class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-600"></div> Akan datang</div>
            </div>
        </div>

        <!-- Jurnal Details Area -->
        <div id="jurnal-detail-area" class="mt-6 min-h-[300px]">
            @foreach($calendar as $dayData)
                @if($dayData['is_current_month'])
                    <div x-show="selectedDate === '{{ $dayData['date'] }}'" 
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4" 
                         x-transition:enter-end="opacity-100 translate-y-0">
                        
                        @if($dayData['jurnal'])
                            @php $item = $dayData['jurnal']; @endphp
                            <!-- Render Jurnal Card -->
                            <div class="glass-card overflow-hidden group">
                                <div class="p-6 border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20">
                                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i> Detail Jurnal
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row justify-between gap-6">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="text-sm font-bold text-blue-500 dark:text-blue-400">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                                        'approved' => 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border-emerald-500/20',
                                                        'rejected' => 'bg-red-500/10 text-red-500 dark:text-red-400 border-red-500/20'
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'Menunggu Verifikasi',
                                                        'approved' => 'Valid',
                                                        'rejected' => 'Ditolak'
                                                    ];
                                                    $statusKey = $item->approval_status ?? 'pending';
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $statusClasses[$statusKey] }}">
                                                    {{ $statusLabels[$statusKey] }}
                                                </span>
                                            </div>
                                             <h3 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-2 uppercase tracking-wide decoration-blue-500 underline underline-offset-8 decoration-2">
                                                {{ $item->deskripsi_pekerjaan }}
                                             </h3>
                                            <div class="flex items-center gap-2 mb-4 mt-4 flex-wrap">
                                                <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 font-medium shadow-sm">
                                                    {{ $item->kompetensi?->nama ?? 'Tidak Ada Kompetensi' }}
                                                </span>
                                                @if($item->tujuanPembelajaran)
                                                    <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-xs text-blue-600 dark:text-blue-300 font-medium shadow-sm">
                                                        TP: {{ $item->tujuanPembelajaran->tp ?? $item->tujuanPembelajaran->nama }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="prose prose-sm dark:prose-invert max-w-none mt-4 text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                                {!! nl2br(e($item->catatan)) !!}
                                            </div>
                                        </div>
                                        
                                        @if($item->foto_path)
                                            <button type="button" 
                                                    @click="modalImageUrl = '{{ asset('storage/' . $item->foto_path) }}'; imageModalOpen = true" 
                                                    class="jurnal-photo-container border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden group/photo cursor-zoom-in relative bg-slate-100 dark:bg-slate-800">
                                                <img src="{{ asset('storage/' . $item->foto_path) }}" alt="Foto Kegiatan" class="w-full h-full object-cover transition-transform duration-300 group-hover/photo:scale-105">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/photo:opacity-100 transition-opacity flex items-center justify-center">
                                                    <i data-lucide="zoom-in" class="w-6 h-6 text-white"></i>
                                                </div>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-row justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                                        @if($statusKey === 'pending')
                                            <a href="{{ route('siswa.jurnal.edit', $item) }}" class="px-4 py-2 bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-blue-900/20 text-slate-600 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400 font-medium rounded-xl transition-colors flex items-center gap-2 text-sm border border-slate-200 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-800">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i> Edit
                                            </a>
                                            <form action="{{ route('siswa.jurnal.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus jurnal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-red-50 dark:bg-slate-800 dark:hover:bg-red-900/20 text-slate-600 hover:text-red-600 dark:text-slate-300 dark:hover:text-red-400 font-medium rounded-xl transition-colors flex items-center gap-2 text-sm border border-slate-200 dark:border-slate-700 hover:border-red-200 dark:hover:border-red-800">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    @if($item->approval_notes)
                                        <div class="mt-4 space-y-3">
                                            <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 text-sm text-slate-700 dark:text-slate-300">
                                                <div class="flex items-center gap-2 font-bold mb-2 text-slate-800 dark:text-slate-200">
                                                    <i data-lucide="message-square" class="w-4 h-4 text-blue-500"></i> Catatan Evaluasi:
                                                </div>
                                                <p class="italic leading-relaxed">"{{ $item->approval_notes }}"</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="glass-card p-8 md:p-12 text-center border-dashed border-2 border-slate-300 dark:border-slate-700">
                                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-slate-200 dark:border-slate-700/50">
                                    @if($dayData['status'] === 'akan_datang')
                                        <i data-lucide="calendar" class="w-10 h-10 text-slate-400"></i>
                                    @elseif($dayData['status'] === 'libur')
                                        <i data-lucide="user-x" class="w-10 h-10 text-blue-400"></i>
                                    @else
                                        <i data-lucide="edit-3" class="w-10 h-10 text-orange-400"></i>
                                    @endif
                                </div>
                                
                                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-2">
                                    {{ \Carbon\Carbon::parse($dayData['date'])->isoFormat('dddd, D MMMM YYYY') }}
                                </h3>
                                
                                @if($dayData['status'] === 'akan_datang')
                                    <p class="text-slate-500 dark:text-slate-400 mb-0">Tanggal ini belum tiba. Anda belum bisa mengisi jurnal.</p>
                                @elseif($dayData['status'] === 'libur')
                                    <p class="text-slate-500 dark:text-slate-400 mb-0">Anda sedang <span class="font-bold uppercase text-blue-500">{{ $dayData['absensi']->status ?? '' }}</span> pada tanggal ini. Jurnal tidak wajib diisi.</p>
                                @else
                                    <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">Anda belum mengisi catatan aktivitas jurnal PKL pada tanggal ini.</p>
                                    
                                    @php
                                        $diffInDays = \Carbon\Carbon::parse($dayData['date'])->diffInDays(\Carbon\Carbon::today(), false);
                                        $isAllowedBackdate = $diffInDays >= 0 && $diffInDays <= $maxBackdateDays;
                                        $isTodayStr = $dayData['date'] === \Carbon\Carbon::today()->format('Y-m-d');
                                    @endphp
                                    
                                    @if($isTodayStr)
                                        @if($hasAbsenToday)
                                            <a href="{{ route('siswa.jurnal.create', ['date' => $dayData['date']]) }}" class="inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all items-center gap-2 hover:-translate-y-0.5">
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                                Isi Jurnal Sekarang
                                            </a>
                                        @else
                                            <div class="inline-flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl border border-amber-200 dark:border-amber-800/50 text-sm font-medium">
                                                <i data-lucide="info" class="w-4 h-4"></i>
                                                <span>Silakan absensi hari ini terlebih dahulu untuk mengisi jurnal.</span>
                                            </div>
                                        @endif
                                    @elseif($isAllowedBackdate)
                                        <a href="{{ route('siswa.jurnal.create', ['date' => $dayData['date']]) }}" class="inline-flex px-6 py-3 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-xl shadow-lg shadow-orange-500/25 transition-all items-center gap-2 hover:-translate-y-0.5">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            Isi Jurnal Susulan
                                        </a>
                                        <p class="text-xs text-slate-400 mt-3">Sisa waktu susulan: {{ $maxBackdateDays - $diffInDays }} hari lagi</p>
                                    @else
                                        <div class="inline-flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl border border-red-200 dark:border-red-800/50 text-sm font-medium">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            <span>Batas waktu pengisian jurnal ({{ $maxBackdateDays }} hari) sudah lewat.</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                        
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Image Modal -->
        <template x-teleport="body">
            <div x-show="imageModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display: none;" 
                 class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6 md:p-8 cursor-zoom-out"
                 @click="imageModalOpen = false"
                 @keydown.escape.window="imageModalOpen = false">
                
                <div @click.stop
                     class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center cursor-default">
                     
                    <button @click="imageModalOpen = false" 
                            class="absolute -top-12 right-0 md:-top-4 md:-right-12 z-50 p-2.5 text-white bg-slate-800/80 hover:bg-red-600 border border-slate-700/50 rounded-full shadow-xl transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500"
                            title="Tutup (Esc)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <div class="w-full h-full flex items-center justify-center bg-slate-900/20 border border-white/5 rounded-3xl p-2 shadow-2xl overflow-hidden">
                        <img :src="modalImageUrl" 
                             class="max-w-full max-h-[75vh] md:max-h-[80vh] rounded-2xl object-contain shadow-inner selection:bg-transparent"
                             alt="Foto Bukti Kegiatan">
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a :href="modalImageUrl" 
                           target="_blank" 
                           class="px-4 py-2 bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold rounded-xl border border-slate-700/50 transition-all flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Buka di Tab Baru
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
