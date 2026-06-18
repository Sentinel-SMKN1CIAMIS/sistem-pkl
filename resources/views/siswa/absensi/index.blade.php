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
                             <p class="text-[10px] text-orange-500 dark:text-orange-400 mt-1 italic">* Geser layar di luar kotak putih ini untuk men-scroll halaman ke bawah</p>
                        </div>

                        <x-button type="button" onclick="submitAbsensi()" variant="emerald" class="w-full py-4 !font-black !rounded-2xl shadow-emerald-500/20" icon="log-in">
                            ABSEN DATANG SEKARANG
                        </x-button>
                    </form>
                @elseif(!$absensiToday->waktu_pulang)
                    <!-- Clock Out Form -->
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-6">
                        <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest mb-1">Sudah Hadir</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($absensiToday->waktu_datang)->format('H:i') }}</p>
                    </div>

                    <form action="{{ route('siswa.absensi.clock-out') }}" method="POST" id="clockout-form">
                        @csrf
                        <button 
                            type="submit" 
                            id="clockout-btn"
                            disabled
                            class="w-full px-4 py-4 text-white font-black rounded-2xl shadow-lg cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #6b7280, #4b5563) !important; color: white !important;"
                        >
                            <span id="button-text">Bisa absen pulang dalam: <span id="countdown-text" class="font-mono">--:--:--</span></span>
                        </button>

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

            <!-- T5.3: Absence Request Form -->
            @if($absensiToday && $absensiToday->waktu_pulang)
            <div class="glass-card p-6">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-orange-400"></i>
                    Tidak Bisa Hadir?
                </h3>
                
                <form action="{{ route('siswa.absensi.submit-absence-request') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-500/10 cursor-pointer">
                                <input type="radio" name="status" value="izin" class="w-4 h-4 text-blue-500">
                                <span class="text-sm">Izin</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-500/10 cursor-pointer">
                                <input type="radio" name="status" value="sakit" class="w-4 h-4 text-red-500">
                                <span class="text-sm">Sakit</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-500/10 cursor-pointer">
                                <input type="radio" name="status" value="alpa" class="w-4 h-4 text-gray-500">
                                <span class="text-sm">Alpa</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Alasan (minimal 10 karakter)</label>
                        <textarea name="alasan" rows="3" placeholder="Jelaskan alasan Anda..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ \Carbon\Carbon::today()->toDateString() }}" max="{{ \Carbon\Carbon::today()->toDateString() }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <x-button type="submit" variant="blue" class="w-full" icon="send">
                        Ajukan Permintaan
                    </x-button>
                </form>
            </div>
            @endif
            
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
                                <th class="px-6 py-4 whitespace-nowrap">Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50 text-sm">
                            @foreach($history as $row)
                                <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'hadir' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'label' => 'Hadir'],
                                                'izin' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'label' => 'Izin'],
                                                'sakit' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'label' => 'Sakit'],
                                                'alpa' => ['bg' => 'bg-gray-500/10', 'text' => 'text-gray-400', 'label' => 'Alpa'],
                                            ];
                                            $config = $statusConfig[$row->status] ?? $statusConfig['hadir'];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['text'] }}/20">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $approvalConfig = [
                                                'pending' => ['bg' => 'bg-yellow-500/10', 'text' => 'text-yellow-400', 'label' => 'Menunggu', 'icon' => 'clock'],
                                                'approved' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'label' => 'Disetujui', 'icon' => 'check'],
                                                'rejected' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'label' => 'Ditolak', 'icon' => 'x'],
                                            ];
                                            $approvalStatus = $row->approval_status ?? 'pending';
                                            $approvalConf = $approvalConfig[$approvalStatus] ?? $approvalConfig['pending'];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $approvalConf['bg'] }} {{ $approvalConf['text'] }} border {{ $approvalConf['text'] }}/20 flex items-center gap-1 w-fit">
                                            <i data-lucide="{{ $approvalConf['icon'] }}" class="w-3 h-3"></i>
                                            {{ $approvalConf['label'] }}
                                        </span>
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

        // T5.2: Countdown Timer for 7-hour requirement
        @if($absensiToday && !$absensiToday->waktu_pulang)
        function updateCountdown() {
            const clockOutBtn = document.getElementById('clockout-btn');
            const countdownText = document.getElementById('countdown-text');
            
            if (!clockOutBtn || !countdownText) return;

            // Parse the clock-in time from database
            const clockInTimeStr = '{{ $absensiToday->waktu_datang }}';
            const [inHours, inMinutes, inSeconds] = clockInTimeStr.split(':').map(Number);
            
            // Create clock-in datetime (today)
            let clockInTime = new Date();
            clockInTime.setHours(inHours, inMinutes, inSeconds, 0);
            
            // Calculate 7 hours later
            const sevenHoursLater = new Date(clockInTime.getTime() + 7 * 60 * 60 * 1000);
            
            // Current time
            const now = new Date();
            
            // Calculate remaining time
            const diff = sevenHoursLater - now;

            if (diff <= 0) {
                // Time's up - enable button and change text
                clockOutBtn.disabled = false;
                clockOutBtn.style.background = 'linear-gradient(135deg, #f97316, #ea580c)';
                clockOutBtn.style.color = 'white';
                clockOutBtn.classList.remove('cursor-not-allowed');
                document.getElementById('button-text').innerHTML = '<i data-lucide="log-out" class="w-5 h-5 inline mr-2"></i>ABSEN PULANG';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
                return;
            }

            // Still waiting - keep button disabled and show countdown
            clockOutBtn.disabled = true;
            clockOutBtn.style.background = 'linear-gradient(135deg, #6b7280, #4b5563)';
            clockOutBtn.style.color = 'white';
            clockOutBtn.classList.add('cursor-not-allowed');
            
            // Format countdown
            const hours = Math.floor(diff / (60 * 60 * 1000));
            const minutes = Math.floor((diff % (60 * 60 * 1000)) / (60 * 1000));
            const seconds = Math.floor((diff % (60 * 1000)) / 1000);
            
            const formattedCountdown = `${hours}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
            countdownText.textContent = formattedCountdown;
        }

        // Run immediately on page load
        updateCountdown();
        
        // Update every second
        setInterval(updateCountdown, 1000);
        @endif

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

            // Get Geolocation (silently, no user notification)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude-input').value = position.coords.latitude;
                        document.getElementById('longitude-input').value = position.coords.longitude;
                    },
                    function(error) {
                        // Silently fail - GPS is captured in background without user knowing
                        console.warn('GPS not available');
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
