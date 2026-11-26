<x-app-layout>
    <x-slot name="header">Broadcast Pengumuman Resmi</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="glass-card p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                    <i data-lucide="megaphone" class="w-4 h-4 text-blue-600"></i>
                    Buat Pengumuman Baru
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Pengumuman langsung muncul di dashboard target dan dikirim sebagai notifikasi ke perangkat siswa/guru.
                </p>

                <form action="{{ route('pokja.pengumuman.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Judul Pengumuman</label>
                        <input type="text" name="judul" required value="{{ old('judul') }}" placeholder="Contoh: Batas Akhir Pengumpulan Laporan PKL"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Target Penerima</label>
                        <select name="target_role" required class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                            <option value="siswa">Seluruh Siswa PKL</option>
                            <option value="pembimbing_sekolah">Guru Pembimbing Sekolah</option>
                            <option value="semua">Semua (Siswa &amp; Guru Pembimbing)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Filter Program Keahlian (Opsional)</label>
                        <select name="program_keahlian_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                            <option value="">Semua Jurusan / Program</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Isi Pengumuman</label>
                        <textarea name="isi" rows="4" required minlength="10" placeholder="Tulis informasi resmi yang ingin disampaikan..."
                                  class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">{{ old('isi') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Berlaku Sampai (Opsional)</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200">
                    </div>
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-colors cursor-pointer">
                        Publikasikan &amp; Kirim Broadcast
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="glass-card p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4">Daftar Pengumuman</h3>
                <div class="space-y-3">
                    @forelse($pengumumans as $p)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $p->judul }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->is_active ? 'bg-emerald-600 text-white' : 'bg-slate-400 text-white' }}">
                                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 uppercase">
                                            {{ $p->target_role }}{{ $p->programKeahlian ? ' • ' . $p->programKeahlian->kode : ' • Semua Jurusan' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">{{ $p->isi }}</p>
                                    <p class="text-[10px] text-slate-400 mt-2">
                                        Oleh {{ $p->creator?->name ?? 'Pokja' }} &bull; {{ $p->created_at?->isoFormat('D MMM YYYY HH:mm') }}
                                        @if($p->expires_at) &bull; Berakhir {{ $p->expires_at->isoFormat('D MMM YYYY HH:mm') }} @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form action="{{ route('pokja.pengumuman.toggle', $p) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-700 dark:text-slate-200 cursor-pointer">
                                            {{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('pokja.pengumuman.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-[11px] font-bold cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-xs text-slate-400 italic">Belum ada pengumuman yang dibuat.</div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $pengumumans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
