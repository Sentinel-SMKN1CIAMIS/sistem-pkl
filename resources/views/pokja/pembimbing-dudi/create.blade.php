<x-app-layout>
    <x-slot name="header">Tambah Pembimbing DUDI</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.pembimbing_dudi.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <form action="{{ route('pokja.pembimbing_dudi.store') }}" method="POST" class="space-y-6"
              x-data="{
                  selectedSiswaId: '',
                  siswaId: {{ json_encode(old('siswa_id', '')) }},
                  namaLengkap: {{ json_encode(old('nama_lengkap', '')) }},
                  jabatan: {{ json_encode(old('jabatan', '')) }},
                  noHp: {{ json_encode(old('no_hp', '')) }},
                  dudiId: {{ json_encode(old('dudi_id', '')) }},
                  username: {{ json_encode(old('username', '')) }},
                  email: {{ json_encode(old('email', '')) }},
                  password: {{ json_encode(old('password', 'pklsmkn1ciamis')) }},
                  
                  init() {
                      this.$watch('selectedSiswaId', value => {
                          if (!value) {
                              this.siswaId = '';
                              this.namaLengkap = '';
                              this.jabatan = '';
                              this.noHp = '';
                              this.dudiId = '';
                              this.username = '';
                              this.email = '';
                              this.password = 'pklsmkn1ciamis';
                              return;
                          }
                          
                          const select = document.getElementById('select-manual-siswa');
                          if (!select) return;
                          const option = select.querySelector(`option[value='${value}']`);
                          if (!option) return;
                          
                          this.siswaId = value;
                          const nama = option.getAttribute('data-nama') || '';
                          this.namaLengkap = nama;
                          this.jabatan = option.getAttribute('data-jabatan') || '';
                          this.noHp = option.getAttribute('data-no-hp') || '';
                          this.dudiId = option.getAttribute('data-dudi-id') || '';
                          const nis = option.getAttribute('data-nis') || '';
                          const namaSiswa = option.getAttribute('data-nama-lengkap') || '';
                          
                          // Auto-generate username and email suggestions based ONLY on mentor name
                          const cleanName = nama.toLowerCase().trim()
                              .replace(/[^a-z0-9\s]/g, '')
                              .replace(/\s+/g, '.');
                          this.username = cleanName ? cleanName : 'mentor';
                          this.email = cleanName ? (cleanName + '@dudi.pkl.id') : 'mentor@dudi.com';
                          this.password = 'pklsmkn1ciamis';
                          
                          // Show alert/toast using showToast helper
                          if (window.showToast) {
                              window.showToast('Data dari ' + namaSiswa + ' berhasil disalin!');
                          }
                      });
                  }
              }">
            @csrf

            <!-- Hidden input to track copied student -->
            <input type="hidden" name="siswa_id" id="siswa_id" x-model="siswaId">

            @if(count($manualMentors) > 0)
            <div class="glass-card p-6 bg-linear-to-r from-blue-50/50 to-indigo-50/50 dark:from-blue-950/20 dark:to-indigo-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl mb-6">
                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-400 mb-2 flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-blue-500"></i>
                    Salin Data Usulan Siswa
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Pilih siswa bimbingan yang telah menuliskan nama pembimbing lapangan secara manual. Memilih siswa di bawah akan mengisi formulir secara otomatis untuk meningkatkan efisiensi.
                </p>
                <div class="max-w-xl">
                    <select id="select-manual-siswa" x-model="selectedSiswaId" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200">
                        <option value="">-- Pilih Siswa Pengusul --</option>
                        @foreach($manualMentors as $student)
                            <option value="{{ $student->id }}" 
                                    data-nama="{{ $student->pembimbing_dudi_nama }}"
                                    data-jabatan="{{ $student->pembimbing_dudi_jabatan }}"
                                    data-no-hp="{{ $student->pembimbing_dudi_no_hp }}"
                                    data-dudi-id="{{ $student->dudi_id }}"
                                    data-nis="{{ $student->nis }}"
                                    data-nama-lengkap="{{ $student->nama_lengkap }}">
                                {{ $student->nama_lengkap }} ({{ $student->nis }}) - {{ $student->pembimbing_dudi_nama }} [{{ $student->dudi->nama ?? 'Belum Diplot' }}]
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Login Info -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Akses Login Mentor
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username</label>
                            <input type="text" name="username" id="username" x-model="username" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                            @error('username') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" x-model="email" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                             <input type="password" name="password" id="password" x-model="password" required
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-purple-400"></i>
                        Profil Mentor / Pembimbing
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" x-model="namaLengkap" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="jabatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jabatan di Perusahaan</label>
                            <input type="text" name="jabatan" id="jabatan" x-model="jabatan" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Contoh: Senior Supervisor">
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" x-model="noHp"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Opsional">
                        </div>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-5 h-5 text-amber-400"></i>
                        Afiliasi Perusahaan (DUDI)
                    </h3>
                    <div>
                        <label for="dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bekerja di Industri</label>
                        <select name="dudi_id" id="dudi_id" x-model="dudiId" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <option value="" disabled selected>Pilih Perusahaan</option>
                            @foreach($dudis as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 italic">Mentor hanya dapat melihat & memvalidasi siswa yang PKL di perusahaan yang sama.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Pembimbing DUDI
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
