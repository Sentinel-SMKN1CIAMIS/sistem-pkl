<x-app-layout>
    <x-slot name="header">Absensi Harian PKL</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-slate-300">
        <!-- Main Action -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 text-center">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-slate-100" id="current-time">00:00:00</h2>
                    <p class="text-slate-400" id="current-date">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>

                @if(!$absensiToday)
                    <!-- Clock In Form -->
                    <form action="{{ route('siswa.absensi.clock-in') }}" method="POST" id="absensi-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="signature" id="signature-input">
                        <input type="hidden" name="latitude" id="latitude-input">
                        <input type="hidden" name="longitude" id="longitude-input">

                        <div class="space-y-2">
                             <label class="block text-sm font-medium text-slate-400 text-left">Tanda Tangan Digital</label>
                             <div class="bg-white rounded-xl overflow-hidden cursor-crosshair">
                                 <canvas id="signature-pad" class="w-full h-40 border border-slate-300"></canvas>
                             </div>
                             <button type="button" id="clear-pad" class="text-[10px] text-slate-500 hover:text-red-400 flex items-center gap-1">
                                 <i data-lucide="refresh-cw" class="w-3 h-3"></i> Bersihkan TTD
                             </button>
                        </div>

                        <button type="button" onclick="submitAbsensi()" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-3 active:scale-95">
                            <i data-lucide="log-in" class="w-6 h-6"></i>
                            ABSEN DATANG SEKARANG
                        </button>
                        <p class="text-[10px] text-slate-500 italic">Harap aktifkan GPS perangkat Anda.</p>
                    </form>
                @elseif(!$absensiToday->waktu_pulang)
                    <!-- Clock Out Form -->
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-6">
                        <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest mb-1">Sudah Hadir</p>
                        <p class="text-2xl font-bold text-slate-200">{{ \Carbon\Carbon::parse($absensiToday->waktu_datang)->format('H:i') }}</p>
                    </div>

                    <form action="{{ route('siswa.absensi.clock-out') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-orange-500 text-white font-black rounded-2xl shadow-xl shadow-orange-500/20 transition-all flex items-center justify-center gap-3 active:scale-95">
                            <i data-lucide="log-out" class="w-6 h-6"></i>
                            ABSEN PULANG
                        </button>
                    </form>
                @else
                    <!-- Finished Today -->
                    <div class="py-12 bg-slate-800/30 rounded-2xl border border-slate-700/50">
                        <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check" class="w-10 h-10 text-emerald-400"></i>
                        </div>
                        <h3 class="text-slate-100 font-bold italic">Selesai Untuk Hari Ini</h3>
                        <p class="text-slate-400 text-sm">Selamat istirahat!</p>
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
                <div class="p-6 border-b border-slate-700/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-100 flex items-center gap-2">
                         <i data-lucide="history" class="w-5 h-5 text-blue-400"></i>
                         Riwayat Kehadiran
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-800/30 border-b border-slate-700/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Datang</th>
                                <th class="px-6 py-4">Pulang</th>
                                <th class="px-6 py-4">GPS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50 text-sm">
                            @foreach($history as $row)
                                <tr class="hover:bg-slate-800/10 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-300">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hadir</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 text-slate-400">{{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4">
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
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Signature Pad
        @if(!$absensiToday)
            const canvas = document.querySelector("#signature-pad");

            // Adjust canvas size to parent container
            function resizeCanvas() {
                const ratio =  Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                // signaturePad.clear(); // clearing canvas normally after resize
            }

            window.addEventListener("resize", resizeCanvas);
            
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
            
            resizeCanvas();

            document.getElementById('clear-pad').addEventListener('click', () => signaturePad.clear());

            // Get Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById('latitude-input').value = position.coords.latitude;
                    document.getElementById('longitude-input').value = position.coords.longitude;
                });
            }

            function submitAbsensi() {
                if (signaturePad.isEmpty()) {
                    alert("Harap isi tanda tangan terlebih dahulu.");
                    return;
                }
                
                document.getElementById('signature-input').value = signaturePad.toDataURL();
                document.getElementById('absensi-form').submit();
            }
        @endif
    </script>
    @endpush
</x-app-layout>
