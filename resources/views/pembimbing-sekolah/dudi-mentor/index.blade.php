<x-app-layout>
    <x-slot name="header">Kelola Akun Pembimbing DUDI</x-slot>

    <div class="space-y-6" x-data="{ modalOpen: false, selectedDudiId: '', selectedDudiName: '' }">
        <!-- Header Info -->
        <div class="glass-card p-6 shadow-xl relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-5 h-5 text-blue-500"></i>
                        DUDI Tempat Bimbingan Siswa
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Daftar tempat PKL anak bimbingan Anda. Anda dapat membuatkan akun pembimbing lapangan (DUDI) agar pihak industri dapat memvalidasi jurnal & absensi siswa.
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Daftar DUDI Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($dudis as $dudi)
                <div class="glass-card p-6 shadow-xl relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    {{ $dudi->kota ?: 'Lokasi PKL' }}
                                </span>
                                <h4 class="text-base font-bold text-slate-800 dark:text-slate-200 mt-1">
                                    {{ $dudi->nama }}
                                </h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300">
                                {{ $dudi->siswa->count() }} Siswa Bimbingan
                            </span>
                        </div>

                        <!-- Status Pembimbing DUDI -->
                        <div class="my-4 p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50 text-xs space-y-2">
                            <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-500"></i>
                                Akun Pembimbing DUDI:
                            </span>

                            @if($dudi->pembimbingDudi->count() > 0)
                                <div class="space-y-1.5 pl-2">
                                    @foreach($dudi->pembimbingDudi as $mentor)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $mentor->nama_lengkap }}</p>
                                                <p class="text-slate-400">{{ $mentor->jabatan ?: 'Pembimbing Lapangan' }} • {{ $mentor->user->email ?? '-' }}</p>
                                            </div>
                                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-500 font-bold text-[9px]">Aktif</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-slate-400 italic pl-2 text-[11px]">Belum ada akun pembimbing industri untuk DUDI ini.</p>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end">
                        <button type="button" 
                                @click="selectedDudiId = '{{ $dudi->id }}'; selectedDudiName = '{{ addslashes($dudi->nama) }}'; modalOpen = true; $nextTick(() => window.lucide?.createIcons())"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                            + Buat Akun Pembimbing DUDI
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-2 glass-card p-12 text-center text-slate-400">
                    <i data-lucide="building" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                    <p class="text-sm">Belum ada DUDI tempat bimbingan yang terdaftar untuk Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- Modal Tambah Akun Pembimbing DUDI -->
        <template x-teleport="body">
            <div x-show="modalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
                 x-transition.opacity.duration.300ms x-cloak>
                 
                 <div @click.away="modalOpen = false" 
                      class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left">
                      
                      <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-blue-500/5 dark:bg-blue-500/10">
                          <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                              <i data-lucide="user-plus" class="text-blue-500 w-4 h-4"></i>
                              Buat Akun Pembimbing DUDI
                          </h3>
                          <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                              <i data-lucide="x" class="w-4 h-4"></i>
                          </button>
                      </div>

                      <form action="{{ route('pembimbing_sekolah.dudi_mentor.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                          @csrf
                          <input type="hidden" name="dudi_id" :value="selectedDudiId">

                          <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-300">
                              <span class="font-bold">Untuk Tempat PKL:</span>
                              <p class="font-bold text-sm" x-text="selectedDudiName"></p>
                          </div>

                          <div class="space-y-1">
                              <label class="font-bold text-slate-700 dark:text-slate-300">Nama Lengkap Pembimbing Industri</label>
                              <input type="text" name="nama_lengkap" required placeholder="Contoh: Bpk. Hendra Gunawan" 
                                     class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                          </div>

                          <div class="space-y-1">
                              <label class="font-bold text-slate-700 dark:text-slate-300">Email (Untuk Login)</label>
                              <input type="email" name="email" required placeholder="pembimbing@industri.com" 
                                     class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                          </div>

                          <div class="grid grid-cols-2 gap-3">
                              <div class="space-y-1">
                                  <label class="font-bold text-slate-700 dark:text-slate-300">Jabatan di DUDI</label>
                                  <input type="text" name="jabatan" placeholder="Contoh: HRD / Supervisor" 
                                         class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                              </div>
                              <div class="space-y-1">
                                  <label class="font-bold text-slate-700 dark:text-slate-300">No. WhatsApp / HP</label>
                                  <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" 
                                         class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                              </div>
                          </div>

                          <div class="space-y-1">
                              <label class="font-bold text-slate-700 dark:text-slate-300">Password Akun</label>
                              <input type="text" name="password" required value="pembimbing123" 
                                     class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                              <p class="text-[10px] text-slate-400">Password awal yang dapat dibagikan kepada pembimbing DUDI.</p>
                          </div>

                          <div class="pt-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-2">
                              <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 rounded-xl font-bold text-slate-700 dark:text-slate-300">Batal</button>
                              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-500/20">Simpan & Buat Akun</button>
                          </div>
                      </form>
                 </div>
            </div>
        </template>
    </div>
</x-app-layout>

