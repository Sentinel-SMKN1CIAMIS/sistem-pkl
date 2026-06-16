<x-app-layout>
    @php
        $getUniqueBadgeClass = function($name) {
            if (!$name) return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
            $palettes = [
                'bg-blue-500/10 text-blue-500 dark:text-blue-400 border-blue-500/20',
                'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border-emerald-500/20',
                'bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border-indigo-500/20',
                'bg-purple-500/10 text-purple-500 dark:text-purple-400 border-purple-500/20',
                'bg-rose-500/10 text-rose-500 dark:text-rose-400 border-rose-500/20',
                'bg-amber-500/10 text-amber-500 dark:text-amber-400 border-amber-500/20',
                'bg-sky-500/10 text-sky-500 dark:text-sky-400 border-sky-500/20',
                'bg-teal-500/10 text-teal-500 dark:text-teal-400 border-teal-500/20',
            ];
            $hash = crc32($name);
            return $palettes[abs($hash) % count($palettes)];
        };
    @endphp
    <x-slot name="header">Edit Pembimbing Sekolah</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.pembimbing_sekolah.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('pokja.pembimbing_sekolah.update', $pembimbing_sekolah) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Akun (Read Only / Limit Edit) -->
                <div class="glass-card p-6 md:col-span-1 border border-blue-500/10">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Akses Login
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Username</label>
                            <input type="text" value="{{ $pembimbing_sekolah->user->username }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 dark:text-slate-400 font-mono italic">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Email</label>
                            <input type="text" value="{{ $pembimbing_sekolah->user->email }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 dark:text-slate-400 italic">
                        </div>
                    </div>
                </div>

                <!-- Informasi Profil -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
                        Profil Pengajar
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pembimbing_sekolah->nama_lengkap) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200">
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NIP</label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $pembimbing_sekolah->nip) }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 font-mono">
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pembimbing_sekolah->no_hp) }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Penugasan -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5 text-purple-400"></i>
                        Penugasan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konsentrasi Keahlian</label>
                            <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                @foreach($concentrations as $item)
                                    <option value="{{ $item->id }}" {{ (old('konsentrasi_keahlian_id', $pembimbing_sekolah->konsentrasi_keahlian_id) == $item->id) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tipe" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Pembimbing</label>
                            <select name="tipe" id="tipe" required onchange="toggleAdaptifFields()"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="kejuruan" {{ old('tipe', $pembimbing_sekolah->tipe) == 'kejuruan' ? 'selected' : '' }}>Guru Kejuruan (Produktif)</option>
                                <option value="umum" {{ old('tipe', $pembimbing_sekolah->tipe) == 'umum' ? 'selected' : '' }}>Guru Umum (Normatif / Adaptif)</option>
                                <option value="keduanya" {{ old('tipe', $pembimbing_sekolah->tipe) == 'keduanya' ? 'selected' : '' }}>Guru Kejuruan & Umum (Produktif + Adaptif)</option>
                            </select>
                        </div>

                        <!-- Adaptif/Normatif specific fields -->
                        <div id="adaptif-fields" class="md:col-span-2 hidden space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div>
                                <label for="mapel_cp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mata Pelajaran (Mapel)</label>
                                <input type="text" name="mapel_cp" id="mapel_cp" value="{{ old('mapel_cp', $pembimbing_sekolah->mapel_cp) }}"
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                       placeholder="Contoh: Matematika, Bahasa Inggris, dll">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kelas yang Diajar</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200/50 dark:border-slate-700/50 max-h-48 overflow-y-auto">
                                    @php $selectedKelas = old('kelas', $currentClasses ?? []); @endphp
                                    @foreach($existingClasses as $kelas)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="kelas[]" value="{{ $kelas }}" class="rounded text-blue-600 focus:ring-blue-500" {{ in_array($kelas, $selectedKelas) ? 'checked' : '' }}>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $kelas }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Siswa yang Dibimbing -->
                <div class="glass-card p-6 md:col-span-2">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <i data-lucide="users" class="w-5 h-5 text-indigo-400"></i>
                            Pilih Siswa yang Dibimbing
                        </h3>
                        <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-500/20 font-semibold w-fit">
                            <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                            Terpilih: <span id="selected-count">0</span> siswa
                        </span>
                    </div>
                    
                    <!-- Filter Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="filter-search" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Cari Nama / NIS</label>
                            <div class="relative">
                                <input type="text" id="filter-search" placeholder="Cari nama atau NIS..." 
                                       class="w-full pl-9 pr-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 text-sm">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                            </div>
                        </div>
                        <div>
                            <label for="filter-jurusan" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Filter Jurusan</label>
                            <select id="filter-jurusan" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 text-sm">
                                <option value="">Semua Jurusan</option>
                                @foreach($concentrations as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="filter-kelas" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Filter Kelas</label>
                            <select id="filter-kelas" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 text-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($existingClasses as $kelas)
                                    <option value="{{ $kelas }}">{{ $kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Checkbox Actions -->
                    <div class="flex gap-2 mb-3">
                        <button type="button" onclick="toggleAllSiswa(true)" class="text-xs px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium transition-all">
                            Pilih Semua Terfilter
                        </button>
                        <button type="button" onclick="toggleAllSiswa(false)" class="text-xs px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium transition-all">
                            Hapus Semua Terfilter
                        </button>
                    </div>

                    <!-- Student List Scrollable Container -->
                    <div class="border border-slate-200/50 dark:border-slate-800/50 rounded-xl overflow-hidden">
                        <div class="max-h-80 overflow-y-auto">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-900 sticky top-0 z-10">
                                    <tr>
                                        <th class="p-3 w-12 text-center">Pilih</th>
                                        <th class="p-3">Siswa</th>
                                        <th class="p-3">Kelas & Jurusan</th>
                                        <th class="p-3">Pembimbing Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody id="siswa-list-body" class="divide-y divide-slate-100 dark:divide-slate-900 bg-white dark:bg-slate-950/30">
                                    @foreach($students as $siswa)
                                        @php
                                            $isCurrentAdvisor = $siswa->pembimbing_sekolah_id === $pembimbing_sekolah->id;
                                            $hasOtherAdvisor = $siswa->pembimbing_sekolah_id && !$isCurrentAdvisor;
                                        @endphp
                                        <tr class="siswa-row hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors"
                                            data-nama="{{ strtolower($siswa->nama_lengkap) }}"
                                            data-nis="{{ $siswa->nis }}"
                                            data-jurusan="{{ $siswa->konsentrasi_keahlian_id }}"
                                            data-kelas="{{ $siswa->kelas }}">
                                            <td class="p-3 text-center">
                                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" 
                                                       class="siswa-checkbox rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer"
                                                       {{ $isCurrentAdvisor ? 'checked' : '' }}>
                                            </td>
                                            <td class="p-3">
                                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $siswa->nama_lengkap }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">NIS: {{ $siswa->nis }}</div>
                                            </td>
                                            <td class="p-3">
                                                <div class="text-slate-700 dark:text-slate-300 font-medium">{{ $siswa->kelas }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $siswa->konsentrasiKeahlian->nama }}</div>
                                            </td>
                                            <td class="p-3">
                                                @if($isCurrentAdvisor)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        Dibimbing guru ini
                                                    </span>
                                                @elseif($hasOtherAdvisor)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $getUniqueBadgeClass($siswa->pembimbingSekolah->nama_lengkap) }}">
                                                        {{ $siswa->pembimbingSekolah->nama_lengkap }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400">
                                                        Belum diplot
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($students->isEmpty())
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-500 dark:text-slate-400 italic">
                                                Belum ada data siswa terdaftar.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Update Data Pembimbing
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleAdaptifFields() {
            const tipe = document.getElementById('tipe').value;
            const adaptifFields = document.getElementById('adaptif-fields');
            if(tipe === 'umum' || tipe === 'keduanya') {
                adaptifFields.classList.remove('hidden');
            } else {
                adaptifFields.classList.add('hidden');
            }
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.siswa-checkbox:checked').length;
            const countEl = document.getElementById('selected-count');
            if (countEl) {
                countEl.textContent = count;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleAdaptifFields();
            updateSelectedCount();

            const searchInput = document.getElementById('filter-search');
            const jurusanSelect = document.getElementById('filter-jurusan');
            const kelasSelect = document.getElementById('filter-kelas');
            const rows = document.querySelectorAll('.siswa-row');

            function filterSiswa() {
                const query = searchInput.value.toLowerCase();
                const jurusan = jurusanSelect.value;
                const kelas = kelasSelect.value;

                rows.forEach(row => {
                    const name = row.getAttribute('data-nama');
                    const nis = row.getAttribute('data-nis');
                    const rowJurusan = row.getAttribute('data-jurusan');
                    const rowKelas = row.getAttribute('data-kelas');

                    const matchesSearch = name.includes(query) || nis.includes(query);
                    const matchesJurusan = !jurusan || rowJurusan === jurusan;
                    const matchesKelas = !kelas || rowKelas === kelas;

                    if (matchesSearch && matchesJurusan && matchesKelas) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterSiswa);
            if (jurusanSelect) jurusanSelect.addEventListener('change', filterSiswa);
            if (kelasSelect) kelasSelect.addEventListener('change', filterSiswa);

            // Listen to checkbox changes
            document.querySelectorAll('.siswa-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });
        });

        function toggleAllSiswa(checked) {
            const visibleRows = document.querySelectorAll('.siswa-row:not(.hidden)');
            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.siswa-checkbox');
                if (checkbox) {
                    checkbox.checked = checked;
                }
            });
            updateSelectedCount();
        }
    </script>
</x-app-layout>
