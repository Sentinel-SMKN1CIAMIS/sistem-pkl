@props([
    'kontakWithMeta',
    'activeUserId' => null,
    'mobileHidden' => false,
])

<div class="{{ $mobileHidden ? 'hidden md:flex' : 'flex' }} w-full md:w-80 flex-shrink-0 overflow-hidden border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex-col" style="min-height: 0; height: 100%;"
     x-data="{
        q: '',
        loading: false,
        contacts: [],
        messages: [],
        timer: null,
        searchUrl: '{{ route('pesan.search') }}',
        onInput() {
            clearTimeout(this.timer);
            const term = this.q.trim();
            if (!term) {
                this.contacts = [];
                this.messages = [];
                this.loading = false;
                return;
            }
            this.loading = true;
            this.timer = setTimeout(async () => {
                try {
                    const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(term), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.contacts = data.contacts || [];
                        this.messages = data.messages || [];
                    }
                } catch (e) {
                    // fail silently
                } finally {
                    this.loading = false;
                }
            }, 220);
        },
        clear() {
            this.q = '';
            this.contacts = [];
            this.messages = [];
            this.loading = false;
        },
        highlightText(text) {
            const safe = String(text || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/&quot;/g,'&quot;');
            const term = this.q.trim();
            if (!term) return safe;
            const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return safe.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark class=\'bg-amber-200 dark:bg-amber-500/40 text-slate-900 dark:text-white rounded px-0.5\'>$1</mark>');
        }
     }">

    {{-- Header Sidebar --}}
    <div class="px-4 pt-4 pb-3 border-b border-slate-200 dark:border-slate-700 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Percakapan</h2>
            @if(in_array(auth()->user()->role, ['pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin']))
                <button type="button" onclick="document.getElementById('broadcastModal').classList.remove('hidden')"
                        class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors cursor-pointer"
                        title="Broadcast ke semua kontak">
                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                </button>
            @endif
        </div>

        {{-- WhatsApp-style Search Input --}}
        <div class="relative">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text"
                   x-model="q"
                   @input="onInput()"
                   @keydown.escape="clear()"
                   placeholder="Cari nama, NIS, atau isi chat..."
                   aria-label="Cari kontak atau isi pesan"
                   class="w-full pl-9 pr-8 py-2 text-xs bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
            <button type="button"
                    x-show="q.length > 0"
                    x-cloak
                    @click="clear()"
                    aria-label="Hapus pencarian"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

    {{-- Search Results Panel (shown when q is not empty) --}}
    <div x-show="q.trim().length > 0" x-cloak class="flex-1 overflow-y-auto">
        <div x-show="loading" class="px-4 py-6 text-center text-xs text-slate-400">
            Mencari kontak &amp; pesan...
        </div>

        <template x-if="!loading && contacts.length === 0 && messages.length === 0">
            <div class="px-4 py-10 text-center text-xs text-slate-400">
                Tidak ada kontak atau pesan yang cocok dengan "<span class="font-semibold text-slate-600 dark:text-slate-300" x-text="q"></span>".
            </div>
        </template>

        {{-- Matching Contacts --}}
        <div x-show="!loading && contacts.length > 0">
            <div class="px-4 py-2 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                Kontak (<span x-text="contacts.length"></span>)
            </div>
            <template x-for="c in contacts" :key="'c-' + c.id">
                <a :href="c.url"
                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors">
                    <div class="w-9 h-9 flex-none rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs" x-text="c.initial"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-html="highlightText(c.name)"></p>
                        <p class="text-[11px] text-slate-400 truncate" x-html="highlightText(c.sublabel)"></p>
                    </div>
                </a>
            </template>
        </div>

        {{-- Matching Chat Messages --}}
        <div x-show="!loading && messages.length > 0">
            <div class="px-4 py-2 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                Pesan (<span x-text="messages.length"></span>)
            </div>
            <template x-for="m in messages" :key="'m-' + m.id">
                <a :href="m.url"
                   class="block px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="m.contact_name"></span>
                        <span class="text-[10px] text-slate-400 flex-shrink-0" x-text="m.time"></span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                        <span x-show="m.mine" class="font-semibold text-slate-600 dark:text-slate-300">Anda: </span>
                        <span x-html="highlightText(m.isi)"></span>
                    </p>
                </a>
            </template>
        </div>
    </div>

    {{-- Default Contact List (shown when q is empty) --}}
    <div x-show="q.trim().length === 0" class="flex-1 overflow-y-auto">
        @forelse($kontakWithMeta as $meta)
            <a href="{{ route('pesan.show', $meta->user) }}"
               class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors group {{ $activeUserId === $meta->user->id ? 'bg-blue-50 dark:bg-blue-500/10 border-l-2 border-l-blue-600' : '' }}">
                <div class="w-10 h-10 flex-none aspect-square rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
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
                                {{ $meta->last_msg->from_user_id === auth()->id() ? 'Anda: ' : '' }}
                                @if($meta->last_msg->is_broadcast)
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">[Broadcast]</span>
                                @endif
                                {{ $meta->last_msg->isi }}
                            @else
                                <span class="italic">Belum ada pesan</span>
                            @endif
                        </span>
                        @if($meta->unread > 0)
                            <span class="flex-shrink-0 bg-blue-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center">
                                {{ $meta->unread > 9 ? '9+' : $meta->unread }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center h-40 text-slate-400 dark:text-slate-500 text-sm text-center p-6">
                <i data-lucide="users" class="w-10 h-10 mb-3 opacity-40"></i>
                <p>Belum ada kontak yang tersedia.</p>
                <p class="text-xs mt-1">Kontak muncul otomatis berdasarkan data pemetaan siswa.</p>
            </div>
        @endforelse
    </div>
</div>
