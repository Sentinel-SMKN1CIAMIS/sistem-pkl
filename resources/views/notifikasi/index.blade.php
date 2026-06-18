<x-app-layout>
    <x-slot name="header">Notifikasi</x-slot>

    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Subheader & Stats -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800/60 shadow-xs">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Kotak Masuk Notifikasi</h3>
                @php
                    $unreadCount = \App\Models\Notifikasi::where('to_user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Anda memiliki <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $unreadCount }}</span> notifikasi yang belum dibaca.
                </p>
            </div>

            @if($notifikasis->count() > 0)
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read_all') }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold transition-all border border-blue-500/20 hover:border-blue-500/30 cursor-pointer">
                                <i data-lucide="check-check" class="w-4 h-4"></i> Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('notifications.clear_all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition-all border border-rose-500/20 hover:border-rose-500/30 cursor-pointer">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Bersihkan Semua
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Notification List Feed -->
        <div class="space-y-4">
            @forelse($notifikasis as $item)
                @php
                    // Dynamic styling based on notification content
                    $icon = 'bell';
                    $iconColor = 'text-blue-500 bg-blue-500/10 dark:bg-blue-500/20 border-blue-500/20';
                    
                    $titleLower = strtolower($item->judul);
                    if (str_contains($titleLower, 'setuju') || str_contains($titleLower, 'acc') || str_contains($titleLower, 'penerimaan')) {
                        $icon = 'check-circle';
                        $iconColor = 'text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/20 border-emerald-500/20';
                    } elseif (str_contains($titleLower, 'tolak') || str_contains($titleLower, 'batal')) {
                        $icon = 'x-circle';
                        $iconColor = 'text-rose-500 bg-rose-500/10 dark:bg-rose-500/20 border-rose-500/20';
                    } elseif (str_contains($titleLower, 'pembimbing') || str_contains($titleLower, 'pemetaan') || str_contains($titleLower, 'penempatan') || str_contains($titleLower, 'penugasan')) {
                        $icon = 'user-check';
                        $iconColor = 'text-indigo-500 bg-indigo-500/10 dark:bg-indigo-500/20 border-indigo-500/20';
                    }
                @endphp

                <!-- Outer Card (Serves as absolute container for the delete button underneath) -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 dark:border-slate-800/60 bg-white dark:bg-slate-900 group shadow-xs hover:shadow-md transition-shadow duration-200">
                    
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

                    <!-- Delete button panel (Revealed underneath on swipe/hover) -->
                    <form action="{{ route('notifications.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')" class="absolute right-0 top-0 bottom-0 z-10">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn h-full px-5 bg-rose-600 hover:bg-rose-500 text-white flex items-center justify-center transition-colors cursor-pointer" title="Hapus notifikasi">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </form>

                    <!-- Inner Card Content (Slides left on swipe/hover) -->
                    <div class="relative z-20 bg-white dark:bg-slate-900 p-5 border-l-4 {{ $item->is_read ? 'border-slate-200 dark:border-slate-700 opacity-70' : 'border-blue-500 shadow-sm shadow-blue-500/5' }} transition-transform duration-300 ease-out md:group-hover:-translate-x-16 cursor-pointer select-none"
                         onclick="handleNotificationClick(event, '{{ $item->id }}', '{{ $item->is_read ? 1 : 0 }}', '{{ $item->link }}')"
                         data-swipe-card="{{ $item->id }}">
                        
                        <div class="flex items-start gap-4">
                            <!-- Icon Circle -->
                            <div class="shrink-0 p-3 rounded-2xl border {{ $iconColor }}">
                                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                            </div>

                            <!-- Notification details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm sm:text-base leading-tight">
                                        {{ $item->judul }}
                                    </h4>
                                    @if(!$item->is_read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-blue-500 text-white animate-pulse">
                                            Baru
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3 leading-relaxed">
                                    {{ $item->pesan }}
                                </p>

                                <!-- Footer Details (Time and Link indicator) -->
                                <div class="flex items-center gap-4 text-xs font-semibold text-slate-500 dark:text-slate-400 flex-wrap">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        {{ $item->created_at->diffForHumans() }}
                                    </span>

                                    @if($item->link)
                                        <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                            Lihat Detail <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Slide indicator icon (Only visible on hover) -->
                            <div class="shrink-0 text-slate-300 dark:text-slate-700 md:group-hover:text-slate-400 transition-colors hidden sm:block">
                                <i data-lucide="chevrons-left" class="w-4 h-4 animate-pulse"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="glass-card p-16 text-center text-slate-500 dark:text-slate-400 border-dashed border-2 border-slate-200 dark:border-slate-800 rounded-3xl">
                    <div class="inline-flex p-4 rounded-full bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-600 mb-4">
                        <i data-lucide="bell-off" class="w-8 h-8"></i>
                    </div>
                    <p class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">Tidak Ada Notifikasi</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Kotak masuk Anda bersih! Kami akan memberi tahu Anda jika ada aktivitas atau penugasan baru.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifikasis->hasPages())
            <div class="mt-8">
                {{ $notifikasis->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Handle clicking anywhere on the notification card
        function handleNotificationClick(event, id, isRead, link) {
            // Prevent trigger if user clicks directly on the delete button or confirmation dialogs
            if (event.target.closest('.action-btn') || event.target.closest('form')) {
                return;
            }

            if (isRead === '1') {
                // Already read: Navigate to detail link directly if exists
                if (link) {
                    window.location.href = link;
                }
            } else {
                // Unread: Submit the form to mark as read (and redirect if redirect input is present)
                const form = document.getElementById('read-form-' + id);
                if (form) {
                    form.submit();
                } else if (link) {
                    window.location.href = link;
                }
            }
        }

        // Swipe Gestures for Mobile (Swipe Left to reveal Delete button)
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('[data-swipe-card]');
            
            cards.forEach(card => {
                let startX = 0;
                let currentX = 0;
                let isSwiping = false;
                const maxSwipe = -64; // Delete panel width is 64px (w-16)

                card.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                    isSwiping = true;
                    card.style.transition = 'none';
                }, { passive: true });

                card.addEventListener('touchmove', (e) => {
                    if (!isSwiping) return;
                    currentX = e.touches[0].clientX - startX;

                    // Only allow sliding left (negative values)
                    if (currentX > 0) currentX = 0;
                    if (currentX < maxSwipe) currentX = maxSwipe;

                    card.style.transform = `translateX(${currentX}px)`;
                }, { passive: true });

                card.addEventListener('touchend', () => {
                    isSwiping = false;
                    card.style.transition = 'transform 0.3s ease-out';
                    
                    // If swiped more than half of the target width, open it. Otherwise close it.
                    if (currentX < maxSwipe / 2) {
                        card.style.transform = `translateX(${maxSwipe}px)`;
                    } else {
                        card.style.transform = 'translateX(0px)';
                    }
                    currentX = 0;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
