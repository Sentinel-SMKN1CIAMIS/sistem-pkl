<x-app-layout>
    <x-slot name="header">sistem log aktivitas</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-400">Menampilkan riwayat aktivitas sistem terbaru.</p>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/30 border-b border-slate-700/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 whitespace-nowrap">IP & Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-800/10 transition-colors group">
                            <td class="px-6 py-4 text-slate-300 font-mono text-xs whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/30">
                                        <i data-lucide="user" class="w-3 h-3 text-blue-400"></i>
                                    </div>
                                    <span class="text-slate-200 font-medium">{{ $log->user->username ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $actionClasses = [
                                        'LOGIN' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'LOGOUT' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'CREATED' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'UPDATED' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'DELETED' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-black border {{ $actionClasses[$log->action] ?? 'bg-slate-500/10 text-slate-400' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 italic font-medium leading-relaxed">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-slate-500 font-mono text-[10px]">{{ $log->ip_address }}</span>
                                    <span class="text-slate-400 font-medium text-[11px] whitespace-nowrap overflow-hidden text-ellipsis max-w-[150px]" title="{{ $log->location }}">
                                        {{ $log->location }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-12 h-12 text-slate-700"></i>
                                    <p class="text-slate-500 font-medium">Belum ada riwayat aktivitas yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
