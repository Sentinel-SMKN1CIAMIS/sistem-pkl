<x-app-layout>
    <x-slot name="header">Absensi Harian PKL</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-slate-700 dark:text-slate-300">
        <!-- Main Action -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 text-center">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-slate-100" id="current-time">00:00:00</h2>
                    <p class="text-slate-600 dark:text-slate-400" id="current-date">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>

                @if(!$absensiToday)
                    <!-- Clock In Form -->
                    <form action="{{ route('siswa.absensi.clock-in') }}" method="POST" id="absensi-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="signature" id="signature-input">
                        <input type="hidden" name="latitude" id="latitude-input">
                        <input type="hidden" name="longitude" id="longitude-input">

                        <div class="space-y-2">
                             <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 text-left">Tanda Tangan Digital</label>
                             <div class="bg-white rounded-xl overflow-hidden cursor-crosshair">
                                 <canvas id="signature-pad" class="w-full h-40 border border-slate-300"></canvas>
                             </div>
                             <button type="button" id="clear-pad" class="text-[10px] text-slate-500 dark:text-slate-400 hover:text-red-400 flex items-center gap-1">
                                 <i data-lucide="refresh-cw" class="w-3 h-3"></i> Bersihkan TTD
                             </button>
                        </div>

                        <x-button type="button" onclick="submitAbsensi()" variant="emerald" class="w-full py-4 !font-black !rounded-2xl shadow-emerald-500/20" icon="log-in">
                            ABSEN DATANG SEKARANG
                        </x-button>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 italic">Harap aktifkan GPS perangkat Anda.</p>
                    </form>
                @elseif(!$absensiToday->waktu_pulang)
                    <!-- Clock Out Form -->
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-6">
                        <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest mb-1">Sudah Hadir</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($absensiToday->waktu_datang)->format('H:i') }}</p>
                    </div>

                    <form action="{{ route('siswa.absensi.clock-out') }}" method="POST">
                        @csrf
                        <x-button variant="orange" class="w-full py-4 !font-black !rounded-2xl shadow-orange-500/20" icon="log-out">
                            ABSEN PULANG
                        </x-button>
                    </form>
                @else
                    <!-- Finished Today -->
                    <div class="py-12 bg-white dark:bg-slate-800/30 rounded-2xl border border-slate-200/50 dark:border-slate-700/50">
                        <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check" class="w-10 h-10 text-emerald-400"></i>
                        </div>
                        <h3 class="text-slate-900 dark:text-slate-100 font-bold italic">Selesai Untuk Hari Ini</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">Selamat istirahat!</p>
                    </div>
                @endif
            </div>
            
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('success') }}
                </div>
            @endif
             @if(session('error'))
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- History -->
        <div class="lg:col-span-2">
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                         <i data-lucide="history" class="w-5 h-5 text-blue-400"></i>
                         Riwayat Kehadiran
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                                <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                                <th class="px-6 py-4 whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap">Datang</th>
                                <th class="px-6 py-4 whitespace-nowrap">Pulang</th>
                                <th class="px-6 py-4 whitespace-nowrap">GPS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50 text-sm">
                            @foreach($history as $row)
                                <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hadir</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($row->latitude)
                                            <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" class="text-blue-400 hover:text-blue-300 flex items-center">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                {{ $history->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Digital Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('current-time');
            if (el) el.textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Signature Pad
        @if(!$absensiToday)
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signature-pad');
            if (!canvas) return;

            // Prevent touch scrolling on canvas (important for mobile)
            canvas.style.touchAction = 'none';

            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 0.5,
                maxWidth: 2.5
            });

            // Resize canvas to match display size while preserving signature data
            function resizeCanvas() {
                const data = signaturePad.toData();
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear(); // Reset canvas after resize
                if (data && data.length > 0) {
                    signaturePad.fromData(data); // Restore signature data
                }
            }

            // Initial sizing
            resizeCanvas();

            // Debounced resize handler
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(resizeCanvas, 250);
            });

            // Clear button
            document.getElementById('clear-pad').addEventListener('click', function() {
                signaturePad.clear();
            });

            // Get Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude-input').value = position.coords.latitude;
                        document.getElementById('longitude-input').value = position.coords.longitude;
                    },
                    function(error) {
                        console.warn('Geolocation error:', error.message);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }

            // Submit function — exposed globally for onclick
            window.submitAbsensi = function() {
                if (signaturePad.isEmpty()) {
                    alert('Harap isi tanda tangan terlebih dahulu.');
                    return;
                }

                document.getElementById('signature-input').value = signaturePad.toDataURL();
                document.getElementById('absensi-form').submit();
            };
        });
        @endif
    </script>
    @endpush
</x-app-layout>
