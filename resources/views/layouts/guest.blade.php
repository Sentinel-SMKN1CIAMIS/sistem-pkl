<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MAS-PKL - Manajemen & Absensi Sistem PKL SMKN 1 Ciamis')</title>
    <meta name="description" content="MAS-PKL (Manajemen & Absensi Sistem Praktik Kerja Lapangan) SMKN 1 Ciamis. Sistem informasi pengelolaan kegiatan PKL, presensi siswa, jurnal harian, dan monitoring pembimbing.">
    <meta name="keywords" content="MAS-PKL, PKL SMKN 1 Ciamis, Sistem Informasi PKL, SMKN 1 Ciamis, Prakerin Ciamis, Jurnal PKL Online, Absensi PKL">
    <meta name="author" content="SMKN 1 Ciamis">
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="google6a810bf91d75edb1">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'MAS-PKL - Manajemen & Absensi Sistem PKL SMKN 1 Ciamis')">
    <meta property="og:description" content="Sistem Informasi Praktik Kerja Lapangan (PKL) SMKN 1 Ciamis. Presensi siswa, jurnal harian, dan pengelolaan laporan PKL.">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:site_name" content="MAS-PKL SMKN 1 Ciamis">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'MAS-PKL - Manajemen & Absensi Sistem PKL SMKN 1 Ciamis')">
    <meta name="twitter:description" content="Sistem Informasi Praktik Kerja Lapangan (PKL) SMKN 1 Ciamis. Presensi siswa, jurnal harian, dan pengelolaan laporan PKL.">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "EducationalOrganization",
      "name": "SMKN 1 Ciamis",
      "url": "https://pkl.smkn1ciamis.id",
      "logo": "https://pkl.smkn1ciamis.id/logo.png",
      "sameAs": [
        "https://smkn1ciamis.sch.id"
      ],
      "description": "Sistem Manajemen dan Absensi Praktik Kerja Lapangan (MAS-PKL) SMKN 1 Ciamis."
    }
    </script>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme Switcher Script (prevent FOUC) -->
    <script>
        if (localStorage.theme === 'dark' || (localStorage.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col items-center justify-center relative overflow-hidden" x-data="{}">
    
    <!-- Theme Toggle -->
    <div class="absolute top-6 right-6 z-50">
        <div x-data="{
            theme: localStorage.theme || 'light',
            open: false,
            setTheme(val) {
                this.theme = val;
                localStorage.theme = val;
                if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                this.open = false;
            }
        }" class="relative">
            <button @click="open = !open" @click.away="open = false" 
                    class="text-slate-700 dark:text-white relative p-2.5 rounded-full hover:bg-slate-200/50 dark:hover:bg-slate-800/50 transition-colors block glass-card">
                <i x-show="theme === 'light'" data-lucide="sun" class="w-5 h-5" x-cloak></i>
                <i x-show="theme === 'dark'" data-lucide="moon" class="w-5 h-5" x-cloak></i>
                <i x-show="theme === 'system'" data-lucide="monitor" class="w-5 h-5" x-cloak></i>
            </button>
            <div x-show="open"
                 x-transition.opacity.duration.200ms
                 class="absolute right-0 mt-2 w-36 glass-card border border-slate-200/50 dark:border-slate-700/50 py-2 rounded-xl text-sm font-medium z-50 text-slate-700 dark:text-slate-200" x-cloak>
                <button @click="setTheme('light')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="sun" class="w-4 h-4"></i> Light
                </button>
                <button @click="setTheme('dark')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="moon" class="w-4 h-4"></i> Dark
                </button>
                <button @click="setTheme('system')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                    <i data-lucide="monitor" class="w-4 h-4"></i> System
                </button>
            </div>
        </div>
    </div>
    <!-- Background colors -->
    <div class="absolute inset-0 bg-slate-50 dark:bg-slate-950 pointer-events-none"></div>
    
    <div class="w-full relative z-10">
        {{ $slot }}
    </div>
    
    <!-- Lucide Icons mapping -->
    <!-- SweetAlert2 Global Script overrides -->
    <script>
        // Prevent browser caching on back button (BFCache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Custom global override for standard window.alert
        window.alert = function(message) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Informasi',
                text: message,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                buttonsStyling: false,
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                customClass: {
                    confirmButton: 'px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-blue-500/25 text-sm focus:outline-none cursor-pointer',
                    popup: 'rounded-2xl border border-slate-200/80 dark:border-slate-800/80 font-sans shadow-2xl',
                    title: 'text-lg font-bold text-slate-900 dark:text-slate-100',
                    htmlContainer: 'text-sm font-medium leading-relaxed'
                }
            });
        };

        // Professional toast wrapper function
        window.showToast = function(message, type = 'success') {
            const isDark = document.documentElement.classList.contains('dark');
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            toast.fire({
                icon: type,
                title: message,
                customClass: {
                    popup: 'rounded-xl border border-slate-200/80 dark:border-slate-800/80 shadow-lg font-sans text-sm'
                }
            });
        };

        // Automatically convert native confirm() attributes on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', () => {
            // Forms with inline confirm in onsubmit
            document.querySelectorAll('form').forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match) {
                        const message = match[1];
                        form.removeAttribute('onsubmit');
                        form.addEventListener('submit', function(e) {
                            if (form.dataset.swalConfirmed === 'true') {
                                return;
                            }
                            e.preventDefault();
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                buttonsStyling: false,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f1f5f9' : '#1e293b',
                                customClass: {
                                    confirmButton: 'px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-red-500/25 text-sm focus:outline-none cursor-pointer mr-3',
                                    cancelButton: 'px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none cursor-pointer',
                                    popup: 'rounded-2xl border border-slate-200/80 dark:border-slate-800/80 font-sans shadow-2xl',
                                    htmlContainer: 'text-sm font-medium leading-relaxed'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.dataset.swalConfirmed = 'true';
                                    form.submit();
                                }
                            });
                        });
                    }
                }
            });

            // Buttons/links with inline confirm in onclick
            document.querySelectorAll('[onclick]').forEach(el => {
                const onclickAttr = el.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('confirm(')) {
                    const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match) {
                        const message = match[1];
                        el.removeAttribute('onclick');
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                buttonsStyling: false,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f1f5f9' : '#1e293b',
                                customClass: {
                                    confirmButton: 'px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-red-500/25 text-sm focus:outline-none cursor-pointer mr-3',
                                    cancelButton: 'px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none cursor-pointer',
                                    popup: 'rounded-2xl border border-slate-200/80 dark:border-slate-800/80 font-sans shadow-2xl',
                                    htmlContainer: 'text-sm font-medium leading-relaxed'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const form = el.closest('form');
                                    if (form) {
                                        form.dataset.swalConfirmed = 'true';
                                        form.submit();
                                    } else if (el.tagName === 'A') {
                                        window.location.href = el.href;
                                    }
                                }
                            });
                        });
                    }
                }
            });
        });
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ session('success') }}", 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ session('error') }}", 'error');
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast("{{ $errors->first() }}", 'error');
            });
        </script>
    @endif
</body>
</html>
