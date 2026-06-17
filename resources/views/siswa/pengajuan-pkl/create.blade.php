<x-app-layout>
    <x-slot name="header">Pengajuan Tempat PKL</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-7 h-7 text-blue-500"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Daftarkan Tempat PKL Kamu</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Isi data perusahaan tempat kamu akan melaksanakan PKL. Pengajuan akan ditinjau oleh Guru Pembimbing.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('siswa.pengajuan_pkl.store') }}" method="POST" class="space-y-5" id="form-pengajuan">
                @csrf
                
                <div>
                    <label for="dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tempat PKL (DUDI) <span class="text-red-500">*</span>
                    </label>
                    <select name="dudi_id" id="dudi_id" onchange="handleDudiChange()"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        <option value="" selected>-- Pilih Tempat PKL (Atau Input Baru) --</option>
                        @foreach($dudis as $dudi)
                            <option value="{{ $dudi->id }}"
                                    data-nama="{{ $dudi->nama }}"
                                    data-pimpinan="{{ $dudi->nama_pimpinan }}"
                                    data-alamat="{{ $dudi->alamat }}"
                                    data-kota="{{ $dudi->kota }}"
                                    data-telp="{{ $dudi->no_telepon }}"
                                    {{ old('dudi_id') == $dudi->id ? 'selected' : '' }}>
                                {{ $dudi->nama }} ({{ $dudi->kota }})
                            </option>
                        @endforeach
                        <option value="baru" {{ old('dudi_id') === 'baru' ? 'selected' : '' }}>[+] Lainnya / Input Tempat PKL Baru</option>
                    </select>
                    @error('dudi_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div id="pembimbing_dudi_container" class="hidden">
                    <label for="pembimbing_dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Pembimbing DUDI
                    </label>
                    <select name="pembimbing_dudi_id" id="pembimbing_dudi_id"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        <option value="" selected>-- Pilih Pembimbing (Opsional) --</option>
                    </select>
                    @error('pembimbing_dudi_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div id="manual_fields" class="space-y-5 {{ old('dudi_id') ? (old('dudi_id') === 'baru' ? '' : '') : '' }}">
                    <div>
                        <label for="nama_perusahaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Nama Perusahaan / Instansi <span class="text-red-500">*</span>
                        </label>
                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Contoh: PT. Maju Bersama">
                    @error('nama_perusahaan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="pimpinan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Pimpinan / HRD</label>
                    <input type="text" name="pimpinan" id="pimpinan" value="{{ old('pimpinan') }}"
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Opsional">
                </div>

                <div>
                    <label for="kota" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Kota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kota" id="kota" value="{{ old('kota') }}"
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Contoh: Bandung">
                    @error('kota')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Alamat Lengkap Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all resize-none"
                              placeholder="Jl. ...">{{ old('alamat') }}</textarea>
                    @error('alamat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="no_telp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. Telepon Perusahaan</label>
                    <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}"
                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                           placeholder="Opsional">
                </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            handleDudiChange();
            
            // Fix form submission when fields are disabled (disabled fields are not sent via POST)
            document.getElementById('form-pengajuan').addEventListener('submit', function() {
                const inputs = ['nama_perusahaan', 'pimpinan', 'kota', 'alamat', 'no_telp'];
                inputs.forEach(id => {
                    document.getElementById(id).disabled = false;
                });
                
                // If "baru" is selected, unset dudi_id value before submit so it becomes null
                const dudiSelect = document.getElementById('dudi_id');
                if (dudiSelect.value === 'baru') {
                    dudiSelect.value = '';
                }
            });
        });

        function handleDudiChange() {
            const select = document.getElementById('dudi_id');
            const selected = select.options[select.selectedIndex];
            const pembimbingContainer = document.getElementById('pembimbing_dudi_container');
            const pembimbingSelect = document.getElementById('pembimbing_dudi_id');
            
            const fields = {
                'nama_perusahaan': document.getElementById('nama_perusahaan'),
                'pimpinan': document.getElementById('pimpinan'),
                'kota': document.getElementById('kota'),
                'alamat': document.getElementById('alamat'),
                'no_telp': document.getElementById('no_telp'),
            };

            if (selected.value && selected.value !== 'baru') {
                // Fill and disable fields
                fields['nama_perusahaan'].value = selected.dataset.nama || '';
                fields['pimpinan'].value = selected.dataset.pimpinan || '';
                fields['kota'].value = selected.dataset.kota || '';
                fields['alamat'].value = selected.dataset.alamat || '';
                fields['no_telp'].value = selected.dataset.telp || '';
                
                for (let key in fields) {
                    fields[key].disabled = true;
                    fields[key].classList.add('opacity-70');
                }

                // Fetch Pembimbing DUDI
                pembimbingContainer.classList.remove('hidden');
                pembimbingSelect.innerHTML = '<option value="">Sedang memuat...</option>';
                
                fetch(`{{ route('siswa.pengajuan_pkl.pembimbing') }}?dudi_id=${selected.value}`)
                    .then(response => response.json())
                    .then(data => {
                        pembimbingSelect.innerHTML = '<option value="">-- Pilih Pembimbing (Opsional) --</option>';
                        if(data.length > 0) {
                            data.forEach(item => {
                                pembimbingSelect.innerHTML += `<option value="${item.id}">${item.nama_lengkap}</option>`;
                            });
                        } else {
                            pembimbingSelect.innerHTML = '<option value="">Belum ada pembimbing di industri ini</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching pembimbing:', error);
                        pembimbingSelect.innerHTML = '<option value="">Gagal memuat pembimbing</option>';
                    });

            } else {
                pembimbingContainer.classList.add('hidden');
                pembimbingSelect.innerHTML = '<option value="">-- Pilih Pembimbing (Opsional) --</option>';

                // Clear (if switching to 'baru') and enable fields
                if (selected.value === 'baru') {
                    for (let key in fields) {
                        if (!fields[key].disabled) continue; // Don't clear if they were already typing
                        fields[key].value = '';
                    }
                }
                
                for (let key in fields) {
                    fields[key].disabled = false;
                    fields[key].classList.remove('opacity-70');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
