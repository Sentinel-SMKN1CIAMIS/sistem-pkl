<x-app-layout>
    <x-slot name="header">Daftar Pengajuan PKL Siswa</x-slot>

    <div class="mb-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="glass-card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-sm">
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Siswa</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Kelas</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Perusahaan Tujuan</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Status</th>
                        <th class="py-3 px-4 text-slate-500 dark:text-slate-400 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pengajuans as $pengajuan)
                    <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="py-3 px-4 text-slate-800 dark:text-slate-200 font-medium">
                            {{ $pengajuan->siswa->nama_lengkap }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            {{ $pengajuan->siswa->kelas }}
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->nama_perusahaan }}</p>
                            @if($pengajuan->alamat)
                                <p class="text-xs text-slate-500">{{ Str::limit($pengajuan->alamat, 50) }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($pengajuan->status === 'menunggu')
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">Menunggu</span>
                            @elseif($pengajuan->status === 'disetujui')
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium">Disetujui</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($pengajuan->status === 'menunggu')
                                <button onclick="openModal({{ $pengajuan->id }})" class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-xs font-medium transition-colors">
                                    Tinjau
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal for this pengajuan -->
                    <dialog id="modal-{{ $pengajuan->id }}" class="bg-transparent m-auto w-full max-w-lg">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 border border-slate-200 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Tinjau Pengajuan PKL</h3>
                            <div class="space-y-3 mb-6 text-sm">
                                <div><span class="text-slate-500 block text-xs">Siswa</span> <span class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->siswa->nama_lengkap }} ({{ $pengajuan->siswa->kelas }})</span></div>
                                <div><span class="text-slate-500 block text-xs">Perusahaan</span> <span class="font-medium text-slate-800 dark:text-slate-200">{{ $pengajuan->nama_perusahaan }}</span></div>
                                <div><span class="text-slate-500 block text-xs">Pimpinan</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->pimpinan ?? '-' }}</span></div>
                                <div><span class="text-slate-500 block text-xs">No. Telp</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->no_telp ?? '-' }}</span></div>
                                <div><span class="text-slate-500 block text-xs">Alamat</span> <span class="text-slate-700 dark:text-slate-300">{{ $pengajuan->alamat ?? '-' }}</span></div>
                            </div>

                            <form action="{{ route('pembimbing_sekolah.pengajuan_pkl.update', $pengajuan) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Aksi</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status" value="disetujui" class="text-emerald-600 focus:ring-emerald-500" required onchange="toggleCatatan({{ $pengajuan->id }}, false)">
                                            <span class="text-sm font-medium text-emerald-700">Setujui</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status" value="ditolak" class="text-red-600 focus:ring-red-500" required onchange="toggleCatatan({{ $pengajuan->id }}, true)">
                                            <span class="text-sm font-medium text-red-700">Tolak</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="catatan-container-{{ $pengajuan->id }}" class="hidden mb-6">
                                    <label for="catatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alasan Penolakan (Opsional)</label>
                                    <textarea name="catatan" rows="3" class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <button type="button" onclick="closeModal({{ $pengajuan->id }})" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg shadow-blue-500/25 transition-colors">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">Belum ada pengajuan PKL dari siswa di kelas Anda.</td>
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
            if(show) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
