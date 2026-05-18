<x-guest-layout>
<style>
:root {
    --slate-bg: #f8fafc;
    --dark-bg: #090d16;
    --card-light: #ffffff;
    --card-dark: #1e293b;
    --border-light: #e2e8f0;
    --border-dark: #334155;
}

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
.dark .login-wrapper { background: var(--dark-bg); }

/* ===== LEFT SIDEBAR (Desktop) ===== */
.login-sidebar {
    width: 420px;
    height: 100%;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 40%, #0f172a 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 48px;
    flex-shrink: 0;
    border-right: 1px solid rgba(255,255,255,0.05);
}

/* Glowing Orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
    z-index: 0;
    pointer-events: none;
}
.orb-1 { width:350px; height:350px; background:#2563eb; top:-100px; left:-100px; animation: orbMove1 18s ease-in-out infinite alternate; }
.orb-2 { width:400px; height:400px; background:#4f46e5; bottom:-150px; right:-150px; animation: orbMove2 22s ease-in-out infinite alternate; }
.orb-3 { width:300px; height:300px; background:#3b82f6; top:30%; left:-50px; animation: orbMove3 20s ease-in-out infinite alternate; }

@keyframes orbMove1 { 0%{transform:translate(0,0) scale(1)} 50%{transform:translate(120px,150px) scale(1.2)} 100%{transform:translate(80px,180px) scale(1.1)} }
@keyframes orbMove2 { 0%{transform:translate(0,0) scale(1)} 50%{transform:translate(-150px,-120px) scale(1.1)} 100%{transform:translate(-100px,-200px) scale(0.9)} }
@keyframes orbMove3 { 0%{transform:translate(0,0) scale(1)} 50%{transform:translate(100px,-150px) scale(1.4)} 100%{transform:translate(30px,-100px) scale(1.2)} }

/* Brand features */
.brand-features { margin-top: 40px; display:flex; flex-direction:column; gap:16px; }
.brand-feature { display:flex; align-items:center; gap:14px; color:rgba(255,255,255,0.75); font-size:13.5px; font-weight:500; }
.feature-icon-box {
    width:36px; height:36px;
    background:rgba(255,255,255,0.1);
    border:1px solid rgba(255,255,255,0.15);
    border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}

/* ===== RIGHT MAIN (Desktop) ===== */
.login-main {
    flex: 1;
    height: 100%;
    background: var(--slate-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px;
    overflow-y: auto;
}
.dark .login-main { background: var(--dark-bg); }

/* Login Card */
.login-card {
    width: 100%;
    max-width: 420px;
    background: var(--card-light);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06);
    padding: 40px;
    position: relative;
    z-index: 20;
    opacity: 0;
    animation: cardIn 0.8s cubic-bezier(0.2,0.8,0.2,1) forwards;
}
.dark .login-card { background:var(--card-dark); border-color:var(--border-dark); box-shadow:none; }

@keyframes cardIn {
    from { opacity:0; transform:perspective(1000px) translateY(40px) rotateX(-8deg) scale(0.96); }
    to   { opacity:1; transform:perspective(1000px) translateY(0) rotateX(0deg) scale(1); }
}

/* Staggered animation */
.si { opacity:0; animation: fadeUp 0.6s cubic-bezier(0.2,0.8,0.2,1) forwards; }
.d1{animation-delay:.1s} .d2{animation-delay:.2s} .d3{animation-delay:.3s}
.d4{animation-delay:.4s} .d5{animation-delay:.5s} .d6{animation-delay:.6s}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

/* Input overrides */
.login-input  { padding-left:44px !important; padding-right:16px !important; }
.login-input-pw { padding-left:44px !important; padding-right:44px !important; }

