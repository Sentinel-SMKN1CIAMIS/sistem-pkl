<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Workspace - MAS-PKL SMKN 1 Ciamis</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="min-h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-['Outfit',sans-serif] flex flex-col items-center justify-center p-4 sm:p-8">

    <div class="w-full max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-8 sm:mb-10">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm mb-4">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-9 h-9 object-contain">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Selamat Datang, {{ $user->pembimbingSekolah?->nama_lengkap ?? $user->name }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-xl mx-auto">
                Akun Anda memiliki akses ganda. Pilih ruang kerja (workspace) yang ingin Anda buka saat ini. Anda tetap dapat berpindah kapan saja melalui menu sidebar.
            </p>
        </div>

        {{-- 2 Workspace Cards: Left = Pokja (Shared), Right = Pembimbing Sekolah (Personal) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Left Card: Workspace Pokja --}}
            <form action="{{ route('workspace.switch') }}" method="POST" class="flex">
                @csrf
                <input type="hidden" name="workspace" value="pokja">
                <button type="submit"
                        class="w-full text-left group p-7 rounded-3xl bg-white dark:bg-slate-900 border-2 {{ $activeWorkspace === 'pokja' ? 'border-blue-600' : 'border-slate-200 dark:border-slate-800 hover:border-blue-500' }} shadow-sm hover:shadow-lg transition-all flex flex-col justify-between cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-13 h-13 rounded-2xl bg-blue-600 text-white flex items-center justify-center">
                                <i data-lucide="briefcase" class="w-6 h-6"></i>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                Shared Workspace
                            </span>
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            Workspace Pokja
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Ruang kerja bersama Tim Pokja PKL. Kelola data siswa, DUDI, pengajuan, pemetaan pembimbing, kuota, dan laporan secara real-time bersama anggota Pokja lainnya.
                        </p>

                        <ul class="mt-5 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-500 shrink-0"></i>
                                Data tersinkronisasi untuk seluruh tim Pokja
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-500 shrink-0"></i>
                                Notifikasi pop-up real-time saat rekan Pokja mengubah data
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-500 shrink-0"></i>
                                Aktivitas tercatat atas nama akun Anda sendiri
                            </li>
                        </ul>
                    </div>

                    <div class="mt-7 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-sm font-bold text-blue-600 dark:text-blue-400">
                        <span>Masuk ke Workspace Pokja</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </button>
            </form>

            {{-- Right Card: Workspace Pembimbing Sekolah --}}
            <form action="{{ route('workspace.switch') }}" method="POST" class="flex">
                @csrf
                <input type="hidden" name="workspace" value="pembimbing_sekolah">
                <button type="submit"
                        class="w-full text-left group p-7 rounded-3xl bg-white dark:bg-slate-900 border-2 {{ $activeWorkspace === 'pembimbing_sekolah' ? 'border-emerald-600' : 'border-slate-200 dark:border-slate-800 hover:border-emerald-500' }} shadow-sm hover:shadow-lg transition-all flex flex-col justify-between cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-13 h-13 rounded-2xl bg-emerald-600 text-white flex items-center justify-center">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                Workspace Pribadi
                            </span>
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            Pembimbing Sekolah
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Dashboard pribadi Anda sebagai Guru Pembimbing. Pantau siswa bimbingan, validasi jurnal harian, setujui izin/absensi, dan evaluasi laporan PKL.
                        </p>

                        <ul class="mt-5 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                Khusus daftar siswa bimbingan Anda sendiri
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                Validasi jurnal harian &amp; persetujuan absensi
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                Evaluasi laporan &amp; komunikasi langsung dengan siswa
                            </li>
                        </ul>
                    </div>

                    <div class="mt-7 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-sm font-bold text-emerald-600 dark:text-emerald-400">
                        <span>Masuk ke Pembimbing Sekolah</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </button>
            </form>
        </div>

        {{-- Footer logout --}}
        <div class="mt-8 text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-red-500 transition-colors cursor-pointer">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</body>
</html>
