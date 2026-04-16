<x-app-layout>
    <x-slot name="header">Konfigurasi Sistem</x-slot>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="mb-6">
            <p class="text-slate-600 dark:text-slate-400">Kelola pengaturan dasar aplikasi seperti Nama Sekolah, Tahun Ajaran, dan logo.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="glass-card p-8 shadow-2xl shadow-blue-500/10">
            <form action="{{ route('admin.config.update') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $defaultConfigs = [
                            ['key' => 'app_name', 'label' => 'Nama Aplikasi', 'placeholder' => 'Contoh: Sistem Informasi PKL SMKN 1 Ciamis'],
                            ['key' => 'tahun_ajaran', 'label' => 'Tahun Ajaran Aktif', 'placeholder' => 'Contoh: 2023/2024'],
                            ['key' => 'app_logo_url', 'label' => 'URL Logo Aplikasi', 'placeholder' => 'https://path-to-your-logo.png'],
                            ['key' => 'kontak_admin', 'label' => 'Kontak Pokja (WA)', 'placeholder' => '0812XXXXXXXX'],
                        ];
                    @endphp

                    @foreach($defaultConfigs as $item)
                        @php
                            $value = $configs->where('key', $item['key'])->first()?->value;
                        @endphp
                        <div>
                            <label for="{{ $item['key'] }}" class="block text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">{{ $item['label'] }}</label>
                            <input type="text" name="{{ $item['key'] }}" id="{{ $item['key'] }}" value="{{ $value }}" placeholder="{{ $item['placeholder'] }}"
                                   class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    @endforeach
                </div>

                <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-black rounded-xl shadow-lg shadow-blue-500/25 transition-all text-xs uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Semua Pengaturan
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-blue-500/5 border border-blue-500/10">
                <h4 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-1">Backup Data</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">Lakukan backup database secara rutin untuk menghindari kehilangan data penting PKL.</p>
                <button class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:text-white flex items-center gap-1 transition-colors">
                    <i data-lucide="database" class="w-3.5 h-3.5"></i>
                    Download SQL Backup
                </button>
            </div>
            <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/10">
                <h4 class="text-xs font-black text-amber-400 uppercase tracking-widest mb-1">Wipe Data</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">Bersihkan data transaksi (absensi & jurnal) untuk persiapan tahun ajaran baru.</p>
                <button class="text-xs font-bold text-red-400 hover:text-red-300 flex items-center gap-1 transition-colors">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    Bersihkan Data Transaksi
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
