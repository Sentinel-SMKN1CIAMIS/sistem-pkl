<x-app-layout>
    <x-slot name="header">Profil Saya</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-8 text-center">
                <div class="relative inline-flex mb-6">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white dark:border-slate-800 shadow-xl bg-slate-100">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=3b82f6&color=fff&size=200" 
                             alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center shadow-lg transform translate-x-1/4 translate-y-1/4">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $siswa->nama_lengkap }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ $siswa->nis }}</p>
                
                <div class="mt-6 pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex flex-col gap-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kelas</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $siswa->kelas }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Program</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-right">{{ $siswa->konsentrasiKeahlian->nama }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6">
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
                    Informasi PKL
                </h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">DUDI / Industri</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->dudi->nama ?? 'Belum Penempatan' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Pembimbing Kejuruan</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->pembimbingSekolah->nama_lengkap ?? 'Belum Ditugaskan' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Pembimbing Umum</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->pembimbingSekolahUmum->nama_lengkap ?? 'Belum Ditugaskan' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Pembimbing Lapangan (DUDI)</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                            {{ $siswa->pembimbingDudi->nama_lengkap ?? $siswa->pembimbing_dudi_nama ?? 'Belum Ditugaskan' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($siswa->dudi)
            <div class="glass-card p-6">
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-red-400"></i>
                    Lokasi DUDI
                </h4>
                <div id="lokasi-dudi-status" class="mb-4">
                    @if($siswa->dudi->latitude && $siswa->dudi->longitude)
                        <div class="flex items-center gap-2 text-xs text-emerald-500 font-medium mb-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Koordinat Tersimpan
                        </div>
                        <p class="text-[11px] text-slate-500 font-mono">{{ number_format($siswa->dudi->latitude, 6) }}, {{ number_format($siswa->dudi->longitude, 6) }}</p>
                        @if($siswa->dudi->zona)
                            <p class="mt-1 text-xs text-blue-400 font-medium">Zona: {{ $siswa->dudi->zona->nama }}</p>
                        @endif
                    @else
                        <p class="text-xs text-amber-400 font-medium flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            Belum ada koordinat
                        </p>
                    @endif
                </div>
                <button type="button" id="btn-update-lokasi" 
                        class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <i data-lucide="navigation" class="w-4 h-4"></i>
                    <span id="btn-lokasi-text">Update Lokasi Saat Ini</span>
                </button>
                <p class="mt-2 text-[10px] text-slate-500 italic text-center">Pastikan Anda sedang berada di lokasi DUDI saat memencet tombol ini.</p>
            </div>
            @endif
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="glass-card">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-blue-400"></i>
                        Pengaturan Profil
                    </h3>
                </div>
                
                <form action="{{ route('siswa.profile.update') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    @method('PATCH')

                    <!-- Akun & Kontak Siswa -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-blue-400"></i>
                            Akun & Kontak Siswa
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Email Terdaftar</label>
                                <div class="relative">
                                    <input type="email" id="email" value="{{ $siswa->user->email }}" readonly
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100/50 dark:bg-slate-900/30 border border-slate-200/50 dark:border-slate-700/30 rounded-xl text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1.5 text-[10px] text-slate-500 italic">Email tidak dapat diubah oleh siswa.</p>
                            </div>
                            <div>
                                <label for="no_hp" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Nomor WhatsApp / HP Siswa</label>
                                <div class="relative">
                                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}"
                                           placeholder="Contoh: 081234567890"
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1.5 text-[10px] text-slate-500 italic">Nomor WhatsApp aktif untuk menerima notifikasi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pembimbing Industri (Manual) -->
                    <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-purple-400"></i>
                            Pembimbing Industri (Manual)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pembimbing_dudi_nama" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Pembimbing Industri</label>
                                <input type="text" name="pembimbing_dudi_nama" id="pembimbing_dudi_nama" 
                                       value="{{ old('pembimbing_dudi_nama', $siswa->pembimbing_dudi_nama) }}"
                                       placeholder="Nama Pembimbing di Industri"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            <div>
                                <label for="pembimbing_dudi_jabatan" class="block text-xs font-bold text-slate-500 uppercase mb-2">Jabatan Pembimbing</label>
                                <input type="text" name="pembimbing_dudi_jabatan" id="pembimbing_dudi_jabatan" 
                                       value="{{ old('pembimbing_dudi_jabatan', $siswa->pembimbing_dudi_jabatan) }}"
                                       placeholder="Contoh: HRD / Mentor / Supervisor"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                             </div>
                             <div class="md:col-span-2">
                                 <label for="unit_pekerjaan" class="block text-xs font-bold text-slate-500 uppercase mb-2">Unit / Bagian Pekerjaan</label>
                                 <input type="text" name="unit_pekerjaan" id="unit_pekerjaan" 
                                        value="{{ old('unit_pekerjaan', $siswa->unit_pekerjaan) }}"
                                        placeholder="Contoh: Divisi IT / Front Office / Bengkel Mesin"
                                        class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                             </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-500 italic">Isi kolom di atas jika pembimbing industri belum terdaftar di sistem, serta untuk melengkapi unit kerja Anda.</p>
                    </div>

                    <!-- Alamat Tempat PKL / DUDI -->
                    <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-400"></i>
                            Alamat Tempat PKL / DUDI
                        </h4>
                        @php
                            $dudiAlamat = '';
                            $alamatHelper = '';
                            if ($siswa->dudi_id && $siswa->dudi) {
                                $dudiAlamat = $siswa->dudi->alamat;
                                $alamatHelper = 'Menampilkan alamat DUDI aktif: <strong>' . e($siswa->dudi->nama) . '</strong>. Perubahan di bawah akan memperbarui alamat DUDI tersebut.';
                            } elseif ($siswa->pengajuanPkl) {
                                if ($siswa->pengajuanPkl->dudi_id && $siswa->pengajuanPkl->dudi) {
                                    $dudiAlamat = $siswa->pengajuanPkl->dudi->alamat;
                                    $alamatHelper = 'Menampilkan alamat DUDI pengajuan: <strong>' . e($siswa->pengajuanPkl->dudi->nama) . '</strong>. Perubahan di bawah akan memperbarui alamat DUDI tersebut.';
                                } else {
                                    $dudiAlamat = $siswa->pengajuanPkl->alamat;
                                    $alamatHelper = 'Menampilkan alamat DUDI pengajuan manual: <strong>' . e($siswa->pengajuanPkl->nama_perusahaan) . '</strong>. Perubahan di bawah akan memperbarui alamat pengajuan Anda.';
                                }
                            } else {
                                $dudiAlamat = $siswa->alamat;
                                $alamatHelper = 'Belum ada DUDI terdaftar atau diajukan. Menyimpan perubahan di bawah akan disimpan sebagai alamat sementara.';
                            }
                        @endphp
                        <div>
                            <label for="alamat" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Alamat Lengkap Tempat PKL / DUDI</label>
                            <textarea name="alamat" id="alamat" rows="3"
                                      placeholder="Tulis alamat lengkap tempat PKL / DUDI..."
                                      class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">{{ old('alamat', $dudiAlamat) }}</textarea>
                            <p class="mt-1.5 text-[11px] text-slate-500/80 dark:text-slate-400/80 italic">{!! $alamatHelper !!}</p>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($siswa->dudi)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btn-update-lokasi');
        const btnText = document.getElementById('btn-lokasi-text');
        if (!btn) return;

        btn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            btn.disabled = true;
            btnText.textContent = 'Mendeteksi lokasi...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    btnText.textContent = 'Menyimpan...';

                    fetch('{{ route("siswa.profile.update-lokasi-dudi") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lng })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            alert(data.message + (data.zona ? ' (Zona: ' + data.zona + ')' : ''));
                            window.location.reload();
                        }
                    })
                    .catch(e => alert('Gagal menyimpan: ' + e.message))
                    .finally(() => {
                        btn.disabled = false;
                        btnText.textContent = 'Update Lokasi Saat Ini';
                    });
                },
                function(error) {
                    let msg = 'Gagal mendapatkan lokasi.';
                    if (error.code === 1) msg = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi.';
                    else if (error.code === 2) msg = 'Lokasi tidak tersedia.';
                    else if (error.code === 3) msg = 'Waktu permintaan habis.';
                    alert(msg);
                    btn.disabled = false;
                    btnText.textContent = 'Update Lokasi Saat Ini';
                },
                { enableHighAccuracy: true, timeout: 30000, maximumAge: 30000 }
            );
        });
    });
    </script>
    @endpush
    @endif
</x-app-layout>
