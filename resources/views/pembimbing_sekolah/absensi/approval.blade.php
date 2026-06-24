<x-app-layout>
    <x-slot name="header">Persetujuan Kehadiran Siswa</x-slot>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-slate-700 dark:text-slate-300">
        <!-- Pending Requests -->
        <div class="lg:col-span-2">
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50">
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-yellow-400"></i>
                        Permintaan Menunggu Persetujuan
                    </h3>
                </div>

                @if($pendingAbsences->count() > 0)
                    <div class="divide-y divide-slate-700/50">
                        @foreach($pendingAbsences as $absence)
                            <div class="p-6 hover:bg-white dark:bg-slate-800/10 transition-colors">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ $absence->siswa->user->name }}</h4>
                                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($absence->tanggal)->isoFormat('D MMMM YYYY') }}</p>
                                    </div>
                                    @php
                                        $statusConfig = [
                                            'izin' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'label' => 'Izin'],
                                            'sakit' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'label' => 'Sakit'],
                                            'alpha' => ['bg' => 'bg-gray-500/10', 'text' => 'text-gray-400', 'label' => 'Alpa'],
                                        ];
                                        $config = $statusConfig[$absence->status] ?? [];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $config['bg'] ?? '' }} {{ $config['text'] ?? '' }} whitespace-nowrap">
                                        {{ $config['label'] ?? $absence->status }}
                                    </span>
                                </div>

                                @if($absence->alasan)
                                    <div class="mb-4 p-3 bg-slate-500/10 rounded-lg border border-slate-500/20">
                                        <p class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Alasan:</p>
                                        <p class="text-slate-900 dark:text-slate-100">{{ $absence->alasan }}</p>
                                    </div>
                                @endif

                                <div class="flex gap-2 sm:gap-3 flex-wrap sm:flex-nowrap">
                                    <!-- Approve Form -->
                                    <form action="{{ route('pembimbing_sekolah.absensi.approve', $absence) }}" method="POST" class="flex-1 min-w-[80px]">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full px-3 py-2 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors font-medium text-xs sm:text-sm flex items-center justify-center gap-1 sm:gap-2">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- Reject Modal Trigger -->
                                    <button type="button" onclick="openRejectModal({{ $absence->id }})" class="flex-1 min-w-[80px] px-3 py-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-colors font-medium text-xs sm:text-sm flex items-center justify-center gap-1 sm:gap-2">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        Tolak
                                    </button>


                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-6 border-t border-slate-200/50 dark:border-slate-700/50">
                        {{ $pendingAbsences->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400"></i>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400">Tidak ada permintaan menunggu persetujuan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Approval History -->
        <div class="lg:col-span-1">
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50">
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5 text-blue-400"></i>
                        Riwayat Persetujuan
                    </h3>
                </div>

                <div class="divide-y divide-slate-700/50 max-h-96 overflow-y-auto">
                    @forelse($approvalHistory as $history)
                        <div class="p-4 hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <p class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $history->siswa->user->name }}</p>
                                @php
                                    $historyConfig = [
                                        'approved' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'icon' => 'check', 'label' => 'Disetujui'],
                                        'rejected' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'icon' => 'x', 'label' => 'Ditolak'],
                                    ];
                                    $historyConf = $historyConfig[$history->approval_status] ?? [];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $historyConf['bg'] ?? '' }} {{ $historyConf['text'] ?? '' }}">
                                    {{ $historyConf['label'] ?? $history->approval_status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($history->tanggal)->format('d M Y') }}</p>
                            @if($history->status)
                                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 italic">{{ ucfirst($history->status) }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-600 dark:text-slate-400 text-sm">
                            Belum ada riwayat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full mx-4">
            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                Tolak Permintaan
            </h3>

            <form id="reject-form" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">Alasan Penolakan</label>
                    <textarea name="approval_note" rows="4" placeholder="Jelaskan mengapa permintaan ini ditolak..." required class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 rounded-lg bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition-colors font-medium">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>



    @push('scripts')
    <script>
        function openRejectModal(absenceId) {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');
            form.action = `/pembimbing_sekolah/absensi/${absenceId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });


    </script>
    @endpush
</x-app-layout>
