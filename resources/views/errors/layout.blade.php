<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'MAS-PKL') }}</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <link href="https://unpkg.com/lucide@latest/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS CDN (Bulletproof fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        dark: '#0f172a',
                        darker: '#020617',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-image: radial-gradient(circle at top right, rgba(59, 130, 246, 0.08) 0%, transparent 40%),
                              radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
        }
        .dark body {
            background-image: radial-gradient(circle at top right, rgba(59, 130, 246, 0.12) 0%, transparent 40%),
                              radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.08) 0%, transparent 40%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>

    <!-- Theme Switcher Script -->
    <script>
        if (localStorage.theme === 'dark' || (localStorage.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col items-center relative overflow-x-hidden p-6 selection:bg-blue-500/30 selection:text-blue-900 dark:selection:text-blue-100">

    <!-- Theme Toggle -->
    <div class="absolute top-6 right-6 z-50">
        <button onclick="toggleTheme()" 
                class="text-slate-700 dark:text-white p-2.5 rounded-full hover:bg-slate-200/50 dark:hover:bg-slate-800/50 transition-colors duration-200 glass-card shadow-sm">
            <i id="theme-icon-sun" class="w-5 h-5 hidden" data-lucide="sun"></i>
            <i id="theme-icon-moon" class="w-5 h-5 hidden" data-lucide="moon"></i>
        </button>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-lg relative z-10 text-center animate-fade-in my-auto py-6">
        <div class="glass-card rounded-2xl p-5 sm:p-10 shadow-xl border border-slate-200/50 dark:border-slate-800/50 transition-all duration-300">
            <!-- Icon -->
            <div class="inline-flex p-4 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 mb-6">
                @yield('icon')
            </div>

            <!-- Error Code -->
            @hasSection('code')
            <h1 class="text-7xl sm:text-8xl font-black tracking-wider bg-clip-text text-transparent bg-linear-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 mb-4 select-none">
                @yield('code')
            </h1>
            @endif

            <!-- Title -->
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3 tracking-tight">
                @yield('title')
            </h2>

            <!-- Message -->
            <p class="text-slate-600 dark:text-slate-400 mb-8 leading-relaxed text-sm sm:text-base">
                @yield('message')
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                <button onclick="history.back()" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-medium text-sm hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors duration-200 flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </button>
                <a href="{{ url('/') }}" 
                   class="w-full sm:w-auto px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm transition-colors duration-200 shadow-md shadow-blue-500/10 flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Kembali ke Beranda
                </a>
            </div>

            @yield('game')
        </div>
    </div>

    <!-- Footer -->
    <div class="w-full text-center text-xs text-slate-400 dark:text-slate-600 select-none pb-6">
        &copy; {{ date('Y') }} {{ config('app.name', 'MAS-PKL') }}. All rights reserved.
    </div>

    <!-- Theme Toggle and Icons Init Script -->
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Manage theme switcher UI
        const sunIcon = document.getElementById('theme-icon-sun');
        const moonIcon = document.getElementById('theme-icon-moon');

        function updateThemeUI() {
            if (document.documentElement.classList.contains('dark')) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateThemeUI();
        }

        // Initialize UI
        updateThemeUI();
    </script>
</body>
</html>
