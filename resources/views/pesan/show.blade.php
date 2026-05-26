<x-app-layout>
    <x-slot name="header">Pesan</x-slot>

    <style>
        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateY(1rem) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .animate-toast-in {
            animation: toast-in 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast-dismiss {
            opacity: 0 !important;
            transform: translateY(1rem) scale(0.95) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
    </style>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <div class="flex flex-col md:flex-row gap-0 h-[calc(100vh-12rem)] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">

        {{-- Panel Kiri: Daftar Kontak --}}
        <div class="hidden md:flex w-80 flex-shrink-0 border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex-col">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Percakapan</h2>
                @if(in_array(auth()->user()->role, ['pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin']))
                <button onclick="document.getElementById('broadcastModal').classList.remove('hidden')"
                        class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors" title="Broadcast ke semua kontak">
                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                </button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse($kontakWithMeta as $meta)
                    <a href="{{ route('pesan.show', $meta->user) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors {{ $meta->user->id === $user->id ? 'bg-blue-50 dark:bg-blue-500/10 border-l-2 border-l-blue-500' : '' }}">
                        <div class="w-10 h-10 flex-none aspect-square rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($meta->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $meta->user->name }}</span>
                                @if($meta->last_msg)
                                    <span class="text-[10px] text-slate-400 flex-shrink-0">{{ $meta->last_msg->created_at->format('H:i') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs text-slate-400 dark:text-slate-500 truncate">
                                    @if($meta->last_msg)
                                        {{ $meta->last_msg->from_user_id === auth()->id() ? 'Anda: ' : '' }}{{ $meta->last_msg->isi }}
                                    @else
                                        <span class="italic">Belum ada pesan</span>
                                    @endif
                                </span>
                                @if($meta->unread > 0)
                                    <span class="flex-shrink-0 bg-blue-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                        {{ $meta->unread > 9 ? '9+' : $meta->unread }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center h-40 text-slate-400 dark:text-slate-500 text-sm text-center p-6">
                        <i data-lucide="users" class="w-10 h-10 mb-3 opacity-40"></i>
                        <p>Belum ada kontak.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Panel Kanan: Thread Chat --}}
        <div class="flex-1 flex flex-col bg-slate-50 dark:bg-slate-800/50 min-w-0">

            {{-- Header Chat --}}
            <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex-shrink-0">
                <a href="{{ route('pesan.index') }}" class="md:hidden p-1.5 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div style="min-width: 36px; min-height: 36px; flex-shrink: 0;" class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                </div>
            </div>

            {{-- Offline / Connection Error Bar --}}
            <div id="connection-warning" class="hidden bg-amber-500 text-white text-xs px-4 py-2 flex items-center justify-between transition-all duration-300">
                <span class="flex items-center gap-2">
                    <i data-lucide="wifi-off" class="w-3.5 h-3.5 animate-pulse"></i>
                    <span id="connection-warning-text">Koneksi terputus. Mencoba menghubungkan kembali...</span>
                </span>
                <button onclick="checkConnectionNow()" class="underline font-semibold hover:text-amber-100 dark:hover:text-amber-200 transition-colors">Coba Sekarang</button>
            </div>

            {{-- Area Pesan --}}
            <div id="chat-area" class="flex-1 overflow-y-auto px-4 py-4 flex flex-col gap-2 scroll-smooth">
                @foreach($messages as $msg)
                    @php $mine = $msg->from_user_id === auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $msg->id }}">
                        <div class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm shadow-sm
                            {{ $mine
                                ? 'bg-blue-600 text-white rounded-br-sm'
                                : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-bl-sm' }}">
                            <p class="whitespace-pre-wrap break-words">{{ $msg->isi }}</p>
                            <p class="text-[10px] mt-1 {{ $mine ? 'text-blue-200' : 'text-slate-400' }} text-right flex items-center justify-end gap-1">
                                {{ $msg->created_at->format('H:i') }}
                                @if($mine)
                                    <i data-lucide="{{ $msg->dibaca_at ? 'check-check' : 'check' }}" class="w-3 h-3 flex-shrink-0 {{ $msg->dibaca_at ? 'text-blue-200' : 'text-blue-300' }}"></i>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
                <div id="chat-bottom"></div>
            </div>

            {{-- Form Kirim Pesan --}}
            <div class="px-4 py-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 flex-shrink-0">
                <form id="send-form" class="flex gap-2 items-end">
                    @csrf
                    <textarea id="msg-input" name="isi" rows="1" required
                              class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all"
                              placeholder="Ketik pesan..." style="max-height:120px"></textarea>
                    <button type="submit"
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors shadow-md">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Broadcast Modal --}}
    <div id="broadcastModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-1">📢 Broadcast Pesan</h3>
            <p class="text-xs text-slate-400 mb-4">Pesan akan dikirim ke <strong>semua kontak</strong> Anda sekaligus.</p>
            <form action="{{ route('pesan.broadcast') }}" method="POST">
                @csrf
                <textarea name="isi" rows="4" required
                          class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                          placeholder="Tulis pesan broadcast di sini..."></textarea>
                <div class="flex gap-3 mt-4 justify-end">
                    <button type="button" onclick="document.getElementById('broadcastModal').classList.add('hidden')"
                            class="px-4 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Kirim Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
    const POLL_URL  = "{{ route('pesan.poll', $user) }}";
    const SEND_URL  = "{{ route('pesan.store', $user) }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const chatArea  = document.getElementById('chat-area');
    const chatBottom = document.getElementById('chat-bottom');
    const form      = document.getElementById('send-form');
    const input     = document.getElementById('msg-input');
    const authId    = {{ auth()->id() }};

    let lastId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
    let pollFailures = 0;

    // Toast Notification System
    function showToast(message, type = 'error') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm max-w-sm transition-all duration-300 transform animate-toast-in
            ${type === 'error' 
                ? 'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/30 text-red-800 dark:text-red-200' 
                : 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-200'}`;
        
        const icon = document.createElement('i');
        icon.dataset.lucide = type === 'error' ? 'alert-triangle' : 'check-circle';
        icon.className = `w-5 h-5 flex-shrink-0 ${type === 'error' ? 'text-red-500' : 'text-emerald-500'}`;
        
        const textSpan = document.createElement('span');
        textSpan.className = 'font-medium flex-1 leading-tight';
        textSpan.innerText = message;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ml-auto flex-shrink-0';
        closeBtn.innerHTML = '<i data-lucide="x" class="w-4 h-4"></i>';
        closeBtn.onclick = () => {
            toast.classList.add('toast-dismiss');
            setTimeout(() => toast.remove(), 300);
        };

        toast.appendChild(icon);
        toast.appendChild(textSpan);
        toast.appendChild(closeBtn);
        container.appendChild(toast);

        if (window.lucide) lucide.createIcons();

        // Auto-remove after time (12s for errors, 6s for success)
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('toast-dismiss');
                setTimeout(() => toast.remove(), 300);
            }
        }, type === 'error' ? 12000 : 6000);
    }

    // Scroll ke bawah
    function scrollBottom() {
        chatBottom.scrollIntoView({ behavior: 'smooth' });
    }
    scrollBottom();

    // Auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    });

    // Enter kirim, Shift+Enter newline
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    // Render satu bubble baru
    function renderBubble(msg) {
        const mine = msg.mine;
        const wrap = document.createElement('div');
        wrap.className = `flex ${mine ? 'justify-end' : 'justify-start'}`;
        wrap.dataset.msgId = msg.id;
        wrap.innerHTML = `
            <div class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm shadow-sm
                ${mine
                    ? 'bg-blue-600 text-white rounded-br-sm'
                    : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-bl-sm'}">
                <p class="whitespace-pre-wrap break-words">${escHtml(msg.isi)}</p>
                <p class="text-[10px] mt-1 ${mine ? 'text-blue-200' : 'text-slate-400'} text-right">
                    ${msg.time}
                    ${mine ? `<i data-lucide="${msg.read ? 'check-check' : 'check'}" class="w-3 h-3 flex-shrink-0 ml-0.5 inline ${msg.read ? 'text-blue-200' : 'text-blue-300'}"></i>` : ''}
                </p>
            </div>`;
        chatBottom.before(wrap);
        if (window.lucide) lucide.createIcons();
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Kirim pesan via AJAX dengan Error Handling kuat
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const isi = input.value.trim();
        if (!isi) return;

        // Cek koneksi internet sebelum fetch
        if (!navigator.onLine) {
            showToast("Gagal mengirim pesan: Tidak ada koneksi internet. Silakan periksa jaringan Anda.", "error");
            return;
        }

        // Simpan pesan di variable untuk direstore jika gagal
        input.value = '';
        input.style.height = 'auto';

        try {
            const res = await fetch(SEND_URL, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': CSRF, 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ isi })
            });

            if (res.ok) {
                const data = await res.json();
                // Langsung render tanpa tunggu polling menggunakan ID asli dari server
                renderBubble({ id: data.id, isi, mine: true, time: data.time, read: false });
                scrollBottom();
                // Update lastId agar polling tidak menarik pesan ini lagi
                if (data.id > lastId) lastId = data.id;
            } else {
                // Restore isi pesan agar tidak hilang diketik ulang
                input.value = isi;
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 120) + 'px';

                if (res.status === 419) {
                    showToast("Sesi Anda telah berakhir (CSRF Token Kedaluwarsa). Silakan segarkan (refresh) halaman ini.", "error");
                } else if (res.status === 403) {
                    showToast("Pengiriman diblokir (403 Forbidden). Kemungkinan diblokir oleh Firewall atau izin akses ditolak.", "error");
                } else if (res.status === 500) {
                    showToast("Terjadi kesalahan pada Server (500 Internal Server Error). Silakan hubungi admin.", "error");
                } else {
                    showToast(`Gagal mengirim pesan (Kesalahan HTTP ${res.status}). Silakan coba lagi.`, "error");
                }
            }
        } catch (error) {
            // Restore isi pesan
            input.value = isi;
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';

            console.error("Gagal mengirim chat:", error);

            if (error.name === 'TypeError' || error.message.includes('Failed to fetch')) {
                showToast("Gagal terhubung ke server. Kemungkinan diblokir oleh Firewall, Adblocker, atau masalah jaringan/antarmuka browser.", "error");
            } else {
                showToast(`Gagal mengirim pesan: ${error.message || 'Kesalahan Jaringan'}`, "error");
            }
        }
    });

    // Koneksi & Polling Error Warning
    function showConnectionWarning(message, isCritical = false) {
        const warning = document.getElementById('connection-warning');
        const warningText = document.getElementById('connection-warning-text');
        const retryBtn = warning.querySelector('button');
        
        warningText.innerText = message;
        warning.classList.remove('hidden');
        
        if (isCritical) {
            warning.classList.remove('bg-amber-500');
            warning.classList.add('bg-red-500');
            retryBtn.innerText = "Segarkan Halaman";
            retryBtn.onclick = () => window.location.reload();
        } else {
            warning.classList.remove('bg-red-500');
            warning.classList.add('bg-amber-500');
            retryBtn.innerText = "Coba Sekarang";
            retryBtn.onclick = () => {
                pollMessages();
            };
        }
        if (window.lucide) lucide.createIcons();
    }

    function checkConnectionNow() {
        pollMessages();
    }

    function updateOnlineStatus() {
        const warning = document.getElementById('connection-warning');
        const warningText = document.getElementById('connection-warning-text');
        if (navigator.onLine) {
            warning.classList.add('hidden');
            // Jika kembali online, langsung sync pesan
            pollMessages();
        } else {
            showConnectionWarning("Koneksi internet terputus. Silakan periksa jaringan Anda.");
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    // Polling pesan baru tiap 5 detik
    async function pollMessages() {
        try {
            const res = await fetch(`${POLL_URL}?after=${lastId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            
            if (!res.ok) {
                if (res.status === 419) {
                    showConnectionWarning("Sesi Anda telah berakhir. Silakan segarkan (refresh) halaman.", true);
                } else if (res.status === 403) {
                    showConnectionWarning("Akses ditolak (403). Koneksi diblokir atau Anda tidak diizinkan.", true);
                } else {
                    pollFailures++;
                    if (pollFailures >= 3) {
                        showConnectionWarning(`Gagal sinkronisasi pesan (HTTP ${res.status}). Menghubungkan kembali...`);
                    }
                }
                return;
            }

            // Sukses
            pollFailures = 0;
            if (navigator.onLine) {
                document.getElementById('connection-warning').classList.add('hidden');
            }

            const msgs = await res.json();
            msgs.forEach(msg => {
                // Jangan render pesan yang sudah ada di DOM
                if (document.querySelector(`[data-msg-id="${msg.id}"]`)) return;
                if (msg.id > lastId) lastId = msg.id;
                renderBubble(msg);
            });
            if (msgs.length > 0) scrollBottom();
        } catch(e) { 
            pollFailures++;
            if (pollFailures >= 3) {
                showConnectionWarning("Gagal menghubungi server. Kemungkinan diblokir oleh Firewall atau jaringan bermasalah.");
            }
        }
    }

    setInterval(pollMessages, 5000);

    // Render session messages (success/error)
    @if(session('success'))
        showToast({!! json_encode(session('success')) !!}, "success");
    @endif
    @if(session('error'))
        showToast({!! json_encode(session('error')) !!}, "error");
    @endif
</script>
@endpush

</x-app-layout>
