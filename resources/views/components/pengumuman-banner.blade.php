@php
    $bannerUser = auth()->user();
    $activePengumumans = $bannerUser ? \App\Models\Pengumuman::activeForUser($bannerUser) : collect();
@endphp

@if($activePengumumans->isNotEmpty())
    <div class="mb-6 space-y-3">
        @foreach($activePengumumans as $pg)
            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-600 text-white">Pengumuman</span>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $pg->judul }}</h4>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ $pg->isi }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
