<x-app-layout>
    <x-slot name="header">Log Kunjungan Lapangan</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Form Catat Kunjungan --}}
        <div class="lg:col-span-1">
            <div class="glass-card p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-600"></i>
                    Catat Kunjungan Supervisi DUDI
                </h3>

                <form action="{{ route('pembimbing_sekolah.kunjungan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="latitude" id="kunjungan-lat" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="kunjungan-lng" value="{{ old('longitude') }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Tempat DUDI</label>
                        <select name="dudi_id" required
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                            <option value="">-- Pilih DUDI --</option>
                            @foreach($dudis as $dudi)
                                <option value="{{ $dudi->id }}" {{ old('dudi_id') == $dudi->id ? 'selected' : '' }}>{{ $dudi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Tanggal Kunjungan</label>
                        <input type="date" name="tanggal_kunjungan" required value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Catatan Hasil Monitoring</label>
                        <textarea name="catatan_monitoring" rows="3" required minlength="10"
                                  placeholder="Catat perkembangan siswa, kehadiran, dan komunikasi dengan pembimbing DUDI..."
                                  class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">{{ old('catatan_monitoring') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Kendala di Lapangan (Opsional)</label>
                        <textarea name="kendala" rows="2"
                                  placeholder="Jika ada kendala yang perlu diketahui Pokja..."
                                  class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">{{ old('kendala') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Foto Dokumentasi (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        <p class="text-[10px] text-slate-400 mt-1">Otomatis dikompres di server (maks. 15MB).</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
                        <div class="text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-200 block">Lokasi GPS Kunjungan</span>
                            <span id="kunjungan-gps-status" class="text-[11px] text-slate-500">Belum diambil (opsional)</span>
                        </div>
                        <button type="button" onclick="captureVisitGps()"
                                class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer shrink-0">
                            Ambil GPS
                        </button>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-colors cursor-pointer">
                        Simpan Log Kunjungan
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Riwayat Kunjungan --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="glass-card p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-emerald-500"></i>
                    Riwayat Kunjungan Lapangan Anda
                </h3>

                @forelse($kunjungans as $item)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/60 mb-3 last:mb-0">
                        <div class="flex flex-col sm:flex-row justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $item->dudi?->nama ?? '-' }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        {{ $item->tanggal_kunjungan?->isoFormat('D MMMM YYYY') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">{{ $item->catatan_monitoring }}</p>
                                @if($item->kendala)
                                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5">
                                        <strong>Kendala:</strong> {{ $item->kendala }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-3 mt-3">
                                    @if($item->latitude && $item->longitude)
                                        <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Lihat Lokasi GPS
                                        </a>
                                    @endif
                                    <form action="{{ route('pembimbing_sekolah.kunjungan.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus log kunjungan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] font-bold text-red-500 hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            @if($item->foto_path)
                                <a href="{{ asset('storage/' . $item->foto_path) }}" target="_blank" class="w-full sm:w-28 h-28 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 block">
                                    <img src="{{ asset('storage/' . $item->foto_path) }}" alt="Foto Kunjungan" class="w-full h-full object-cover">
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-xs text-slate-400 italic">
                        Belum ada log kunjungan lapangan yang dicatat.
                    </div>
                @endforelse

                <div class="mt-4">{{ $kunjungans->links() }}</div>
            </div>
        </div>
    </div>

    <script>
        function captureVisitGps() {
            const status = document.getElementById('kunjungan-gps-status');
            if (!navigator.geolocation) {
                status.textContent = 'Browser tidak mendukung GPS.';
                return;
            }
            status.textContent = 'Mendeteksi lokasi...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('kunjungan-lat').value = pos.coords.latitude.toFixed(6);
                    document.getElementById('kunjungan-lng').value = pos.coords.longitude.toFixed(6);
                    status.textContent = 'Tersimpan (' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4) + ')';
                    status.className = 'text-[11px] font-bold text-emerald-600 dark:text-emerald-400';
                },
                () => {
                    status.textContent = 'Gagal mengambil GPS. Pastikan izin lokasi aktif.';
                    status.className = 'text-[11px] text-red-500';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
</x-app-layout>
