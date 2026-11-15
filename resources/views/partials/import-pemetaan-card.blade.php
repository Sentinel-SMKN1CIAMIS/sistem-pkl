@php
    $endpoint = $postUrl ?? route('admin.config.import-pemetaan');
@endphp

<!-- Pemetaan Pembimbing & DUDI Import Card -->
<div class="glass-card p-6 shadow-xl relative overflow-hidden" x-data="pemetaanImporter('{{ $endpoint }}')">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-500">
                <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Impor Pemetaan Pembimbing & DUDI PKL</h4>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Smart Filter & Disambiguasi</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-normal mt-0.5">
                    Otomatis memetakan siswa ke tempat PKL, membuat/menimpa akun pembimbing sekolah (username & password otomatis NIP), mendaftarkan master DUDI, serta mengecualikan siswa yang sudah selesai (DKV).
                </p>
            </div>
        </div>
    </div>

    <!-- Smart Filter Jurusan -->
    <div class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/50 dark:border-slate-800/50 space-y-3 mb-6">
        <div class="flex items-center justify-between">
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-blue-500"></i>
                Pilih Program Keahlian / Jurusan yang Diimpor (Smart Filter):
            </label>
            <span class="text-[10px] text-slate-400 italic">* DKV dinonaktifkan secara default karena sudah selesai PKL</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="AKL" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">AKL (Akuntansi)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="PBS" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">PBS (Perbankan Syariah)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="MPLB" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">MPLB (Perkantoran)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="PM" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">PM (Bisnis Ritel)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="PBD" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">PBD (Bisnis Digital)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="KLN" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">Kuliner (Tata Boga)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="HTL" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">Perhotelan</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                <input type="checkbox" value="PPLG" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-700 dark:text-slate-300">PPLG (RPL)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 hover:border-amber-500 transition-all col-span-2 sm:col-span-4">
                <input type="checkbox" value="DKV" x-model="selectedJurusan" class="rounded text-amber-600 focus:ring-amber-500">
                <span class="font-medium">DKV (Beres PKL - dinonaktifkan secara default)</span>
            </label>
        </div>
    </div>

    <!-- Options & Source (Password guru otomatis NIP) -->
    <div class="mb-6 text-xs">
        <div class="space-y-1.5">
            <label class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i data-lucide="file-up" class="w-3.5 h-3.5 text-amber-500"></i>
                Pilih Berkas Pemetaan (Opsional)
            </label>
            <input type="file" @change="fileToUpload = $event.target.files[0]" accept=".csv,.xlsx,.txt" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500/10 file:text-amber-600 dark:file:text-amber-400 hover:file:bg-amber-500/20 cursor-pointer">
            <p class="text-[10px] text-slate-400">
                Biarkan kosong untuk otomatis memakai master berkas pemetaan 2026/2027 yang tersimpan di server. Password awal guru otomatis menggunakan <strong>NIP resmi</strong> masing-masing guru.
            </p>
        </div>
    </div>

    <!-- Action Trigger -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200/50 dark:border-slate-800/50">
        <div class="text-[11px] text-slate-500">
            <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedJurusan.length"></span> jurusan dipilih untuk dipetakan.
        </div>
        <button type="button" @click="startImportProcess()" :disabled="selectedJurusan.length === 0" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
            <i data-lucide="play" class="w-4 h-4"></i>
            Mulai Impor & Petakan Data Siswa
        </button>
    </div>

    <!-- Progress & Summary Modal -->
    <template x-teleport="body">
        <div x-show="modalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
             x-transition.opacity.duration.300ms x-cloak>
             
             <div @click.away="closeModal()" 
                  class="glass-card w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left flex flex-col">
                  
                  <!-- Modal Header -->
                  <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-amber-500/5 dark:bg-amber-500/10 shrink-0">
                      <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                          <i data-lucide="file-spreadsheet" class="text-amber-500 w-5 h-5"></i>
                          Proses Impor Pemetaan PKL
                      </h3>
                      <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                          <i data-lucide="x" class="w-4 h-4"></i>
                      </button>
                  </div>

                  <!-- Modal Body -->
                  <div class="p-6 space-y-4 text-xs text-slate-600 dark:text-slate-400">
                      <!-- Running State -->
                      <div x-show="isRunning" class="space-y-4 text-center py-2">
                          <div class="inline-flex p-3 rounded-2xl bg-amber-500/10 text-amber-500 animate-pulse">
                              <i data-lucide="refresh-cw" class="w-7 h-7 animate-spin"></i>
                          </div>
                          <div>
                              <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Memproses Data Pemetaan...</h4>
                              <p class="text-[11px] text-slate-500">Memetakan siswa, guru pembimbing, dan DUDI secara bertahap.</p>
                          </div>

                          <!-- Progress Bar -->
                          <div class="space-y-1.5 text-left p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50">
                              <div class="flex justify-between items-center text-xs font-semibold">
                                  <span><strong class="text-amber-600 dark:text-amber-400" x-text="processed"></strong> dari <span x-text="total"></span> Data</span>
                                  <span class="font-bold text-amber-600 dark:text-amber-400" x-text="percent + '%'">0%</span>
                              </div>
                              <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden shadow-inner">
                                  <div class="bg-gradient-to-r from-amber-500 to-emerald-500 h-3 rounded-full transition-all duration-300" :style="'width: ' + percent + '%'"></div>
                              </div>
                          </div>
                      </div>

                      <!-- Finished State -->
                      <div x-show="isFinished" class="space-y-4 py-2" style="display: none;">
                          <div class="text-center space-y-2">
                              <div class="inline-flex p-3 rounded-2xl bg-emerald-500/10 text-emerald-500">
                                  <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                              </div>
                              <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400" x-text="formatType === 'guru_nip' ? 'Sinkronisasi Data Guru & NIP Berhasil!' : 'Impor Pemetaan Berhasil Selesai!'"></h4>
                          </div>

                          <template x-if="formatType === 'guru_nip'">
                              <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50 text-xs">
                                  <div class="grid grid-cols-2 gap-3">
                                      <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                          <span class="text-emerald-600 dark:text-emerald-400 font-bold block">Akun Baru Dibuat:</span>
                                          <span class="font-extrabold text-base text-slate-800 dark:text-slate-100" x-text="summary.pembimbing_created + ' Guru'"></span>
                                      </div>
                                      <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/20">
                                          <span class="text-blue-600 dark:text-blue-400 font-bold block">Kredensial Disinkronkan:</span>
                                          <span class="font-extrabold text-base text-slate-800 dark:text-slate-100" x-text="summary.pembimbing_matched + ' Guru'"></span>
                                      </div>
                                  </div>
                                  <div class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-slate-700 dark:text-slate-300 space-y-1">
                                      <p class="font-semibold text-amber-700 dark:text-amber-400">Informasi Kredensial Guru:</p>
                                      <p class="text-[11px] leading-relaxed">Username login dan password awal (default) guru otomatis disetel menggunakan <b>NIP</b> masing-masing guru. Data bimbingan DUDI dan siswa tidak terganggu.</p>
                                  </div>
                              </div>
                          </template>

                          <template x-if="formatType !== 'guru_nip'">
                              <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50 text-[11px]">
                                  <div class="p-2.5 rounded-lg bg-blue-500/10">
                                      <span class="text-blue-500 font-bold block">Pembimbing Sekolah:</span>
                                      <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.pembimbing_created + ' Baru / ' + summary.pembimbing_matched + ' Cocok'"></span>
                                  </div>
                                  <div class="p-2.5 rounded-lg bg-indigo-500/10">
                                      <span class="text-indigo-500 font-bold block">Tempat DUDI:</span>
                                      <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.dudi_created + ' Baru / ' + summary.dudi_matched + ' Cocok'"></span>
                                  </div>
                                  <div class="p-2.5 rounded-lg bg-emerald-500/10 col-span-2">
                                      <span class="text-emerald-500 font-bold block">Siswa Berhasil Dipetakan (PKL):</span>
                                      <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.siswa_mapped + ' Siswa'"></span>
                                  </div>
                              </div>
                          </template>
                      </div>

                      <!-- Error State -->
                      <div x-show="errorMessage" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 space-y-1" style="display: none;">
                          <p class="font-bold">Terjadi Kesalahan</p>
                          <p x-text="errorMessage"></p>
                      </div>
                  </div>

                  <!-- Modal Footer -->
                  <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-2 bg-slate-50/50 dark:bg-slate-800/30">
                      <template x-if="isRunning">
                          <button type="button" @click="cancelImport()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-md transition-all">
                              Hentikan / Batalkan
                          </button>
                      </template>
                      <template x-if="isFinished">
                          <button type="button" @click="finishAndReload()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all">
                              Selesai & Segarkan Halaman
                          </button>
                      </template>
                      <template x-if="!isRunning && !isFinished">
                          <button type="button" @click="closeModal()" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl">
                              Tutup
                          </button>
                      </template>
                  </div>
             </div>
        </div>
    </template>
