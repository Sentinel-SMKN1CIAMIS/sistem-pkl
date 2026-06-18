<x-app-layout>
    <x-slot name="header">Validasi Pengajuan PKL Siswa (Pokja)</x-slot>

    <div class="mb-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    @if($pengajuans->count() > 0)
        <div class="flex justify-end mb-4">
            <form action="{{ route('pokja.pengajuan_pkl.clear_all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh data pengajuan siswa? Semua berkas lampiran juga akan dihapus permanen.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition-all border border-rose-500/20 hover:border-rose-500/30 cursor-pointer">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Semua Pengajuan
                </button>
            </form>
        </div>
    @endif

    <div class="glass-card p-6">
        <div class="overflow-x-auto lg:overflow-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-sm">
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Siswa</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Kelas / Konsentrasi</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Perusahaan Tujuan</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Status</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pengajuans as $pengajuan)
                    <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="py-3 px-4 text-slate-800 dark:text-slate-200 font-medium">
                            {{ $pengajuan->siswa->nama_lengkap }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            <span class="block">{{ $pengajuan->siswa->kelas }}</span>
                            <span class="text-xs text-slate-500">{{ $pengajuan->siswa->konsentrasiKeahlian->nama }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->nama_perusahaan }}</p>
                            @if($pengajuan->alamat)
                                <p class="text-xs text-slate-500">{{ Str::limit($pengajuan->alamat, 50) }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($pengajuan->status === 'menunggu')
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">Menunggu Kaprog</span>
                            @elseif($pengajuan->status === 'disetujui_kaprog')
                                <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold animate-pulse">Butuh Validasi Pokja</span>
                            @elseif($pengajuan->status === 'disetujui')
                                <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-semibold">Disetujui Pokja</span>
                            @else
                                <span class="px-2.5 py-1 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 rounded-lg text-xs font-semibold">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-flex justify-end" x-on:click.away="open = false">
                                <button x-on:click="open = !open" class="p-1.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-8 w-36 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                     style="display: none;">
                                     
                                    @if($pengajuan->status === 'disetujui_kaprog')
                                        <button type="button" onclick="openModal('{{ $pengajuan->id }}'); open = false" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left cursor-pointer">
                                            <i data-lucide="check-square" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Validasi
                                        </button>
                                    @endif

                                    <form action="{{ route('pokja.pengajuan_pkl.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan PKL ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left cursor-pointer">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-500"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal untuk validasi Pokja -->
                    <dialog id="modal-{{ $pengajuan->id }}" class="backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm bg-transparent border-0 outline-none p-0 w-full max-w-lg m-auto">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 border border-slate-200/50 dark:border-slate-700/50 text-slate-700 dark:text-slate-300">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Validasi Pengajuan PKL Siswa</h3>
                            
                            <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl mb-4 text-xs">
                                <span class="font-bold text-blue-500 block mb-1">Catatan Persetujuan Kaprog:</span>
                                <p class="text-slate-600 dark:text-slate-400">Disetujui oleh Kepala Program Keahlian.</p>
                            </div>

                            <div class="space-y-3 mb-6 text-sm">
                                <div><span class="text-slate-500 block text-xs">Siswa</span> <span class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->siswa->nama_lengkap }} ({{ $pengajuan->siswa->kelas }})</span></div>
                                <div><span class="text-slate-500 block text-xs">Perusahaan</span> <span class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->nama_perusahaan }}</span></div>
                                <div><span class="text-slate-500 block text-xs">Pimpinan</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->pimpinan ?? '-' }}</span></div>
                                <div><span class="text-slate-500 block text-xs">No. Telp</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->no_telp ?? '-' }}</span></div>
                                <div><span class="text-slate-500 block text-xs">Alamat</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->alamat ?? '-' }}</span></div>
                            </div>

                            <form action="{{ route('pokja.pengajuan_pkl.validasi', $pengajuan->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Aksi Validasi Pokja</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status" value="disetujui" class="text-emerald-600 focus:ring-emerald-500" required onchange="toggleCatatan('{{ $pengajuan->id }}', false)">
                                            <span class="text-sm font-semibold text-emerald-700">Setujui & Terbitkan Surat Pengantar</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status" value="ditolak" class="text-red-600 focus:ring-red-500" required onchange="toggleCatatan('{{ $pengajuan->id }}', true)">
                                            <span class="text-sm font-semibold text-red-700">Tolak</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="catatan-container-{{ $pengajuan->id }}" class="hidden mb-6">
                                    <label for="catatan" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alasan Penolakan (Wajib jika ditolak)</label>
                                    <textarea name="catatan" rows="3" class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <button type="button" onclick="closeModal('{{ $pengajuan->id }}')" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">Batal</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg shadow-blue-500/25 transition-colors cursor-pointer">Simpan Validasi</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">Belum ada pengajuan PKL yang membutuhkan validasi Pokja.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $pengajuans->links() }}
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).showModal();
        }
        function closeModal(id) {
            document.getElementById('modal-' + id).close();
        }
        function toggleCatatan(id, show) {
            const container = document.getElementById('catatan-container-' + id);
            const textarea = container.querySelector('textarea');
            if(show) {
                container.classList.remove('hidden');
                textarea.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                textarea.removeAttribute('required');
            }
        }
    </script>
</x-app-layout>
