<x-guest-layout>
    <style>
        /* Custom Colors & Layout Variables */
        :root {
            --primary-blue: #2563eb;
            --primary-blue-dark: #1d4ed8;
            --primary-blue-light: #eff6ff;
            --slate-bg: #f8fafc;
            --dark-bg: #090d16;
            --card-light: #ffffff;
            --card-dark: #1e293b;
            --border-light: #e2e8f0;
            --border-dark: #334155;
        }

        /* Full Screen Wrapper */
        .login-wrapper {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            flex-direction: row;
            background: var(--slate-bg);
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
        }

        .dark .login-wrapper {
            background: var(--dark-bg);
        }

        /* Left Sidebar (Desktop) */
        .login-sidebar {
            width: 420px;
            height: 100%;
            background-color: #0f172a;
            background-image: linear-gradient(to bottom right, #1e3a8a, #0f172a);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            flex-shrink: 0;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Glowing Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.45;
            z-index: 0;
            pointer-events: none;
        }

        .orb-1 {
            width: 350px;
            height: 350px;
            background: #2563eb;
            top: -100px;
            left: -100px;
            animation: moveOrb1 18s ease-in-out infinite alternate;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: #4f46e5;
            bottom: -150px;
            right: -150px;
            animation: moveOrb2 22s ease-in-out infinite alternate;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: #3b82f6;
            top: 30%;
            left: -50px;
            animation: moveOrb3 20s ease-in-out infinite alternate;
        }

        @keyframes moveOrb1 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(120px, 150px) scale(1.2); }
            66% { transform: translate(180px, 40px) scale(0.8); }
            100% { transform: translate(80px, 180px) scale(1.1); }
        }

        @keyframes moveOrb2 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-150px, -120px) scale(1.1); }
            66% { transform: translate(-250px, 80px) scale(1.3); }
            100% { transform: translate(-100px, -200px) scale(0.9); }
        }

        @keyframes moveOrb3 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(100px, -150px) scale(1.4); }
            66% { transform: translate(150px, 100px) scale(0.7); }
            100% { transform: translate(30px, -100px) scale(1.2); }
        }

        /* Right Content Area */
        .login-main {
            flex: 1;
            height: 100%;
            background: var(--slate-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            overflow-y: auto;
            position: relative;
        }

        .dark .login-main {
            background: var(--dark-bg);
        }

        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-light);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 40px;
            position: relative;
            z-index: 20;
            opacity: 0;
            animation: cardEntrance3D 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .dark .login-card {
            background: var(--card-dark);
            border-color: var(--border-dark);
            box-shadow: none;
        }

        @keyframes cardEntrance3D {
            0% {
                opacity: 0;
                transform: perspective(1000px) translateY(40px) rotateX(-8deg) scale(0.96);
            }
            100% {
                opacity: 1;
                transform: perspective(1000px) translateY(0) rotateX(0deg) scale(1);
            }
        }

        /* Staggered Items Animation */
        .animate-stagger-item {
            opacity: 0;
            animation: fadeUpStagger 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }

        @keyframes fadeUpStagger {
            0% {
                opacity: 0;
                transform: translateY(12px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Brand Features & Icon box */
        .brand-features {
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255, 255, 255, 0.75);
            font-size: 13.5px;
            font-weight: 500;
        }

        .feature-icon-box {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }

        /* Bulletproof Input Padding Overrides (Fixes icon overlaps) */
        .login-input {
            padding-left: 44px !important;
            padding-right: 16px !important;
        }

        .login-input-password {
            padding-left: 44px !important;
            padding-right: 44px !important;
        }

        /* Wave SVG for Mobile (Perfect wave with no gaps) */
        .wave-container {
            display: none;
        }

        /* Responsive Styles */
        @media (max-width: 1023px) {
            .login-wrapper {
                flex-direction: column;
            }

            .login-sidebar {
                width: 100%;
                height: 35vh;
                padding: 24px;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                justify-content: center;
                align-items: center;
            }

            .login-main {
                width: 100%;
                height: 65vh;
                padding: 24px;
            }

            .wave-container {
                display: block;
                position: absolute;
                bottom: -1px; /* Overlaps content background by 1px to prevent hairline pixel gaps */
                left: 0;
                right: 0;
                width: 100%;
                height: 48px;
                overflow: hidden;
                pointer-events: none;
                z-index: 10;
            }

            .wave-svg {
                display: block; /* Eliminates the default inline baseline margin gap */
                width: 100%;
                height: 100%;
                fill: var(--slate-bg);
            }

            .dark .wave-svg {
                fill: var(--dark-bg);
            }
        }
    </style>

    <!-- Main Full-Screen Layout Wrapper -->
    <div class="login-wrapper">
        
        <!-- ==================== LEFT BRAND PANEL (Desktop) / TOP PANEL (Mobile) ==================== -->
        <div class="login-sidebar">
            <!-- Background Orbs -->
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>

            <!-- Top Header (Desktop only) -->
            <div class="hidden lg:flex items-center gap-3 relative z-10 animate-stagger-item delay-100">
                <div class="p-2 bg-white rounded-xl shadow-md">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                </div>
                <div>
                    <h2 class="text-lg font-black text-white leading-none">MAS-PKL</h2>
                    <span class="text-[10px] text-slate-400 tracking-wider font-semibold uppercase">SMKN 1 CIAMIS</span>
                </div>
            </div>

            <!-- Center Block (Desktop only) -->
            <div class="hidden lg:block relative z-10 my-auto animate-stagger-item delay-200">
                <h1 class="text-white font-extrabold text-3xl tracking-wider mb-2">SISTEM PKL</h1>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">Monitoring administrasi, jurnal harian, dan absensi siswa SMK Negeri 1 Ciamis terintegrasi.</p>
                
                <!-- Brand Features -->
                <div class="brand-features">
                    <div class="brand-feature">
                        <span class="feature-icon-box">
                            <i data-lucide="shield-check" class="w-4.5 h-4.5 text-white"></i>
                        </span>
                        <span>Akses Aman Terenkripsi</span>
                    </div>
                    <div class="brand-feature">
                        <span class="feature-icon-box">
                            <i data-lucide="activity" class="w-4.5 h-4.5 text-white"></i>
                        </span>
                        <span>Monitoring Harian Real-Time</span>
                    </div>
                    <div class="brand-feature">
                        <span class="feature-icon-box">
                            <i data-lucide="briefcase" class="w-4.5 h-4.5 text-white"></i>
                        </span>
                        <span>Evaluasi Bimbingan DUDI</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Branding Header (exactly matching Foto 1!) -->
            <div class="lg:hidden flex flex-col items-center justify-center h-full w-full relative z-10">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-14 h-14 object-contain mb-2 filter drop-shadow">
                <h1 class="text-2xl font-black text-white tracking-wide">MAS-PKL</h1>
                <p class="text-xs text-blue-100 font-medium opacity-90">Monitoring & Administrasi Siswa PKL</p>
                
                <!-- Bottom SVG curve for Mobile -->
                <div class="wave-container">
                    <svg class="wave-svg" viewBox="0 0 1440 74" preserveAspectRatio="none">
                        <path d="M0,32L120,42.7C240,53,480,75,720,74.7C960,75,1200,53,1320,42.7L1440,32L1440,74L1320,74C1200,74,960,74,720,74C480,74,240,74,120,74L0,74Z"></path>
                    </svg>
                </div>
            </div>

            <!-- Bottom Block (Desktop only) -->
            <div class="hidden lg:block relative z-10 text-slate-500 text-xs animate-stagger-item delay-500">
                Protected admin area — MAS-PKL © {{ date('Y') }}
            </div>
        </div>

        <!-- ==================== RIGHT PANEL (Desktop Form Area) / BOTTOM PANEL (Mobile Form Area) ==================== -->
        <div class="login-main">
            
            <!-- Login Card -->
            <div class="login-card">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1.5 animate-stagger-item delay-100">Selamat datang kembali</h2>
                <p class="subtitle text-sm text-slate-500 dark:text-slate-400 mb-8 animate-stagger-item delay-200">Silakan masuk menggunakan akun MAS-PKL Anda</p>

                @if ($errors->any())
                    <div class="alert alert-danger bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl p-3.5 mb-6 text-xs font-semibold animate-stagger-item delay-250">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5 animate-stagger-item delay-300" x-data="{ username: '{{ old('username') }}', password: '', showPassword: false }">
                    @csrf

                    <!-- Username -->
                    <div class="space-y-2">
                        <label for="username" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Username / NIS / NIP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </div>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus x-model="username"
                                   class="login-input w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 transition-all shadow-sm"
                                   placeholder="Username / NIS / NIP">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Password</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
                            </div>
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required x-model="password"
                                   class="login-input-password w-full pl-11 pr-11 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 transition-all shadow-sm"
                                   placeholder="Password">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                                <i data-lucide="eye" x-show="!showPassword" class="w-4.5 h-4.5"></i>
                                <i data-lucide="eye-off" x-show="showPassword" class="w-4.5 h-4.5" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me / Lupa Password -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4.5 h-4.5 rounded border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950 transition-all cursor-pointer">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors">Lupa Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <x-button class="w-full py-3 text-sm font-bold shadow-md shadow-blue-500/10 dark:shadow-none" icon="arrow-right-circle" x-bind:error-text="!username && !password ? 'isi username/NIS/NIP dan password' : (!username ? 'Isi username/NIS/NIP' : 'Isi Password')">
                        Masuk Ke Akun
                    </x-button>
                </form>

                <div class="login-footer text-center mt-6 text-xs text-slate-400 dark:text-slate-500 animate-stagger-item delay-500">
                    Protected admin area — MAS-PKL © {{ date('Y') }}
                </div>
            </div>

        </div>

    </div>

    <!-- Script to ensure Lucide icons load correctly -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</x-guest-layout>
