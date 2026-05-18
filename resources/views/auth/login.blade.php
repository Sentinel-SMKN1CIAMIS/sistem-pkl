<x-guest-layout>
<style>
* { box-sizing: border-box; }

/* ====== DESKTOP LOGIN ====== */
.dl {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; flex-direction: row;
    font-family: 'Outfit', sans-serif;
    background: #fff; overflow: hidden;
}
.dark .dl { background: #0f172a; }

.dl-left {
    width: 42%; height: 100%;
    background: linear-gradient(160deg, #2563eb 0%, #1e3a8a 55%, #0f172a 100%);
    position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 56px 40px; text-align: center; flex-shrink: 0;
}

/* Animated glowing orbs (from Swift reference) */
.dl-orb {
    position: absolute; border-radius: 50%;
    filter: blur(80px); pointer-events: none; z-index: 0;
}
.dl-orb-a {
    width: 350px; height: 350px;
    background: #3b82f6; opacity: 0.5;
    top: -100px; left: -100px;
    animation: orbA 18s ease-in-out infinite alternate;
}
.dl-orb-b {
    width: 400px; height: 400px;
    background: #6366f1; opacity: 0.5;
    bottom: -150px; right: -150px;
    animation: orbB 22s ease-in-out infinite alternate;
}
.dl-orb-c {
    width: 300px; height: 300px;
    background: #818cf8; opacity: 0.3;
    top: 30%; left: -50px;
    animation: orbC 20s ease-in-out infinite alternate;
}
@keyframes orbA {
    0%   { transform: translate(0, 0) scale(1); }
    33%  { transform: translate(200px, 250px) scale(1.2); }
    66%  { transform: translate(350px, 50px) scale(0.8); }
    100% { transform: translate(150px, 350px) scale(1.1); }
}
@keyframes orbB {
    0%   { transform: translate(0, 0) scale(1); }
    33%  { transform: translate(-250px, -200px) scale(1.1); }
    66%  { transform: translate(-400px, 100px) scale(1.3); }
    100% { transform: translate(-150px, -400px) scale(0.9); }
}
@keyframes orbC {
    0%   { transform: translate(0, 0) scale(1); }
    33%  { transform: translate(150px, -250px) scale(1.4); }
    66%  { transform: translate(250px, 150px) scale(0.7); }
    100% { transform: translate(50px, -150px) scale(1.2); }
}

/* Desktop cloud divider: 3-layer, big smooth bumps like Spacer */
.dl-cloud {
    position: absolute; right: -1px; top: 0; bottom: 0;
    width: 160px; z-index: 10;
}
.dl-cloud svg { width: 100%; height: 100%; }

/* Logo box */
.dl-logo {
    width: 72px; height: 72px;
    background: white; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    padding: 10px; position: relative; z-index: 1;
}

/* Brand content */
.dl-brand { position: relative; z-index: 1; }
.dl-feats { margin-top: 36px; display: flex; flex-direction: column; gap: 16px; text-align: left; }
.dl-feat { display: flex; align-items: center; gap: 14px; color: rgba(255,255,255,0.75); font-size: 13.5px; font-weight: 500; }
.dl-feat-icon {
    width: 36px; height: 36px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}

/* Right panel */
.dl-right {
    flex: 1; height: 100%; background: #fff; overflow-y: auto;
    display: flex; align-items: center; justify-content: center; padding: 56px 64px;
}
.dark .dl-right { background: #1e293b; }

/* ====== MOBILE LOGIN ====== */
.ml {
    position: fixed; inset: 0; z-index: 9999;
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(170deg, #1d4ed8 0%, #1e3a8a 45%, #0f172a 100%);
    overflow-y: auto; overflow-x: hidden;
    display: flex; flex-direction: column;
}
.ml-header {
    width: 100%; padding: 52px 24px 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; flex-shrink: 0; min-height: 200px;
}
/* Mobile cloud: horizontal multi-layer */
.ml-cloud {
    width: 100%; flex-shrink: 0;
    line-height: 0; margin-top: -10px;
    position: relative; z-index: 2;
}
.ml-cloud svg { display: block; width: 100%; }
.ml-sheet {
    flex: 1; background: #fff;
    padding: 28px 28px 48px;
    position: relative; z-index: 2; min-height: 400px;
}
.dark .ml-sheet { background: #1e293b; }

/* Show/hide */
@media (min-width: 1024px) { .dl{display:flex} .ml{display:none} }
@media (max-width: 1023px) { .dl{display:none} .ml{display:flex} }

/* ====== SHARED FORM STYLES ====== */
.lp-title { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.dark .lp-title { color: #f1f5f9; }
.lp-sub { font-size: 13px; color: #94a3b8; margin-bottom: 28px; }
.lp-form { width: 100%; max-width: 380px; }
.ul-group { margin-bottom: 22px; }
.ul-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #64748b; margin-bottom: 8px; }
.dark .ul-label { color: #94a3b8; }
.ul-wrap { position: relative; border-bottom: 1.5px solid #cbd5e1; transition: border-color .2s; display: flex; align-items: center; }
.ul-wrap:focus-within { border-bottom-color: #2563eb; }
.ul-icon { color: #94a3b8; flex-shrink: 0; padding: 0 8px 0 2px; }
.ul-input { flex: 1; border: none; outline: none; background: transparent; padding: 10px 0; font-size: 14px; color: #0f172a; font-family: 'Outfit', sans-serif; }
.dark .ul-input { color: #f1f5f9; }
.ul-input::placeholder { color: #cbd5e1; }
.ul-right { color: #94a3b8; cursor: pointer; border: none; background: transparent; padding: 4px 2px; flex-shrink: 0; }
.lp-btn {
    width: 100%; background: linear-gradient(90deg, #2563eb, #4f46e5);
    color: #fff; border: none; border-radius: 50px; padding: 14px;
    font-size: 15px; font-weight: 700; font-family: 'Outfit', sans-serif;
    cursor: pointer; transition: all .25s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 20px rgba(37,99,235,0.35); margin-top: 8px;
}
.lp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,99,235,0.45); }
.lp-row { display: flex; align-items: center; justify-content: space-between; margin: 18px 0 26px; }
.lp-remember { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; cursor: pointer; }
.lp-forgot { font-size: 12px; font-weight: 700; color: #2563eb; text-decoration: none; }
.lp-forgot:hover { color: #1d4ed8; }
.lp-footer { margin-top: 24px; font-size: 11px; color: #94a3b8; }
.lp-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 10px; padding: 12px 16px; font-size: 12px; margin-bottom: 18px; }

/* Stagger */
.si { opacity: 0; animation: fadeUp .55s cubic-bezier(.2,.8,.2,1) forwards; }
.d1{animation-delay:.1s}.d2{animation-delay:.2s}.d3{animation-delay:.3s}.d4{animation-delay:.4s}.d5{animation-delay:.5s}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>

{{-- ============================
     DESKTOP LAYOUT (>=1024px)
     ============================ --}}
<div class="dl">
    <div class="dl-left">
        {{-- Animated orbs (Swift-style) --}}
        <div class="dl-orb dl-orb-a"></div>
        <div class="dl-orb dl-orb-b"></div>
        <div class="dl-orb dl-orb-c"></div>

        {{-- 3-layer cloud divider on right edge (Spacer-style big smooth bumps) --}}
        <div class="dl-cloud">
            <svg viewBox="0 0 160 1000" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">

                {{-- Layer 1: BACK — most transparent, widest bumps --}}
                <path d="
                    M160,0 L160,1000
                    L40,1000
                    C40,1000 40,960 55,930
                    C70,900 25,880 25,840
                    C25,800 65,780 65,740
                    C65,700 20,680 20,640
                    C20,600 60,575 60,535
                    C60,495 15,475 15,435
                    C15,395 55,370 55,330
                    C55,290 25,270 25,230
                    C25,190 60,170 60,130
                    C60,90 30,70 30,40
                    C30,15 45,5 50,0
                    L160,0 Z
                " fill="white" opacity="0.12"/>

                {{-- Layer 2: MIDDLE — semi-transparent --}}
                <path d="
                    M160,0 L160,1000
                    L60,1000
                    C60,1000 55,955 72,920
                    C89,885 48,860 48,815
                    C48,770 85,748 85,705
                    C85,662 42,640 42,595
                    C42,550 82,530 82,488
                    C82,446 38,425 38,382
                    C38,339 78,318 78,275
                    C78,232 45,212 45,170
                    C45,128 80,108 80,68
                    C80,38 60,15 65,0
                    L160,0 Z
                " fill="white" opacity="0.3"/>

                {{-- Layer 3: FRONT — solid white, main cloud shape --}}
                <path d="
                    M160,0 L160,1000
                    L80,1000
                    C80,1000 75,950 90,912
                    C105,874 70,848 70,805
                    C70,762 102,738 102,695
                    C102,652 65,630 65,588
                    C65,546 98,522 98,480
                    C98,438 60,416 60,374
                    C60,332 96,310 96,268
                    C96,226 64,204 64,162
                    C64,120 95,100 95,62
                    C95,30 78,10 82,0
                    L160,0 Z
                " fill="white"/>

            </svg>
        </div>

        {{-- Brand content — all white text --}}
        <div class="dl-brand si d1">
            <div class="dl-logo">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <p class="text-white/50 text-[10px] tracking-[0.2em] uppercase font-bold mb-1">SMKN 1 Ciamis</p>
            <h1 class="text-white font-extrabold text-2xl tracking-wide">SISTEM PKL</h1>
            <p class="text-white/60 text-sm mt-3 leading-relaxed max-w-xs">Monitoring administrasi, jurnal harian, dan absensi siswa PKL terintegrasi.</p>

            <div class="dl-feats si d2">
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="shield-check" class="w-4 h-4 text-white"></i></span>
                    <span class="text-white/75">Akses Aman Terenkripsi</span>
                </div>
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="activity" class="w-4 h-4 text-white"></i></span>
                    <span class="text-white/75">Monitoring Harian Real-Time</span>
                </div>
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="briefcase" class="w-4 h-4 text-white"></i></span>
                    <span class="text-white/75">Evaluasi Bimbingan DUDI</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right white panel --}}
    <div class="dl-right">
        <div class="lp-form si d1">
            <h2 class="lp-title">Selamat datang kembali</h2>
            <p class="lp-sub">Silakan masuk menggunakan akun MAS-PKL Anda</p>
            @if ($errors->any())
                <div class="lp-error"><ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('login') }}" x-data="{ un:'{{ old('username') }}', pw:'', show:false }">
                @csrf
                <div class="ul-group">
                    <label class="ul-label">Username / NIS / NIP</label>
                    <div class="ul-wrap">
                        <span class="ul-icon"><i data-lucide="user" class="w-4 h-4"></i></span>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" x-model="un" required autofocus class="ul-input" placeholder="Username / NIS / NIP">
                    </div>
                </div>
                <div class="ul-group">
                    <label class="ul-label">Password</label>
                    <div class="ul-wrap">
                        <span class="ul-icon"><i data-lucide="lock" class="w-4 h-4"></i></span>
                        <input id="password" name="password" :type="show?'text':'password'" x-model="pw" required class="ul-input" placeholder="Password Anda">
                        <button type="button" class="ul-right" @click="show=!show">
                            <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="show" class="w-4 h-4" x-cloak></i>
                        </button>
                    </div>
                </div>
                <div class="lp-row">
                    <label class="lp-remember"><input type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 rounded"> Ingat Saya</label>
                    <a href="#" class="lp-forgot">Lupa Password?</a>
                </div>
                <button type="submit" class="lp-btn"><i data-lucide="log-in" class="w-4 h-4"></i> Masuk</button>
            </form>
            <p class="lp-footer">Protected admin area — MAS-PKL © {{ date('Y') }}</p>
        </div>
    </div>
</div>

{{-- ============================
     MOBILE LAYOUT (<1024px)
     ============================ --}}
<div class="ml">
    <div class="ml-header">
        <h1 class="text-white font-extrabold text-3xl tracking-wide mb-1">MAS-PKL</h1>
        <p class="text-white/80 text-sm font-medium">Monitoring & Administrasi Siswa PKL</p>
    </div>

    {{-- Horizontal multi-layer cloud (Spacer-style big smooth bumps) --}}
    <div class="ml-cloud">
        <svg viewBox="0 0 400 140" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">

            {{-- Layer 1: BACK — most transparent --}}
            <path d="
                M0,140 L400,140 L400,50
                C400,50 380,30 350,35
                C320,40 310,65 280,60
                C250,55 245,30 215,25
                C185,20 178,48 148,45
                C118,42 115,20 85,18
                C55,16 48,42 20,38
                C8,36 0,28 0,28
                L0,140 Z
            " fill="white" opacity="0.12"/>

            {{-- Layer 2: MIDDLE — semi-transparent --}}
            <path d="
                M0,140 L400,140 L400,65
                C400,65 385,48 358,52
                C331,56 322,78 295,72
                C268,66 260,42 232,38
                C204,34 195,58 168,55
                C141,52 135,32 108,30
                C81,28 72,52 45,48
                C25,45 10,38 0,40
                L0,140 Z
            " fill="white" opacity="0.3"/>

            {{-- Layer 3: FRONT — solid white --}}
            <path d="
                M0,140 L400,140 L400,82
                C400,82 388,62 365,68
                C342,74 335,95 310,88
                C285,81 278,58 252,55
                C226,52 218,72 192,68
                C166,64 160,45 135,42
                C110,39 102,62 78,58
                C54,54 45,72 22,65
                C10,61 0,55 0,55
                L0,140 Z
            " fill="white"/>

        </svg>
    </div>

    <div class="ml-sheet">
        <h2 class="lp-title mb-1">Sign in</h2>
        <p class="lp-sub">Masuk menggunakan akun MAS-PKL Anda</p>
        @if ($errors->any())
            <div class="lp-error"><ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('login') }}" x-data="{ un:'{{ old('username') }}', pw:'', show:false }">
            @csrf
            <div class="ul-group">
                <label class="ul-label">Username / NIS / NIP</label>
                <div class="ul-wrap">
                    <span class="ul-icon"><i data-lucide="user" class="w-4 h-4"></i></span>
                    <input name="username" type="text" value="{{ old('username') }}" x-model="un" required autofocus class="ul-input" placeholder="Username / NIS / NIP">
                </div>
            </div>
            <div class="ul-group">
                <label class="ul-label">Password</label>
                <div class="ul-wrap">
                    <span class="ul-icon"><i data-lucide="lock" class="w-4 h-4"></i></span>
                    <input name="password" :type="show?'text':'password'" x-model="pw" required class="ul-input" placeholder="Password Anda">
                    <button type="button" class="ul-right" @click="show=!show">
                        <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                        <i data-lucide="eye-off" x-show="show" class="w-4 h-4" x-cloak></i>
                    </button>
                </div>
            </div>
            <div class="lp-row">
                <label class="lp-remember"><input type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 rounded"> Ingat Saya</label>
                <a href="#" class="lp-forgot">Lupa Password?</a>
            </div>
            <button type="submit" class="lp-btn"><i data-lucide="log-in" class="w-4 h-4"></i> Masuk</button>
        </form>
        <p class="lp-footer">Protected admin area — MAS-PKL © {{ date('Y') }}</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</x-guest-layout>
