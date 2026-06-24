<x-app-layout>
    <x-slot name="header">Kehadiran Siswa PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Pantau dan cek tanda tangan serta lokasi kehadiran siswa hari ini.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($pendingEarlyLeaves->count() > 0)
        <div class="mb-8 glass-card border-orange-500/20 dark:border-orange-500/20 overflow-hidden">
            <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50 bg-orange-500/5">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-500 animate-pulse"></i>
                    Permintaan Izin Pulang Lebih Awal Menunggu Persetujuan
                </h3>
            </div>
            <div class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                @foreach($pendingEarlyLeaves as $earlyLeave)
                    <div class="p-6 hover:bg-white/5 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ $earlyLeave->siswa->nama_lengkap }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">NIS: {{ $earlyLeave->siswa->nis }}</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                    <span class="font-semibold">Tanggal Absen:</span> {{ \Carbon\Carbon::parse($earlyLeave->tanggal)->isoFormat('D MMMM YYYY') }} 
                                    | <span class="font-semibold">Jam Masuk:</span> {{ \Carbon\Carbon::parse($earlyLeave->waktu_datang)->format('H:i') }}
                                </p>
                                <div class="mt-3 p-3 bg-slate-500/5 dark:bg-slate-800/30 rounded-xl border border-slate-200/30 dark:border-slate-700/30">
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Alasan Pulang Lebih Awal:</p>
                                    <p class="text-sm text-slate-800 dark:text-slate-200 italic">"{{ $earlyLeave->early_leave_reason }}"</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openApproveModal({{ $earlyLeave->id }})" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5 shadow-md shadow-emerald-500/20">
                                    <i data-lucide="check" class="w-4 h-4"></i> Setujui
                                </button>
                                <button type="button" onclick="openRejectModal({{ $earlyLeave->id }})" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5 shadow-md shadow-red-500/20">
                                    <i data-lucide="x" class="w-4 h-4"></i> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Jam Datang</th>
                        <th class="px-6 py-4 whitespace-nowrap">Jam Pulang</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanda Tangan</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-900 dark:text-slate-100 font-medium block">{{ $row->siswa->nama_lengkap }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $row->siswa->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                @if($row->status === 'hadir' && $row->alasan)
                                    <br><span class="text-[10px] text-orange-500 font-medium" title="{{ $row->alasan }}"><i data-lucide="info" class="w-3 h-3 inline"></i> {{ Str::limit($row->alasan, 20) }}</span>
                                @endif

                                @if($row->early_leave_request_status && $row->early_leave_request_status !== 'none')
                                    @if($row->early_leave_request_status === 'pending')
                                        <br>
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20 cursor-pointer animate-pulse" onclick="openApproveModal({{ $row->id }})" title="Menunggu persetujuan pulang cepat. Klik untuk respon.">
                                            <i data-lucide="clock" class="w-2.5 h-2.5"></i>
                                            Pulang Cepat (Menunggu)
                                        </span>
                                    @elseif($row->early_leave_request_status === 'approved')
                                        <br>
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="{{ $row->early_leave_approval_note ? 'Catatan: ' . $row->early_leave_approval_note : 'Disetujui' }}">
                                            <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                            Pulang Cepat (Disetujui)
                                        </span>
                                    @elseif($row->early_leave_request_status === 'rejected')
                                        <br>
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20" title="{{ $row->early_leave_approval_note ? 'Alasan: ' . $row->early_leave_approval_note : 'Ditolak' }}">
                                            <i data-lucide="x" class="w-2.5 h-2.5"></i>
                                            Pulang Cepat (Ditolak)
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->ttd_siswa_path)
                                    <button class="p-1.5 rounded-lg bg-slate-100/10 hover:bg-slate-100/20 border border-slate-100/10 group relative" 
                                            onclick="showSignature('{{ asset('storage/' . $row->ttd_siswa_path) }}')">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-700 dark:text-slate-300"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($row->latitude)
                                    <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500/20 transition-all font-bold text-xs">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                        Map
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data kehadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>

    <!-- Approve Modal -->
    <div id="approve-modal" class="hidden fixed inset-0 z-100 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" onclick="closeApproveModal()">
        <div class="glass-card max-w-md w-full p-6 relative border-emerald-500/20" onclick="event.stopPropagation()">
            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
                Setujui Izin Pulang Cepat
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Apakah Anda yakin ingin menyetujui permohonan pulang lebih awal untuk siswa ini?</p>
            <form id="approve-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Catatan Persetujuan (Opsional)</label>
                    <textarea name="approval_note" rows="3" placeholder="Tulis catatan (misal: 'Pekerjaan hari ini sudah selesai')" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeApproveModal()" class="flex-1 px-4 py-2 rounded-lg bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white transition-colors font-medium shadow-lg shadow-emerald-500/20">
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 z-100 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" onclick="closeRejectModal()">
        <div class="glass-card max-w-md w-full p-6 relative border-red-500/20" onclick="event.stopPropagation()">
            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                Tolak Izin Pulang Cepat
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Apakah Anda yakin ingin menolak permohonan pulang lebih awal untuk siswa ini?</p>
            <form id="reject-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Alasan Penolakan (Wajib, min 5 karakter)</label>
                    <textarea name="approval_note" rows="3" placeholder="Tulis alasan penolakan (misal: 'Pekerjaan masih belum selesai')" required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 rounded-lg bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors font-medium shadow-lg shadow-red-500/20">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Signature -->
    <div id="sig-modal" class="fixed inset-0 z-100 hidden items-center justify-center p-4 bg-slate-900/60" onclick="closeModal()">
        <div class="glass-card max-w-lg w-full p-2 relative" onclick="event.stopPropagation()">
            <img id="sig-img" src="" alt="Digital Signature" class="w-full bg-white rounded-xl">
            <button onclick="closeModal()" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-red-600 text-slate-900 dark:text-white flex items-center justify-center shadow-lg border border-red-500">
                 <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function showSignature(url) {
            document.getElementById('sig-img').src = url;
            const modal = document.getElementById('sig-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('sig-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openApproveModal(id) {
            const modal = document.getElementById('approve-modal');
            const form = document.getElementById('approve-form');
            form.action = `/pembimbing_dudi/absensi/${id}/approve-early-leave`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeApproveModal() {
            const modal = document.getElementById('approve-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        function openRejectModal(id) {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');
            form.action = `/pembimbing_dudi/absensi/${id}/reject-early-leave`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeRejectModal() {
            const modal = document.getElementById('reject-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    @endpush
</x-app-layout>
