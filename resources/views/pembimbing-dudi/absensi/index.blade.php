<x-app-layout>
    <x-slot name="header">Kehadiran Siswa PKL</x-slot>

    <div class="mb-6">
        <p class="text-slate-400">Pantau dan cek tanda tangan serta lokasi kehadiran siswa hari ini.</p>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/30 border-b border-slate-700/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Jam Datang</th>
                        <th class="px-6 py-4">Jam Pulang</th>
                        <th class="px-6 py-4">Tanda Tangan</th>
                        <th class="px-6 py-4 text-right">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($absensis as $row)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-slate-100 font-medium block">{{ $row->siswa->nama_lengkap }}</span>
                                <span class="text-[10px] text-slate-500">{{ $row->siswa->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($row->ttd_siswa_path)
                                    <button class="p-1.5 rounded-lg bg-slate-100/10 hover:bg-slate-100/20 border border-slate-100/10 group relative" 
                                            onclick="showSignature('{{ asset('storage/' . $row->ttd_siswa_path) }}')">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-300"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
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
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 italic">
                                Belum ada data kehadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>

    <!-- Modal for Signature -->
    <div id="sig-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" onclick="closeModal()">
        <div class="glass-card max-w-lg w-full p-2 relative" onclick="event.stopPropagation()">
            <img id="sig-img" src="" alt="Digital Signature" class="w-full bg-white rounded-xl">
            <button onclick="closeModal()" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shadow-lg border border-red-500">
                 <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function showSignature(url) {
            document.getElementById('sig-img').src = url;
            document.getElementById('sig-modal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('sig-modal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
