<x-app-layout>
    <x-slot name="header">Kelola Pengguna Sistem</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-400">Total terdaftar: <span class="text-blue-400 font-bold">{{ $users->total() }}</span> akun.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/30 border-b border-slate-700/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm italic">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-800/10 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-slate-200 font-bold not-italic font-mono">{{ $user->username }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleClasses = [
                                        'super_admin' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'pokja' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'siswa' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'pembimbing_sekolah' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'pembimbing_dudi' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-black border {{ $roleClasses[$user->role] ?? 'bg-slate-500/10 text-slate-400' }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-blue-400 transition-colors">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="p-2 text-slate-400 hover:text-red-400 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
