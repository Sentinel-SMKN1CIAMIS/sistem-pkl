<x-app-layout>
    <x-slot name="header">Kelola Program Keahlian</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-400">Daftar semua program keahlian yang terdaftar di sistem.</p>
        <a href="{{ route('admin.program_keahlian.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Tambah Program
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-700/50 bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Program</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($programs as $program)
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md bg-slate-800 border border-slate-700 text-sm font-mono text-blue-400">
                                    {{ $program->kode }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-200">
                                {{ $program->nama }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.program_keahlian.edit', $program) }}" class="p-2 text-slate-400 hover:text-blue-400 transition-colors" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.program_keahlian.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 italic">
                                Belum ada data program keahlian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
