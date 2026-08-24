<x-app-layout>
    <x-slot name="header">Notifikasi</x-slot>

    @php
        $unreadCount = \App\Models\Notifikasi::where('to_user_id', auth()->id())
            ->where('is_read', false)
            ->whereNotIn('tipe', ['pesan_baru', 'pesan_broadcast'])
            ->count();
    @endphp

    <div class="max-w-3xl mx-auto space-y-4">
        <!-- Back link -->
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Dashboard
            </a>
        </div>

        <!-- Header & Quick Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-800/60">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Notifikasi</h1>
                    @if($unreadCount > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60">
                            {{ $unreadCount }} baru
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Pemberitahuan aktivitas, validasi, dan informasi sistem
                </p>
            </div>

            @if($notifikasis->count() > 0)
                <div class="flex items-center gap-1.5 self-start sm:self-auto">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read_all') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors cursor-pointer" title="Tandai semua telah dibaca">
                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                <span>Tandai dibaca</span>
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('notifications.clear_all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua riwayat notifikasi?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer" title="Hapus semua notifikasi">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Bersihkan</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Minimalist Device Permission Banner (Hanya muncul jika izin belum aktif / diblokir) -->
        <div id="notif-permission-banner" class="hidden p-3.5 rounded-xl bg-blue-50/70 dark:bg-slate-800/70 border border-blue-200/50 dark:border-slate-700/50 items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-blue-600/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i data-lucide="bell-ring" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white truncate" id="notif-banner-title">Aktifkan Notifikasi di HP</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" id="notif-banner-desc">Terima pemberitahuan langsung di status bar saat ada pesan atau validasi.</p>
                </div>
            </div>
            <button type="button" onclick="enableDeviceNotifications()" id="notif-enable-btn" class="shrink-0 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                Aktifkan
            </button>
        </div>

        <!-- Notification Feed List -->
        <div class="space-y-2.5">
            @forelse($notifikasis as $item)
                @php
                    $titleLower = strtolower($item->judul);
                    $icon = 'bell';
                    $iconClass = 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50';

                    if (str_contains($titleLower, 'setuju') || str_contains($titleLower, 'acc') || str_contains($titleLower, 'penerimaan')) {
                        $icon = 'check-circle';
                        $iconClass = 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50';
                    } elseif (str_contains($titleLower, 'tolak') || str_contains($titleLower, 'batal')) {
                        $icon = 'x-circle';
                        $iconClass = 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50';
                    } elseif (str_contains($titleLower, 'pembimbing') || str_contains($titleLower, 'pemetaan') || str_contains($titleLower, 'penempatan') || str_contains($titleLower, 'penugasan')) {
                        $icon = 'user-check';
                        $iconClass = 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50';
                    } elseif (str_contains($titleLower, 'pesan')) {
                        $icon = 'message-square';
                        $iconClass = 'text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/50';
                    }
                @endphp

                <!-- Notification Row Card -->
                <div class="group relative flex items-start justify-between gap-3.5 p-3.5 sm:p-4 rounded-xl border transition-all {{ $item->is_read ? 'bg-white/60 dark:bg-slate-900/60 border-slate-200/50 dark:border-slate-800/50 text-slate-500' : 'bg-white dark:bg-slate-900 border-blue-200/60 dark:border-blue-900/40 shadow-xs' }}">
                    
                    <!-- Hidden read form -->
                    @if(!$item->is_read)
                        <form id="read-form-{{ $item->id }}" action="{{ route('notifications.read', $item) }}" method="POST" class="hidden">
                            @csrf
                            @method('PATCH')
                            @if($item->link)
                                <input type="hidden" name="redirect" value="1">
                            @endif
                        </form>
                    @endif

                    <!-- Clickable Area -->
                    <div class="flex items-start gap-3 min-w-0 flex-1 cursor-pointer" onclick="handleNotificationClick('{{ $item->id }}', '{{ $item->is_read ? 1 : 0 }}', '{{ $item->link }}')">
                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $iconClass }}">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white leading-tight truncate">
                                    {{ $item->judul }}
                                </h3>
                                @if(!$item->is_read)
                                    <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2">
                                {{ $item->pesan }}
                            </p>

                            <div class="flex items-center gap-3 mt-2 text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                <span>{{ $item->created_at->diffForHumans() }}</span>
                                @if($item->link)
                                    <span class="inline-flex items-center gap-0.5 text-blue-600 dark:text-blue-400 hover:underline">
                                        Buka <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Delete button -->
                    <form action="{{ route('notifications.destroy', $item) }}" method="POST" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer opacity-70 group-hover:opacity-100" title="Hapus">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            @empty
                <!-- Clean Minimal Empty State -->
                <div class="py-16 text-center text-slate-400 dark:text-slate-500">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada notifikasi</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">Semua pemberitahuan aktivitas dan pengumuman akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifikasis->hasPages())
            <div class="pt-4">
                {{ $notifikasis->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function handleNotificationClick(id, isRead, link) {
            if (isRead === '1') {
                if (link) window.location.href = link;
            } else {
                const form = document.getElementById('read-form-' + id);
                if (form) {
                    form.submit();
                } else if (link) {
                    window.location.href = link;
                }
            }
        }

        function updateNotifPermissionUI() {
            const banner = document.getElementById('notif-permission-banner');
            const title = document.getElementById('notif-banner-title');
            const desc = document.getElementById('notif-banner-desc');
            const btn = document.getElementById('notif-enable-btn');
            if (!banner || !('Notification' in window)) return;

            if (Notification.permission === 'granted') {
                // Jika sudah aktif, sembunyikan banner agar layout tetap rapi & bersih
                banner.classList.add('hidden');
            } else if (Notification.permission === 'denied') {
                banner.classList.remove('hidden');
                banner.className = 'p-3 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between gap-3';
                if (title) {
                    title.textContent = 'Izin Notifikasi Diblokir';
                    title.className = 'text-xs font-semibold text-amber-900 dark:text-amber-300 truncate';
                }
                if (desc) desc.textContent = 'Buka setelan browser / ikon gembok untuk mengizinkan notifikasi.';
                if (btn) {
                    btn.className = 'shrink-0 px-2.5 py-1 bg-amber-600 hover:bg-amber-500 text-white rounded-lg text-[11px] font-semibold transition-colors cursor-pointer';
                    btn.textContent = 'Bantuan';
                    btn.onclick = function() {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Cara Mengaktifkan Notifikasi',
                                html: `
                                    <div class="text-xs text-left text-slate-600 space-y-1.5">
                                        <p>Izin notifikasi diblokir di browser Anda. Untuk mengaktifkannya:</p>
                                        <ol class="list-decimal ml-4 space-y-1">
                                            <li>Tap ikon <b>Gembok (🔒)</b> di samping URL browser.</li>
                                            <li>Ubah <b>Notifikasi</b> menjadi <b>Izinkan (Allow)</b>.</li>
                                            <li>Muat ulang halaman.</li>
                                        </ol>
                                    </div>
                                `,
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'Mengerti'
                            });
                        } else {
                            alert('Tap ikon gembok di samping URL dan pilih Izinkan Notifikasi.');
                        }
                    };
                }
            } else {
                // Default: belum diizinkan
                banner.classList.remove('hidden');
                banner.className = 'p-3 rounded-xl bg-blue-50/80 dark:bg-slate-800/80 border border-blue-200/50 dark:border-slate-700/50 flex items-center justify-between gap-3';
                if (title) {
                    title.textContent = 'Aktifkan Notifikasi di HP';
                    title.className = 'text-xs font-semibold text-slate-900 dark:text-white truncate';
                }
                if (desc) desc.textContent = 'Terima pemberitahuan pesan & validasi di status bar.';
                if (btn) {
                    btn.className = 'shrink-0 px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer';
                    btn.textContent = 'Aktifkan';
                    btn.onclick = enableDeviceNotifications;
                }
            }
            if (window.lucide) lucide.createIcons();
        }

        function enableDeviceNotifications() {
            if (!('Notification' in window)) return;
            Notification.requestPermission().then(perm => {
                updateNotifPermissionUI();
                if (perm === 'granted') {
                    if (window.showToast) window.showToast('Notifikasi perangkat berhasil diaktifkan!', 'success');
                    new Notification('MAS-PKL: Notifikasi Aktif', {
                        body: 'Pemberitahuan pesan & absensi akan muncul langsung di HP Anda.',
                        icon: '{{ asset("logo.png") }}',
                        badge: '{{ asset("icons/badge-96x96.png") }}',
                        vibrate: [200, 100, 200]
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateNotifPermissionUI();
        });
    </script>
    @endpush
</x-app-layout>