/* ===== MOBILE LAYOUT ===== */
@media (max-width: 1023px) {
    .login-wrapper { flex-direction: column; overflow-y: auto; }

    /* Top section: full blue bg with topographic pattern */
    .login-sidebar {
        width: 100%;
        height: auto;
        min-height: 280px;
        padding: 0;
        border-right: none;
        border-bottom: none;
        justify-content: flex-start;
        align-items: stretch;
        position: relative;
    }

    /* Mobile content area inside sidebar */
    .mobile-top-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding-top: 48px;
        padding-bottom: 80px; /* space for wave overlap */
        position: relative;
        z-index: 5;
        text-align: center;
    }

    /* Topographic contour lines pattern overlay */
    .topo-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        opacity: 0.15;
    }

    /* The organic blob wave at bottom of mobile header */
    .mobile-wave-wrap {
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        z-index: 10;
        line-height: 0;
    }
    .mobile-wave-wrap svg {
        display: block;
        width: 100%;
        height: 90px;
    }

    /* Main area on mobile: only bottom half */
    .login-main {
        flex: 1;
        height: auto;
        min-height: 0;
        padding: 32px 20px 40px;
        background: var(--slate-bg);
        align-items: flex-start;
        overflow-y: visible;
    }

    .login-card {
        max-width: 100%;
        padding: 28px 24px;
        border-radius: 20px;
        box-shadow: 0 4px 30px rgba(0,0,0,0.08);
        margin-top: 0;
    }

    /* Hide desktop-only blocks on mobile */
    .desktop-only { display: none !important; }
}

/* Hide mobile-only blocks on desktop */
@media (min-width: 1024px) {
    .mobile-only { display: none !important; }
}
</style>

