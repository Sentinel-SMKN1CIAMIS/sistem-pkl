<x-app-layout>
    <x-slot name="header">Pesan</x-slot>

    <style>
        .pesan-shell {
            height: calc(100vh - 14.5rem);
            height: calc(100dvh - 14.5rem);
        }
        @media (min-width: 768px) {
            .pesan-shell {
                height: calc(100vh - 12rem);
            }
        }
    </style>

    <div class="pesan-shell flex flex-col md:flex-row gap-0 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">

        {{-- Panel Kiri: Daftar Kontak + Pencarian WhatsApp-style --}}
        @include('pesan.partials.sidebar', ['kontakWithMeta' => $kontakWithMeta, 'activeUserId' => null, 'mobileHidden' => false])

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
