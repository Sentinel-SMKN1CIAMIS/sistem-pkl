<x-app-layout>
    <x-slot name="header">Notifikasi</x-slot>

    <div class="max-w-2xl mx-auto space-y-4">
        @forelse($notifikasis as $item)
            <div class="glass-card p-6 border-l-4 {{ $item->is_read ? 'border-slate-700 opacity-60' : 'border-blue-500' }} transition-all">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-100 mb-1">{{ $item->judul }}</h3>
                        <p class="text-sm text-slate-400 mb-3">{{ $item->pesan }}</p>
                        <span class="text-[10px] text-slate-500 font-mono tracking-tighter uppercase">
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    @if(!$item->is_read)
                        <form action="{{ route('notifications.read', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg text-blue-400 transition-all shadow-sm" title="Tandai sudah baca">
                                <i data-lucide="check" class="w-4 h-4 text-emerald-400"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center text-slate-500 italic">
                Tidak ada notifikasi baru untuk Anda.
            </div>
        @endforelse

        <div class="mt-8">
            {{ $notifikasis->links() }}
        </div>
    </div>
</x-app-layout>
