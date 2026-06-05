<x-app-layout>
    <x-slot name="header">Detail Grup Pokja: {{ $group->name }}</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.pokja-groups.index') }}" class="text-blue-400 hover:underline text-sm flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.pokja-groups.edit', $group) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Edit
            </a>
            <form action="{{ route('admin.pokja-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('Hapus grup ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Grup -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Informasi Grup</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400">Nama Grup</p>
                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $group->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400">Status</p>
                    @if($group->is_active)
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aktif</span>
                    @else
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">Nonaktif</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400">Deskripsi</p>
                    <p class="text-slate-900 dark:text-slate-100">{{ $group->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400">Dibuat</p>
                    <p class="text-slate-900 dark:text-slate-100">{{ $group->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400">Diubah</p>
                    <p class="text-slate-900 dark:text-slate-100">{{ $group->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Statistik</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-blue-500/10 border border-blue-500/20">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Total Anggota</span>
                    <span class="text-2xl font-bold text-blue-400">{{ $group->users()->count() }}/4</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-500/10 border border-slate-500/20">
                    <p class="text-xs uppercase font-bold text-slate-600 dark:text-slate-400 mb-2">Kapasitas Grup</p>
                    <div class="w-full h-2 bg-slate-300 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: {{ ($group->users()->count() / 4) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Anggota -->
    <div class="glass-card p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Anggota Grup</h3>
            @if($group->users()->count() < 4)
                <button onclick="document.getElementById('addMemberForm').style.display = document.getElementById('addMemberForm').style.display === 'none' ? 'block' : 'none'" 
                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium text-xs transition-colors flex items-center gap-1">
                    <i data-lucide="plus" class="w-3 h-3"></i>
                    Tambah Anggota
                </button>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Tambah Anggota (Hidden) -->
        <div id="addMemberForm" class="mb-6 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hidden">
            <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-3 text-sm">Tambah Anggota Baru</h4>
            <form action="{{ route('admin.pokja-groups.add-member', $group) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="user_id" required class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($pokjaUsers as $user)
                        @if(!$group->hasMemberId($user->id))
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium text-xs transition-colors">
                    Tambah
                </button>
            </form>
        </div>

        <!-- Tabel Anggota -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50 text-xs uppercase font-bold text-slate-600 dark:text-slate-400 tracking-wider">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                    @forelse($group->users as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900 dark:text-slate-100">{{ $member->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $member->username }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $member->email }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($group->users()->count() > 2)
                                    <form action="{{ route('admin.pokja-groups.remove-member', $group) }}" method="POST" class="inline" onsubmit="return confirm('Hapus anggota ini dari grup?')">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Minimal anggota</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400 text-sm">
                                Grup belum memiliki anggota
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
