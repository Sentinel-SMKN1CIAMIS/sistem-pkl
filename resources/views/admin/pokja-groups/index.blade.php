<x-app-layout>
    <x-slot name="header">Manajemen Grup Pokja</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Total grup: <span class="text-blue-400 font-bold">{{ $groups->total() }}</span> grup.</p>
        <a href="{{ route('admin.pokja-groups.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Grup Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($groups->isEmpty())
        <div class="glass-card p-12 text-center">
            <i data-lucide="inbox" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
            <p class="text-slate-600 dark:text-slate-400">Belum ada grup Pokja. <a href="{{ route('admin.pokja-groups.create') }}" class="text-blue-400 hover:underline">Buat yang pertama</a>.</p>
        </div>
    @else
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 text-xs uppercase font-bold tracking-wider">
                            <th class="px-6 py-4 whitespace-nowrap">Nama Grup</th>
                            <th class="px-6 py-4 whitespace-nowrap">Anggota</th>
                            <th class="px-6 py-4 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap">Dibuat</th>
                            <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50 text-sm font-normal">
                        @foreach($groups as $group)
                            <tr class="hover:bg-white dark:bg-slate-800/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $group->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        {{ $group->users()->count() }} / 4
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($group->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-black border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-black border bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ $group->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div x-data="{ open: false }" class="relative flex justify-end" x-on:click.away="open = false">
                                        <button x-on:click="open = !open" class="p-1.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 mt-8 w-40 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                             style="display: none;">
                                            <a href="{{ route('admin.pokja-groups.show', $group) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 text-blue-500"></i>
                                                Lihat
                                            </a>
                                            <a href="{{ route('admin.pokja-groups.edit', $group) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-500"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.pokja-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Hapus grup ini? Anggota akan kehilangan akses.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($groups->hasPages())
                <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
