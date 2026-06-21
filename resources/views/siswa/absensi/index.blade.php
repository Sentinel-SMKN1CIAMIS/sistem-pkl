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

                        <!-- GPS Status Indicator -->
                        <div id="gps-status" class="p-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            <span id="gps-status-text">Mendeteksi lokasi GPS...</span>
                        </div>

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

                        <x-button type="button" onclick="submitAbsensi()" variant="emerald" class="w-full py-4 font-black! rounded-2xl! shadow-emerald-500/20" icon="log-in" id="submit-btn">
                            ABSEN DATANG SEKARANG
                        </x-button>
                    </form>
                @elseif($absensiToday->status === 'hadir' && !$absensiToday->waktu_pulang)
                    <!-- Clock Out Form -->
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-6">
                        <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest mb-1">Sudah Hadir</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($absensiToday->waktu_datang)->format('H:i') }}</p>
                    </div>

                    <form action="{{ route('siswa.absensi.clock-out') }}" method="POST" id="clockout-form">
                        @csrf
                        
                        {{-- Early Leave Request Status --}}
                        @if($absensiToday->early_leave_request_status === 'pending')
                            <div class="mb-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-600 dark:text-blue-400">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="clock" class="w-5 h-5 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">Menunggu Persetujuan Izin Pulang Cepat</p>
                                        <p class="text-xs mt-1 opacity-90">Pembimbing DUDI sedang meninjau permintaan Anda.</p>
                                        <p class="text-xs mt-2 italic">"{{ $absensiToday->early_leave_reason }}"</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($absensiToday->early_leave_request_status === 'approved')
                            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="check-circle" class="w-5 h-5 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">Izin Pulang Cepat Disetujui oleh Pembimbing DUDI</p>
                                        <p class="text-xs mt-1">Anda dapat melakukan absen pulang sekarang.</p>
                                        @if($absensiToday->early_leave_approval_note)
                                            <p class="text-xs mt-2 italic">"{{ $absensiToday->early_leave_approval_note }}"</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($absensiToday->early_leave_request_status === 'rejected')
                            <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-600 dark:text-red-400">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="x-circle" class="w-5 h-5 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">Izin Pulang Cepat Ditolak</p>
                                        <p class="text-xs mt-1">Anda harus menunggu hingga waktu normal pulang (7 jam).</p>
                                        @if($absensiToday->early_leave_approval_note)
                                            <p class="text-xs mt-2 italic">"{{ $absensiToday->early_leave_approval_note }}"</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div id="countdown-info" class="mb-4 p-3 bg-orange-500/10 border border-orange-500/20 rounded-xl text-orange-600 dark:text-orange-400 text-sm hidden">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                            Waktu normal pulang dalam: <span id="countdown-text" class="font-mono font-bold">--:--:--</span>
                        </div>

                        <button 
                            type="submit" 
                            id="clockout-btn"
                            class="w-full px-4 py-4 text-white font-black rounded-2xl shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #f97316, #ea580c);"
                        >
                            <i data-lucide="log-out" class="w-5 h-5 inline mr-2"></i>ABSEN PULANG
                        </button>
                    </form>

                    {{-- Early Leave Request Form (only show if between 1-7 hours and no request yet) --}}
                    <div id="early-leave-request-container" class="mt-4 mb-4 hidden">
                        <form action="{{ route('siswa.absensi.request-early-leave') }}" method="POST" id="early-leave-form" class="p-4 bg-orange-500/5 border border-orange-500/20 rounded-xl">
                            @csrf
                            <label class="block text-sm font-semibold text-orange-600 dark:text-orange-400 mb-2">
                                <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i>
                                Ingin Pulang Lebih Awal?
                            </label>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">Ajukan izin pulang cepat ke pembimbing DUDI Anda.</p>
                            <textarea name="early_leave_reason" rows="2" required
                                      placeholder="Jelaskan alasan Anda ingin pulang lebih awal..." 
                                      class="w-full px-3 py-2 rounded-lg border border-orange-300 dark:border-orange-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent mb-3"></textarea>
                            <button type="submit" class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i> Ajukan Izin Pulang Cepat
                            </button>
                        </form>
                    </div>
                @elseif($absensiToday->status !== 'hadir')
                    <!-- Absence requested today -->
                    <div class="py-12 bg-white dark:bg-slate-800/30 rounded-2xl border border-slate-200/50 dark:border-slate-700/50">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="info" class="w-10 h-10 text-blue-400"></i>
                        </div>
                        <h3 class="text-slate-900 dark:text-slate-100 font-bold italic">Status Hari Ini: {{ ucfirst($absensiToday->status) }}</h3>
                        @if($absensiToday->approval_status === 'pending')
                            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Menunggu persetujuan guru pembimbing.</p>
                        @elseif($absensiToday->approval_status === 'approved')
                            <p class="text-emerald-500 dark:text-emerald-400 text-sm mt-1">Pengajuan telah disetujui.</p>
                        @elseif($absensiToday->approval_status === 'rejected')
                            <p class="text-red-500 dark:text-red-400 text-sm mt-1">Pengajuan ditolak.</p>
                        @endif
                    </div>
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
            @if(!$absensiToday)
            <div class="glass-card p-6">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-orange-400"></i>
                    Tidak Bisa Hadir Hari Ini?
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
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Alasan (minimal 10 karakter)</label>
                        <textarea name="alasan" rows="3" placeholder="Jelaskan alasan Anda..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
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
                                                'alpha' => ['bg' => 'bg-gray-500/10', 'text' => 'text-gray-400', 'label' => 'Alpa'],
                                            ];
                                            $config = $statusConfig[$row->status] ?? $statusConfig['hadir'];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['text'] }}/20">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                        @if($row->status === 'hadir' && $row->alasan)
                                            <br><span class="text-[10px] text-orange-500 font-medium" title="{{ $row->alasan }}"><i data-lucide="info" class="w-3 h-3 inline"></i> {{ Str::limit($row->alasan, 20) }}</span>
                                        @endif
                                    </td>
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
    </script>

    @if($absensiToday && !$absensiToday->waktu_pulang)
    <script>
        // T5.2: Countdown Timer and Button Control with Early Leave Approval (STRICT MODE)
        function updateCountdown() {
            const countdownInfo = document.getElementById('countdown-info');
            const countdownText = document.getElementById('countdown-text');
            const clockoutBtn = document.getElementById('clockout-btn');
            const earlyLeaveRequestContainer = document.getElementById('early-leave-request-container');
            
            if (!countdownText || !clockoutBtn) return;

            // Get early leave request status from backend
            const earlyLeaveStatus = '{{ $absensiToday->early_leave_request_status ?? "none" }}';

            // Parse the clock-in time from database
            const clockInTimeStr = '{{ $absensiToday->waktu_datang }}';
            const [inHours, inMinutes, inSeconds] = clockInTimeStr.split(':').map(Number);
            
            // Create clock-in datetime (today)
            let clockInTime = new Date();
            clockInTime.setHours(inHours, inMinutes, inSeconds, 0);
            
            // Calculate 1 hour and 7 hours later
            const oneHourLater = new Date(clockInTime.getTime() + 1 * 60 * 60 * 1000);
            const sevenHoursLater = new Date(clockInTime.getTime() + 7 * 60 * 60 * 1000);
            
            // Current time
            const now = new Date();
            
            // Check if 1 hour has passed (minimum to enable clock out)
            const diffOneHour = now - oneHourLater;
            
            if (diffOneHour < 0) {
                // LESS THAN 1 HOUR - Disable button, hide all
                clockoutBtn.disabled = true;
                clockoutBtn.style.opacity = '0.5';
                clockoutBtn.style.cursor = 'not-allowed';
                
                countdownInfo.classList.add('hidden');
                earlyLeaveRequestContainer.classList.add('hidden');
                
                return;
            }
            
            // Calculate remaining time until 7 hours (normal work time)
            const diffSevenHours = sevenHoursLater - now;

            if (diffSevenHours <= 0) {
                // MORE THAN 7 HOURS - Enable button, normal clock out
                clockoutBtn.disabled = false;
                clockoutBtn.style.opacity = '1';
                clockoutBtn.style.cursor = 'pointer';
                
                countdownInfo.classList.add('hidden');
                earlyLeaveRequestContainer.classList.add('hidden');
                return;
            }

            // BETWEEN 1-7 HOURS - Check early leave status (STRICT MODE: MUST BE APPROVED)
            
            // Format countdown to 7 hours
            const hours = Math.floor(diffSevenHours / (60 * 60 * 1000));
            const minutes = Math.floor((diffSevenHours % (60 * 60 * 1000)) / (60 * 1000));
            const seconds = Math.floor((diffSevenHours % (60 * 1000)) / 1000);
            const formattedCountdown = `${hours}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
            
            if (earlyLeaveStatus === 'approved') {
                // APPROVED - Enable button (can leave early)
                clockoutBtn.disabled = false;
                clockoutBtn.style.opacity = '1';
                clockoutBtn.style.cursor = 'pointer';
                
                countdownInfo.classList.add('hidden');
                earlyLeaveRequestContainer.classList.add('hidden');
                
            } else if (earlyLeaveStatus === 'pending') {
                // PENDING - Disable button (waiting for approval)
                clockoutBtn.disabled = true;
                clockoutBtn.style.opacity = '0.5';
                clockoutBtn.style.cursor = 'not-allowed';
                
                countdownInfo.classList.add('hidden');
                earlyLeaveRequestContainer.classList.add('hidden');
                
            } else if (earlyLeaveStatus === 'rejected') {
                // REJECTED - Disable button (must wait until 7 hours)
                clockoutBtn.disabled = true;
                clockoutBtn.style.opacity = '0.5';
                clockoutBtn.style.cursor = 'not-allowed';
                
                // Show countdown with rejection message
                countdownInfo.classList.remove('hidden');
                countdownInfo.className = 'mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-600 dark:text-red-400 text-sm';
                countdownInfo.innerHTML = `<i data-lucide="clock" class="w-4 h-4 inline mr-1"></i> Izin pulang cepat ditolak. Tombol aktif dalam: <span class="font-mono font-bold">${formattedCountdown}</span>`;
                
                earlyLeaveRequestContainer.classList.add('hidden');
                
            } else {
                // NONE - Disable button BUT show early leave request form
                clockoutBtn.disabled = true;
                clockoutBtn.style.opacity = '0.5';
                clockoutBtn.style.cursor = 'not-allowed';
                
                // Show countdown
                countdownInfo.classList.remove('hidden');
                countdownInfo.className = 'mb-4 p-3 bg-orange-500/10 border border-orange-500/20 rounded-xl text-orange-600 dark:text-orange-400 text-sm';
                countdownText.textContent = formattedCountdown;
                
                // Show early leave request form
                earlyLeaveRequestContainer.classList.remove('hidden');
            }
            
            // Refresh lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Run immediately on page load
        updateCountdown();
        
        // Update every second
        setInterval(updateCountdown, 1000);
    </script>
    @endif

    @if(!$absensiToday)
    <script>
        // Signature Pad
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

            // GPS Status Variables
            let gpsReady = false;
            const gpsStatusDiv = document.getElementById('gps-status');
            const gpsStatusText = document.getElementById('gps-status-text');
            const submitBtn = document.getElementById('submit-btn');

            // Disable submit button initially
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';

            // Get Geolocation (REQUIRED for clock-in)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Success: GPS detected
                        document.getElementById('latitude-input').value = position.coords.latitude;
                        document.getElementById('longitude-input').value = position.coords.longitude;
                        gpsReady = true;

                        // Update UI to success state
                        gpsStatusDiv.className = 'p-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400';
                        gpsStatusDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i><span>Lokasi GPS terdeteksi</span>';
                        
                        // Enable submit button
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.style.cursor = 'pointer';

                        // Refresh lucide icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    },
                    function(error) {
                        // Error: GPS failed
                        gpsReady = false;
                        let errorMessage = 'GPS tidak dapat diakses';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Izin akses lokasi ditolak. Harap aktifkan lokasi di pengaturan browser.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS perangkat aktif.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Waktu tunggu GPS habis. Coba refresh halaman.';
                                break;
                        }

                        // Update UI to error state
                        gpsStatusDiv.className = 'p-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400';
                        gpsStatusDiv.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4"></i><span>' + errorMessage + '</span>';
                        
                        // Keep submit button disabled
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.5';
                        submitBtn.style.cursor = 'not-allowed';

                        // Refresh lucide icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }

                        console.error('GPS Error:', error);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            } else {
                // Browser doesn't support geolocation
                gpsStatusDiv.className = 'p-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400';
                gpsStatusDiv.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4"></i><span>Browser tidak mendukung GPS</span>';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // Submit function — exposed globally for onclick
            window.submitAbsensi = function() {
                if (!gpsReady) {
                    alert('Lokasi GPS wajib diaktifkan untuk melakukan absensi hadir.\n\nHarap:\n1. Aktifkan lokasi di pengaturan perangkat Anda\n2. Izinkan akses lokasi untuk browser\n3. Refresh halaman ini');
                    return;
                }

                if (signaturePad.isEmpty()) {
                    alert('Harap isi tanda tangan terlebih dahulu.');
                    return;
                }

                document.getElementById('signature-input').value = signaturePad.toDataURL();
                document.getElementById('absensi-form').submit();
            };
        });
    </script>
    @endif
    @endpush
</x-app-layout>
