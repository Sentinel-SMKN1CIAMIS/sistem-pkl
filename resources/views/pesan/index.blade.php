<x-app-layout>
    <x-slot name="header">Pesan</x-slot>

    <div class="flex flex-col md:flex-row gap-0 h-[calc(100vh-12rem)] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">

        {{-- Panel Kiri: Daftar Kontak --}}
        <div class="w-full md:w-80 flex-shrink-0 border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col">
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
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors group">
                        {{-- Avatar --}}
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
                        <p>Belum ada kontak yang tersedia.</p>
                        <p class="text-xs mt-1">Kontak muncul otomatis berdasarkan data pemetaan siswa.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Panel Kanan: Placeholder saat tidak ada chat aktif --}}
        <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/50">
            <div class="text-center text-slate-400 dark:text-slate-500">
                <i data-lucide="message-circle" class="w-16 h-16 mx-auto mb-4 opacity-30"></i>
                <p class="font-semibold text-lg">Pilih percakapan</p>
                <p class="text-sm mt-1">Klik salah satu kontak di sebelah kiri untuk mulai mengobrol.</p>
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

    @if(session('success'))
        <div class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

</x-app-layout>