</div>

@once
<script>
    function pemetaanImporter(endpointUrl) {
        return {
            endpointUrl: endpointUrl,
            selectedJurusan: ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG'],
            fileToUpload: null,
            modalOpen: false,
            isRunning: false,
            isCancelled: false,
            isFinished: false,
            errorMessage: '',
            processed: 0,
            total: 0,
            percent: 0,
            offset: 0,
            batchSize: 50,
            formatType: 'default',
            summary: {
                pembimbing_created: 0,
                pembimbing_matched: 0,
                dudi_created: 0,
                dudi_matched: 0,
                siswa_mapped: 0
            },

            closeModal() {
                if (this.isRunning) {
                    if (!confirm('Impor sedang berjalan. Yakin ingin membatalkan?')) return;
                    this.cancelImport();
                }
                this.modalOpen = false;
            },

            cancelImport() {
                this.isCancelled = true;
                this.isRunning = false;
                this.$nextTick(() => window.lucide?.createIcons());
            },

            async startImportProcess() {
                this.modalOpen = true;
                this.isRunning = true;
                this.isCancelled = false;
                this.isFinished = false;
                this.errorMessage = '';
                this.processed = 0;
                this.total = 0;
                this.percent = 0;
                this.offset = 0;
                this.formatType = 'default';
                this.summary = {
                    pembimbing_created: 0,
                    pembimbing_matched: 0,
                    dudi_created: 0,
                    dudi_matched: 0,
                    siswa_mapped: 0
                };
                this.$nextTick(() => window.lucide?.createIcons());

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                let cacheId = null;

                try {
                    while (this.isRunning && !this.isCancelled) {
                        const formData = new FormData();
                        if (cacheId) {
                            formData.append('cache_id', cacheId);
                        } else if (this.fileToUpload) {
                            formData.append('file', this.fileToUpload);
                        }
                        formData.append('offset', this.offset);
                        formData.append('batch_size', this.batchSize);
                        this.selectedJurusan.forEach(j => formData.append('jurusan_filter[]', j));

                        const res = await fetch(this.endpointUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!res.ok) {
                            const err = await res.json().catch(() => ({}));
                            throw new Error(err.message || ('Server error HTTP ' + res.status));
                        }

                        const data = await res.json();
                        if (data.status !== 'success') {
                            throw new Error(data.message || 'Gagal memproses batch impor pemetaan.');
                        }

                        if (data.cache_id) {
                            cacheId = data.cache_id;
                        }

                        this.total = data.total;
                        this.processed = data.processed_total;
                        this.percent = data.percent;
                        this.offset = data.offset;
                        if (data.format) {
                            this.formatType = data.format;
                        }

                        if (data.stats) {
                            this.summary.pembimbing_created += (data.stats.pembimbing_created || 0);
                            this.summary.pembimbing_matched += (data.stats.pembimbing_matched || 0);
                            this.summary.dudi_created += (data.stats.dudi_created || 0);
                            this.summary.dudi_matched += (data.stats.dudi_matched || 0);
                            this.summary.siswa_mapped += (data.stats.siswa_mapped || 0);
                        }

                        if (data.done || this.offset >= data.total) {
                            this.isFinished = true;
                            this.isRunning = false;
                            this.percent = 100;
                            this.$nextTick(() => window.lucide?.createIcons());
                            break;
                        }

                        await new Promise(r => setTimeout(r, 60));
                    }
                } catch (err) {
                    this.errorMessage = err.message || 'Terjadi kesalahan saat mengimpor pemetaan.';
                    this.isRunning = false;
                } finally {
                    this.$nextTick(() => window.lucide?.createIcons());
                }
            },

            finishAndReload() {
                window.location.reload();
            }
        };
    }
</script>
@endonce

