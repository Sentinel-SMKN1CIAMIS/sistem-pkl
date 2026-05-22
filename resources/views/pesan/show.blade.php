<x-app-layout>
    <x-slot name="header">Pesan</x-slot>

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
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
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
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                </div>
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
                                    <i data-lucide="{{ $msg->dibaca_at ? 'check-check' : 'check' }}" class="w-3 h-3 {{ $msg->dibaca_at ? 'text-blue-200' : 'text-blue-300' }}"></i>
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
                    ${mine ? '<svg xmlns="http://www.w3.org/2000/svg" class="inline w-3 h-3 ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/><polyline points="20 6 9 17 4 12" transform="translate(4,0)"/></svg>' : ''}
                </p>
            </div>`;
        chatBottom.before(wrap);
        if (window.lucide) lucide.createIcons();
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Kirim pesan via AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const isi = input.value.trim();
        if (!isi) return;

        input.value = '';
        input.style.height = 'auto';

        const res = await fetch(SEND_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ isi })
        });

        if (res.ok) {
            const data = await res.json();
            // Langsung render tanpa tunggu polling
            renderBubble({ id: Date.now(), isi, mine: true, time: new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}), read: false });
            scrollBottom();
            // Set lastId setelah polling berikutnya akan sinkron sendiri
        }
    });

    // Polling pesan baru tiap 5 detik
    async function pollMessages() {
        try {
            const res = await fetch(`${POLL_URL}?after=${lastId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) return;
            const msgs = await res.json();
            msgs.forEach(msg => {
                // Jangan render pesan yang sudah ada di DOM
                if (document.querySelector(`[data-msg-id="${msg.id}"]`)) return;
                if (msg.id > lastId) lastId = msg.id;
                renderBubble(msg);
            });
            if (msgs.length > 0) scrollBottom();
        } catch(e) { /* silent */ }
    }

    setInterval(pollMessages, 5000);
</script>
@endpush

</x-app-layout>
