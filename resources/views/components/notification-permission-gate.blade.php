{{-- Mandatory Notification Permission Gate (Absolute Harus Diizinkan di Browser yang Mendukung) --}}
<div id="notif-permission-gate"
     class="hidden fixed inset-0 z-[10000] bg-slate-900/80 flex items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="notif-gate-title">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-700 flex items-center justify-center shrink-0">
                <i data-lucide="bell-ring" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 id="notif-gate-title" class="text-sm font-extrabold">Wajib Aktifkan Notifikasi</h3>
                <p class="text-[11px] text-blue-100">Agar pesan, pengingat absen, &amp; pengumuman langsung masuk ke HP/Web.</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            {{-- Default state: user belum klik Izinkan --}}
            <div id="notif-gate-default" class="space-y-4">
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Aplikasi <strong>MAS-PKL</strong> memerlukan izin notifikasi agar pesan dari pembimbing/Pokja, pengingat jam absen, dan pengumuman penting tetap masuk meskipun layar HP terkunci.
                </p>
                <button type="button" id="notif-gate-allow-btn"
                        class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-extrabold flex items-center justify-center gap-2 transition-colors cursor-pointer">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    Izinkan Notifikasi Sekarang
                </button>
            </div>

            {{-- Denied state: browser sudah memblokir --}}
            <div id="notif-gate-denied" class="hidden space-y-4">
                <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/60 text-xs text-red-700 dark:text-red-300 space-y-1.5">
                    <p class="font-bold">Izin notifikasi saat ini sedang diblokir oleh browser Anda.</p>
                    <ol class="list-decimal pl-4 space-y-1 text-[11px]">
                        <li>Ketuk ikon <strong>gembok / setelan situs</strong> di sebelah kiri alamat website (address bar).</li>
                        <li>Pilih menu <strong>Izin / Permissions</strong> &rarr; <strong>Notifikasi</strong>.</li>
                        <li>Ubah menjadi <strong>Izinkan / Allow</strong>, lalu klik tombol di bawah.</li>
                    </ol>
                </div>
                <button type="button" id="notif-gate-recheck-btn"
                        class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-extrabold flex items-center justify-center gap-2 transition-colors cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Sudah Diizinkan — Muat Ulang
                </button>
                <button type="button" id="notif-gate-dismiss-denied-btn"
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition-colors cursor-pointer">
                    Lanjutkan Dulu (Akan Ditanyakan Lagi Saat Masuk Kembali)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        // Hanya wajibkan jika browser mendukung Notification API & berjalan di HTTPS / localhost
        if (!('Notification' in window) || !window.isSecureContext) {
            return;
        }

        const gate = document.getElementById('notif-permission-gate');
        const defaultBox = document.getElementById('notif-gate-default');
        const deniedBox = document.getElementById('notif-gate-denied');
        const allowBtn = document.getElementById('notif-gate-allow-btn');
        const recheckBtn = document.getElementById('notif-gate-recheck-btn');
        const dismissDeniedBtn = document.getElementById('notif-gate-dismiss-denied-btn');
        if (!gate) return;

        const DISMISS_KEY = 'notif_denied_dismissed_once';
        let leftApp = false;

        function evaluatePermission(forceShow = false) {
            const perm = Notification.permission;
            if (perm === 'granted') {
                sessionStorage.removeItem(DISMISS_KEY);
                gate.classList.add('hidden');
                document.body.style.overflow = '';
                if (typeof window.syncWebPushSubscription === 'function') {
                    window.syncWebPushSubscription(false);
                }
                return true;
            }

            if (perm === 'denied' && !forceShow && sessionStorage.getItem(DISMISS_KEY) === '1') {
                gate.classList.add('hidden');
                document.body.style.overflow = '';
                return false;
            }

            gate.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            if (perm === 'denied') {
                defaultBox.classList.add('hidden');
                deniedBox.classList.remove('hidden');
            } else {
                defaultBox.classList.remove('hidden');
                deniedBox.classList.add('hidden');
            }
            if (window.lucide) lucide.createIcons();
            return false;
        }

        allowBtn?.addEventListener('click', async () => {
            allowBtn.disabled = true;
            try {
                const result = await Notification.requestPermission();
                if (result === 'granted') {
                    if (typeof window.syncWebPushSubscription === 'function') {
                        await window.syncWebPushSubscription(true);
                    }
                }
            } catch (e) {
                // ignore
            } finally {
                allowBtn.disabled = false;
                evaluatePermission(true);
            }
        });

        recheckBtn?.addEventListener('click', () => {
            sessionStorage.removeItem(DISMISS_KEY);
            if (!evaluatePermission(true)) {
                window.location.reload();
            }
        });

        dismissDeniedBtn?.addEventListener('click', () => {
            sessionStorage.setItem(DISMISS_KEY, '1');
            gate.classList.add('hidden');
            document.body.style.overflow = '';
        });

        evaluatePermission();

        // Jika user sempat keluar dari app/tab lalu masuk lagi, munculkan lagi pengingatnya
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                leftApp = true;
            } else if (document.visibilityState === 'visible' && leftApp) {
                leftApp = false;
                if (Notification.permission !== 'granted') {
                    sessionStorage.removeItem(DISMISS_KEY);
                    evaluatePermission(true);
                }
            }
        });

        // Pantau jika user mengubah izin lewat setelan browser secara live
        if (navigator.permissions && navigator.permissions.query) {
            navigator.permissions.query({ name: 'notifications' }).then(status => {
                status.onchange = () => evaluatePermission(true);
            }).catch(() => {});
        }
    });
})();
</script>
