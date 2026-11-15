<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mode Pemeliharaan - {{ config('app.name', 'Sistem Informasi PKL') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-900 text-slate-100 flex flex-col justify-between selection:bg-amber-500 selection:text-white relative overflow-x-hidden">
    <!-- Ambient Background Glows -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Header Navigation Bar -->
    <header class="relative z-10 px-6 py-5 max-w-6xl mx-auto w-full flex items-center justify-between">
        <div class="flex items-center gap-3">
            @php
                $logoUrl = \App\Models\KonfigurasiSistem::where('key', 'app_logo_url')->first()?->value;
                $appName = \App\Models\KonfigurasiSistem::where('key', 'app_name')->first()?->value ?? config('app.name', 'MAS-PKL');
            @endphp
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="w-8 h-8 rounded-lg object-contain">
            @else
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center font-black text-sm">
                    PKL
                </div>
            @endif
            <span class="font-bold text-sm tracking-tight text-white">{{ $appName }}</span>
        </div>

        <div class="flex items-center gap-2">
            @auth
                <span class="text-xs text-slate-400 hidden sm:inline">Masuk sebagai: <strong class="text-slate-200">{{ auth()->user()->name }}</strong></span>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 underline underline-offset-4 flex items-center gap-1.5 transition-colors">
                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                    Login Staf / Admin
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Maintenance Content -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-xl w-full text-center space-y-6">
            <!-- Animated Icon Card -->
            <div class="relative inline-flex items-center justify-center">
                <div class="absolute inset-0 rounded-3xl bg-amber-500/25 blur-xl animate-pulse"></div>
                <div class="relative p-6 rounded-3xl bg-slate-800/80 border border-amber-500/40 shadow-2xl backdrop-blur-xl">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                        <i data-lucide="wrench" class="w-9 h-9"></i>
                    </div>
                </div>
            </div>

            <!-- Title & Status -->
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-400 text-xs font-black uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    Pemeliharaan Sistem Sedang Berlangsung
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                    Sistem Sedang Dalam Perawatan
                </h1>
            </div>

            <!-- Custom Message Card -->
            <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/60 shadow-xl backdrop-blur-md text-left">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400 shrink-0 mt-0.5">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400">Pemberitahuan Administrator:</h4>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                            {{ $summary['message'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Context / Action Section -->
            @auth
                @php
                    $isAllowed = \App\Services\MaintenanceService::isRoleAllowed(auth()->user()->role);
                    $roleLabel = \App\Services\MaintenanceService::ALL_ROLES[auth()->user()->role]['label'] ?? ucfirst(auth()->user()->role);
                @endphp

                @if($isAllowed)
                    <div class="p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs text-left flex items-start gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5"></i>
                        <div class="flex-1 space-y-1">
                            <p class="font-bold text-white">Akses Diizinkan!</p>
                            <p class="text-[11px] text-emerald-200/90 leading-relaxed">
                                Akun Anda terdaftar sebagai <strong>{{ $roleLabel }}</strong> dan telah diberikan izin akses oleh Super Admin selama masa pemeliharaan ini.
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold text-xs transition-all shadow-md shadow-emerald-500/20">
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    Masuk ke Dashboard Sistem
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-xs text-left flex items-start gap-3">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-red-400 shrink-0 mt-0.5"></i>
                        <div class="flex-1 space-y-1">
                            <p class="font-bold text-white">Akses Dibatasi Sementara</p>
                            <p class="text-[11px] text-red-200/90 leading-relaxed">
                                Anda login sebagai <strong>{{ $roleLabel }}</strong>. Peran ini sedang dibatasi sementara waktu agar proses pemeliharaan database dan server berjalan lancar.
                            </p>
                            <div class="pt-2 flex items-center gap-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold text-xs transition-colors cursor-pointer">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all cursor-pointer">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        Login Khusus Staf & Admin
                    </a>
                    <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 text-xs font-semibold transition-all cursor-pointer">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Muat Ulang Halaman
                    </button>
                </div>
            @endauth
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-6 py-4 border-t border-slate-800 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} SMKN 1 Ciamis &bull; Sistem Informasi PKL &bull; Seluruh hak cipta dilindungi.</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

