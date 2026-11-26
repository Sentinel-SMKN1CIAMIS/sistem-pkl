@props([
    'totalAtRisk' => 0,
    'criticalCount' => 0,
    'warningCount' => 0,
])

<div x-data="{
        open: false,
        mode: 'bulk',
        siswaId: '',
        siswaName: '',
        filterSeverity: 'all',
        judul: 'Pengingat Absensi & Jurnal PKL',
        pesan: 'Halo {nama}, sistem mencatat Anda perlu perhatian terkait: {alasan}. Mohon segera lengkapi absensi & jurnal PKL Anda atau balas pesan ini jika ada kendala di tempat DUDI.',
        openModal(detail) {
            this.mode = detail?.mode || 'bulk';
            this.siswaId = detail?.siswaId || '';
            this.siswaName = detail?.siswaName || '';
            this.filterSeverity = detail?.filterSeverity || 'all';
            if (this.mode === 'single' && detail?.reasons) {
                this.pesan = 'Halo ' + this.siswaName + ', sistem mencatat: ' + detail.reasons + '. Mohon segera konfirmasi dan lengkapi kewajiban PKL Anda melalui aplikasi MAS-PKL.';
            } else {
                this.pesan = 'Halo {nama}, sistem mencatat Anda perlu perhatian terkait: {alasan}. Mohon segera lengkapi absensi & jurnal PKL Anda atau balas pesan ini jika ada kendala di tempat DUDI.';
            }
            this.open = true;
        }
    }"
    @open-ew-broadcast.window="openModal($event.detail)"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60"
    role="dialog"
    aria-modal="true"
    aria-label="Kirim Pengingat Siswa Berisiko">
    <div @click.outside="open = false"
         class="w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
        {{-- Modal Header (Solid Amber, Zero Gradient) --}}
        <div class="px-6 py-4 bg-amber-500 text-white flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i data-lucide="megaphone" class="w-5 h-5 shrink-0"></i>
                <div>
                    <h3 class="text-sm font-extrabold" x-text="mode === 'single' ? 'Kirim Teguran / Pengingat Personal' : 'Broadcast Pengingat Siswa Berisiko'"></h3>
                    <p class="text-[11px] text-amber-50" x-text="mode === 'single' ? ('Target: ' + siswaName) : 'Masuk ke Pesan Chat, Notifikasi Web, & Push Notif HP'"></p>
                </div>
            </div>
            <button type="button" @click="open = false" class="p-1.5 rounded-lg hover:bg-amber-600 transition-colors cursor-pointer" aria-label="Tutup modal">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('early_warning.broadcast') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="mode" :value="mode">
            <input type="hidden" name="siswa_id" :value="siswaId">

            {{-- Delivery Channel Info --}}
            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-2.5">
                <i data-lucide="bell-ring" class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold block">Terkirim ke 3 Jalur Otomatis:</span>
                    <span class="text-[11px] text-blue-700 dark:text-blue-300">Inbox Pesan (siswa bisa balas), Notifikasi Lonceng &amp; Toast Web real-time, serta Push Notification langsung ke HP siswa.</span>
                </div>
            </div>

            {{-- Target Severity Filter (Bulk Mode Only) --}}
            <div x-show="mode === 'bulk'">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Penerima</label>
                <select name="filter_severity" x-model="filterSeverity"
                        class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    <option value="all">Semua Siswa Berisiko ({{ $totalAtRisk }} siswa)</option>
                    <option value="critical">Hanya Kritis — Tidak Absen &ge; 3 Hari Kerja ({{ $criticalCount }} siswa)</option>
                    <option value="warning">Hanya Jurnal Tertunggak &gt; 5 Hari ({{ $warningCount }} siswa)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Judul Notifikasi</label>
                <input type="text" name="judul" x-model="judul" required maxlength="150"
                       class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Isi Pesan Pengingat</label>
                    <span x-show="mode === 'bulk'" class="text-[10px] font-mono text-slate-500">Placeholder: {nama}, {alasan}</span>
                </div>
                <textarea name="pesan" x-model="pesan" rows="4" required minlength="10" maxlength="1000"
                          class="w-full px-3.5 py-2.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 leading-relaxed"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="open = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold transition-colors cursor-pointer">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span x-text="mode === 'single' ? 'Kirim Sekarang' : 'Kirim Broadcast'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
