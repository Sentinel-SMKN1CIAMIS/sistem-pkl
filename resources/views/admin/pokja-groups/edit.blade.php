<x-app-layout>
    <x-slot name="header">Edit Grup Pokja: {{ $group->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card p-8">
            <a href="{{ route('admin.pokja-groups.show', $group) }}" class="text-blue-400 hover:underline text-sm flex items-center gap-1 mb-6">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Detail
            </a>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <p class="font-semibold mb-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        Validasi Gagal
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.pokja-groups.update', $group) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Grup</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}" required
                           class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Misal: Pokja RPL 2026">
                    @error('name')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Deskripsi (Opsional)</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsi ringkas tentang grup ini">{{ old('description', $group->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
                        Anggota Grup (2-4 Orang)
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Pilih 2 hingga 4 pengguna dengan role Pokja</p>
                    
                    <div class="space-y-2">
                        @forelse($pokjaUsers as $user)
                            <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <input type="checkbox" name="members[]" value="{{ $user->id }}" 
                                       {{ $group->hasMemberId($user->id) || in_array($user->id, old('members', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-500 rounded focus:ring-blue-500">
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->username }}</p>
                                </div>
                            </label>
                        @empty
                            <div class="p-4 text-center text-slate-500 dark:text-slate-400 text-sm">
                                <i data-lucide="alert-circle" class="w-5 h-5 inline-block mr-2"></i>
                                Tidak ada pengguna Pokja yang tersedia
                            </div>
                        @endforelse
                    </div>
                    @error('members')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-500 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-slate-800 dark:text-slate-200">Aktifkan grup</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-6 border-t border-slate-200/50 dark:border-slate-700/50">
                    <a href="{{ route('admin.pokja-groups.show', $group) }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium text-sm transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
