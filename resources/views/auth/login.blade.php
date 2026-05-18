<x-guest-layout>
    <style>
        /* Premium Background Orbs Animations */
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
            background: #2563eb; /* Blue 600 */
            top: -100px;
            left: -100px;
            animation: moveOrb1 18s ease-in-out infinite alternate;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: #4f46e5; /* Indigo 600 */
            bottom: -150px;
            right: -150px;
            animation: moveOrb2 22s ease-in-out infinite alternate;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: #3b82f6; /* Blue 500 */
            top: 30%;
            left: -50px;
            animation: moveOrb3 20s ease-in-out infinite alternate;
        }

        @keyframes moveOrb1 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(150px, 180px) scale(1.2); }
            66% { transform: translate(220px, 40px) scale(0.8); }
            100% { transform: translate(100px, 220px) scale(1.1); }
        }

        @keyframes moveOrb2 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-180px, -150px) scale(1.1); }
            66% { transform: translate(-300px, 80px) scale(1.3); }
            100% { transform: translate(-120px, -280px) scale(0.9); }
        }

        @keyframes moveOrb3 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(120px, -180px) scale(1.4); }
            66% { transform: translate(200px, 120px) scale(0.7); }
            100% { transform: translate(40px, -120px) scale(1.2); }
        }

        /* 3D Staggered Entrance Animations */
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

        .animate-card {
            animation: cardEntrance3D 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .animate-stagger-item {
            opacity: 0;
            animation: fadeUpStagger 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        /* Set delayed animation offsets */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
    </style>

    <!-- Main Responsive Layout Container -->
    <div class="fixed inset-0 z-30 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950 font-sans overflow-hidden">
        
        <!-- ==================== LEFT BRAND SIDEBAR (Desktop) / TOP PANEL (Mobile) ==================== -->
        <div class="relative w-full md:w-[420px] h-[35vh] md:h-full bg-slate-900 flex flex-col justify-between p-6 md:p-12 overflow-hidden shrink-0 border-r border-slate-800/40">
            <!-- Background Orbs -->
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>

            <!-- Brand Block (Desktop) -->
            <div class="hidden md:block brand-block relative z-10 text-center my-auto animate-stagger-item delay-100">
                <div class="w-[72px] h-[72px] bg-white rounded-[18px] flex items-center justify-center mx-auto mb-6 shadow-xl shadow-black/30 p-2">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-white font-extrabold text-3xl tracking-wider mb-2">MAS-PKL</h1>
                <p class="text-xs tracking-[0.15em] text-slate-400 font-semibold uppercase">Monitoring & Administrasi</p>
                
                <!-- Brand Features (exactly like Swift in Foto 2 but branded for MAS-PKL) -->
                <div class="brand-features mt-12 flex flex-col gap-4 text-left">
                    <div class="brand-feature flex items-center gap-3.5 text-slate-300/80 text-[13px] font-medium animate-stagger-item delay-400">
                        <i class="w-9 h-9 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center text-white/80 shrink-0 shadow-inner">
                            <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                        </i>
                        Akses Aman Terenkripsi
                    </div>
                    <div class="brand-feature flex items-center gap-3.5 text-slate-300/80 text-[13px] font-medium animate-stagger-item delay-500">
                        <i class="w-9 h-9 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center text-white/80 shrink-0 shadow-inner">
                            <i data-lucide="activity" class="w-4.5 h-4.5"></i>
                        </i>
                        Monitoring Harian Real-Time
                    </div>
                    <div class="brand-feature flex items-center gap-3.5 text-slate-300/80 text-[13px] font-medium animate-stagger-item delay-600">
                        <i class="w-9 h-9 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center text-white/80 shrink-0 shadow-inner">
                            <i data-lucide="briefcase" class="w-4.5 h-4.5"></i>
                        </i>
                        Evaluasi Kemitraan DUDI
                    </div>
                </div>
            </div>

            <!-- Mobile-only Wave Backdrop & Branding Header (exactly matching Foto 1!) -->
            <div class="md:hidden flex flex-col items-center justify-center h-full w-full relative z-10 pt-4">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-14 h-14 object-contain mb-2 filter drop-shadow">
                <h1 class="text-2xl font-black text-white tracking-wide">MAS-PKL</h1>
                <p class="text-xs text-blue-100 font-medium opacity-90">Monitoring & Administrasi Siswa PKL</p>
                
                <!-- Bottom SVG organic wave curve for Mobile, exactly matching Foto 1's soft curved divide -->
                <div class="absolute bottom-0 left-0 right-0 w-full h-8 overflow-hidden pointer-events-none">
                    <svg class="absolute bottom-0 left-0 w-full h-full text-slate-50 dark:text-slate-950 fill-current" viewBox="0 0 1440 74" preserveAspectRatio="none">
                        <path d="M0,32L120,42.7C240,53,480,75,720,74.7C960,75,1200,53,1320,42.7L1440,32L1440,74L1320,74C1200,74,960,74,720,74C480,74,240,74,120,74L0,74Z"></path>
                    </svg>
                </div>
            </div>

            <!-- Footer (Desktop only) -->
            <div class="hidden md:block relative z-10 text-slate-500 text-xs mt-auto animate-stagger-item delay-700">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 inline mr-1 text-blue-500"></i> Protected admin area — MAS-PKL © 2026
            </div>
        </div>

        <!-- ==================== RIGHT PANEL (Desktop Form Area) / BOTTOM PANEL (Mobile Form Area) ==================== -->
        <div class="w-full md:flex-1 bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-6 md:p-12 overflow-y-auto relative h-[65vh] md:h-full">
            
            <!-- Login Card (exactly Swift-style but in Blue theme) -->
            <div class="w-full max-w-[420px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none p-8 md:p-10 animate-card relative z-10">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1.5 animate-stagger-item delay-200">Selamat datang kembali</h2>
                <p class="subtitle text-sm text-slate-500 dark:text-slate-400 mb-8 animate-stagger-item delay-300">Silakan masuk menggunakan akun MAS-PKL Anda</p>

                @if ($errors->any())
                    <div class="alert alert-danger bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl p-3.5 mb-6 text-xs font-semibold animate-stagger-item delay-350">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5 animate-stagger-item delay-400" x-data="{ username: '{{ old('username') }}', password: '', showPassword: false }">
                    @csrf

                    <!-- Username -->
                    <div class="space-y-2">
                        <label for="username" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Username / NIS / NIP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </div>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus x-model="username"
                                   class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 transition-all shadow-sm"
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
                                   class="w-full pl-11 pr-11 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 transition-all shadow-sm"
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

                <div class="login-footer text-center mt-6 text-xs text-slate-400 dark:text-slate-500 animate-stagger-item delay-600">
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
