<x-app-layout>
    <x-slot name="header">Kehadiran Siswa PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Pantau dan cek tanda tangan serta lokasi kehadiran siswa hari ini.</p>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Jam Datang</th>
                        <th class="px-6 py-4 whitespace-nowrap">Jam Pulang</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tanda Tangan</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-900 dark:text-slate-100 font-medium block">{{ $row->siswa->nama_lengkap }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $row->siswa->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                @if($row->status === 'hadir' && $row->alasan)
                                    <br><span class="text-[10px] text-orange-500 font-medium" title="{{ $row->alasan }}"><i data-lucide="info" class="w-3 h-3 inline"></i> {{ Str::limit($row->alasan, 20) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->ttd_siswa_path)
                                    <button class="p-1.5 rounded-lg bg-slate-100/10 hover:bg-slate-100/20 border border-slate-100/10 group relative" 
                                            onclick="showSignature('{{ asset('storage/' . $row->ttd_siswa_path) }}')">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-700 dark:text-slate-300"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($row->latitude)
                                    <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500/20 transition-all font-bold text-xs">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                        Map
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data kehadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>

    <!-- Modal for Signature -->
    <div id="sig-modal" class="fixed inset-0 z-100 hidden items-center justify-center p-4 bg-slate-900/60" onclick="closeModal()">
        <div class="glass-card max-w-lg w-full p-2 relative" onclick="event.stopPropagation()">
            <img id="sig-img" src="" alt="Digital Signature" class="w-full bg-white rounded-xl">
            <button onclick="closeModal()" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-red-600 text-slate-900 dark:text-white flex items-center justify-center shadow-lg border border-red-500">
                 <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function showSignature(url) {
            document.getElementById('sig-img').src = url;
            const modal = document.getElementById('sig-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('sig-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    @endpush
</x-app-layout>