<div class="login-wrapper">

    <!-- ===== LEFT / TOP : BRAND PANEL ===== -->
    <div class="login-sidebar">
        <!-- Orbs (desktop only, mobile uses solid gradient) -->
        <div class="orb orb-1 desktop-only"></div>
        <div class="orb orb-2 desktop-only"></div>
        <div class="orb orb-3 desktop-only"></div>

        <!-- MOBILE: topographic SVG pattern overlay -->
        <svg class="topo-overlay mobile-only" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            <!-- Topographic contour lines, like the reference design's organic squiggly lines -->
            <path d="M-20,150 C40,120 80,180 140,140 C200,100 240,170 300,130 C360,90 390,160 440,140" fill="none" stroke="white" stroke-width="1.5"/>
            <path d="M-20,170 C30,140 90,200 150,160 C210,120 250,185 310,150 C370,115 400,175 450,155" fill="none" stroke="white" stroke-width="1.5"/>
            <path d="M-20,190 C50,155 100,215 160,178 C220,140 260,198 320,165 C380,132 405,192 455,172" fill="none" stroke="white" stroke-width="1.2"/>
            <path d="M-20,210 C60,175 110,228 170,195 C230,162 270,212 330,182 C390,152 410,208 460,188" fill="none" stroke="white" stroke-width="1.2"/>
            <path d="M-20,120 C55,90 100,155 160,115 C220,75 270,148 330,108 C390,68 420,135 450,110" fill="none" stroke="white" stroke-width="1.5"/>
            <path d="M-20,95 C45,65 95,130 155,92 C215,54 265,125 325,88 C385,50 415,118 460,90" fill="none" stroke="white" stroke-width="1.2"/>
            <path d="M-20,70 C35,42 90,105 148,70 C206,35 260,100 320,65 C380,30 410,95 455,68" fill="none" stroke="white" stroke-width="1"/>
            <path d="M-20,230 C70,198 118,240 180,212 C240,182 285,228 345,200 C400,172 420,225 460,205" fill="none" stroke="white" stroke-width="1"/>
            <!-- Sparkle-like star accents -->
            <path d="M320,80 L322,74 L324,80 L330,82 L324,84 L322,90 L320,84 L314,82 Z" fill="white" opacity="0.6"/>
            <path d="M60,110 L62,104 L64,110 L70,112 L64,114 L62,120 L60,114 L54,112 Z" fill="white" opacity="0.5"/>
            <path d="M200,60 L201,56 L202,60 L206,61 L202,62 L201,66 L200,62 L196,61 Z" fill="white" opacity="0.5"/>
        </svg>

        <!-- DESKTOP: Top header with logo -->
        <div class="desktop-only flex items-center gap-3 relative z-10 si d1">
            <div class="p-2.5 bg-white rounded-xl shadow-lg">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-9 h-9 object-contain">
            </div>
            <div>
                <h2 class="text-lg font-black text-white leading-none">MAS-PKL</h2>
                <span class="text-[10px] text-slate-400 tracking-wider font-semibold uppercase">SMKN 1 Ciamis</span>
            </div>
        </div>

        <!-- DESKTOP: Center block -->
        <div class="desktop-only relative z-10 my-auto si d2">
            <h1 class="text-white font-extrabold text-3xl tracking-wide mb-3">SISTEM PKL</h1>
            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">Monitoring administrasi, jurnal harian, dan absensi siswa SMK Negeri 1 Ciamis terintegrasi.</p>
            <div class="brand-features">
                <div class="brand-feature">
                    <span class="feature-icon-box"><i data-lucide="shield-check" class="w-4 h-4 text-white"></i></span>
                    <span>Akses Aman Terenkripsi</span>
                </div>
                <div class="brand-feature">
                    <span class="feature-icon-box"><i data-lucide="activity" class="w-4 h-4 text-white"></i></span>
                    <span>Monitoring Harian Real-Time</span>
                </div>
                <div class="brand-feature">
                    <span class="feature-icon-box"><i data-lucide="briefcase" class="w-4 h-4 text-white"></i></span>
                    <span>Evaluasi Bimbingan DUDI</span>
                </div>
            </div>
        </div>

        <!-- DESKTOP: Footer -->
        <div class="desktop-only relative z-10 text-slate-500 text-xs si d5">
            Protected admin area — MAS-PKL © {{ date('Y') }}
        </div>

        <!-- MOBILE: Top content (no logo, text only) -->
        <div class="mobile-only mobile-top-content">
            <h1 class="text-3xl font-black text-white tracking-wide mb-1">MAS-PKL</h1>
            <p class="text-sm text-blue-100 font-medium opacity-90">Monitoring & Administrasi Siswa PKL</p>
        </div>

        <!-- MOBILE: Organic blob wave (matches reference photo motif) -->
        <div class="mobile-only mobile-wave-wrap">
            <svg viewBox="0 0 390 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <!-- Organic blob wave: dips deep on left, rises on right, matches reference -->
                <path d="M0,55 C30,10 80,70 140,35 C200,0 250,55 320,30 C355,17 375,45 390,35 L390,90 L0,90 Z" fill="white"/>
            </svg>
        </div>
    </div>

    <!-- ===== RIGHT / BOTTOM: FORM PANEL ===== -->
    <div class="login-main">
        <div class="login-card">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1.5 si d1">Selamat datang kembali</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 si d2">Silakan masuk menggunakan akun MAS-PKL Anda</p>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl p-3.5 mb-6 text-xs font-semibold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5 si d3"
                  x-data="{ username: '{{ old('username') }}', password: '', showPassword: false }">
                @csrf

                <!-- Username -->
                <div class="space-y-2">
                    <label for="username" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Username / NIS / NIP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus x-model="username"
                               class="login-input w-full py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 transition-all shadow-sm"
                               placeholder="Username / NIS / NIP">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required x-model="password"
                               class="login-input-pw w-full py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 transition-all shadow-sm"
                               placeholder="Password">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <i data-lucide="eye" x-show="!showPassword" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="showPassword" class="w-4 h-4" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-200 dark:border-slate-800 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ingat Saya</span>
                    </label>
                    <a href="#" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors">Lupa Password?</a>
                </div>

                <!-- Submit -->
                <x-button class="w-full py-3 text-sm font-bold shadow-md shadow-blue-500/10 dark:shadow-none" icon="arrow-right-circle"
                          x-bind:error-text="!username && !password ? 'Isi username dan password' : (!username ? 'Isi username/NIS/NIP' : 'Isi Password')">
                    Masuk Ke Akun
                </x-button>
            </form>

            <div class="text-center mt-6 text-xs text-slate-400 dark:text-slate-500 si d5">
                Protected admin area — MAS-PKL © {{ date('Y') }}
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</x-guest-layout>
