<x-app-layout>
    <x-slot name="header">Edit Pembimbing Sekolah</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.pembimbing_sekolah.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('pokja.pembimbing_sekolah.update', $pembimbing_sekolah) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Akun (Read Only / Limit Edit) -->
                <div class="glass-card p-6 md:col-span-1 border border-blue-500/10">
                    <h3 class="text-lg font-semibold text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Akses Login
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-2">Username</label>
                            <input type="text" value="{{ $pembimbing_sekolah->user->username }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 font-mono italic">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-2">Email</label>
                            <input type="text" value="{{ $pembimbing_sekolah->user->email }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 italic">
                        </div>
                    </div>
                </div>

                <!-- Informasi Profil -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
                        Profil Pengajar
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pembimbing_sekolah->nama_lengkap) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200">
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-medium text-slate-300 mb-2">NIP</label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $pembimbing_sekolah->nip) }}"
                                   class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 font-mono">
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-300 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pembimbing_sekolah->no_hp) }}"
                                   class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Penugasan -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5 text-purple-400"></i>
                        Penugasan Jurusan
                    </h3>
                    <div>
                        <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-300 mb-2">Jurusan / Konsentrasi</label>
                        <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                            @foreach($concentrations as $item)
                                <option value="{{ $item->id }}" {{ (old('konsentrasi_keahlian_id', $pembimbing_sekolah->konsentrasi_keahlian_id) == $item->id) ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Update Data Pembimbing
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
