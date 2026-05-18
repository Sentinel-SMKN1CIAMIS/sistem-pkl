<x-guest-layout>
<style>
* { box-sizing: border-box; }

/* =============================================
   DESKTOP LAYOUT: Split panel left/right
   ============================================= */
.login-page {
    position: fixed; inset: 0; z-index: 9999;
    font-family: 'Outfit', sans-serif;
    background: #fff;
    overflow: hidden;
}
.dark .login-page { background: #0f172a; }

/* --- DESKTOP --- */
@media (min-width: 1024px) {
    .login-page {
        display: flex;
        flex-direction: row;
    }

    /* Left blue panel */
    .panel-blue {
        width: 42%;
        height: 100%;
        background: linear-gradient(160deg, #2563eb 0%, #1e3a8a 55%, #0f172a 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 56px 40px;
        text-align: center;
        flex-shrink: 0;
    }

    /* Cloud wave on right edge of blue panel (desktop) */
    .desktop-cloud {
        display: block;
        position: absolute;
        right: -1px; top: 0; bottom: 0;
        width: 72px;
        z-index: 10;
    }
    .desktop-cloud svg { width: 100%; height: 100%; }
    .mobile-cloud { display: none; }
    .mobile-brand { display: none; }

    /* Right white panel */
    .panel-white {
        flex: 1;
        height: 100%;
        background: #fff;
        overflow-y: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 56px 64px;
    }
    .dark .panel-white { background: #1e293b; }
}

/* =============================================
   MOBILE LAYOUT: Full screen, blue bg, white blob bottom
   ============================================= */
@media (max-width: 1023px) {
    .login-page {
        display: block;
        overflow-y: auto;
        /* Blue gradient fills entire screen */
        background: linear-gradient(170deg, #2563eb 0%, #1e3a8a 50%, #0f172a 100%);
        min-height: 100vh;
        padding: 0;
        position: relative;
    }
    .dark .login-page { background: linear-gradient(170deg, #1d4ed8 0%, #1e3a8a 50%, #0f172a 100%); }

    /* Blue panel: just the top text area */
    .panel-blue {
        width: 100%;
        background: transparent;
        position: relative;
        overflow: visible;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 56px 24px 16px;
        text-align: center;
    }

    /* Hide desktop cloud and features on mobile */
    .desktop-cloud { display: none; }
    .desktop-brand { display: none !important; }
    .mobile-brand { display: flex; }

    /* The white blob container — comes after panel-blue in HTML */
    .panel-white {
        width: 100%;
        background: transparent;
        padding: 0;
        display: block;
        position: relative;
    }

    /* The white cloud wave at top of form area */
    .mobile-cloud {
        display: block;
        width: 100%;
        line-height: 0;
        position: relative;
        z-index: 2;
    }
    .mobile-cloud svg { display: block; width: 100%; height: 80px; }

    /* White form area */
    .form-area {
        background: #fff;
        padding: 8px 24px 48px;
        position: relative;
        z-index: 2;
        min-height: 50vh;
    }
    .dark .form-area { background: #1e293b; }
}

/* --- Orbs (desktop only) --- */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    opacity: 0.35;
    pointer-events: none;
    z-index: 0;
}
.orb-a { width:280px; height:280px; background:#3b82f6; top:-80px; left:-60px; animation: oa 20s ease-in-out infinite alternate; }
.orb-b { width:320px; height:320px; background:#6366f1; bottom:-100px; left:10%; animation: ob 24s ease-in-out infinite alternate; }
@keyframes oa { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(60px,100px) scale(1.2)} }
@keyframes ob { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(-60px,-80px) scale(1.3)} }

/* --- Desktop Brand Block --- */
.desktop-brand { position: relative; z-index: 2; width: 100%; }

.logo-box {
    width: 68px; height: 68px;
    background: #ffffff;
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.22);
    padding: 8px;
}

.feat-list { margin-top: 36px; display: flex; flex-direction: column; gap: 14px; text-align: left; }
.feat-item { display:flex; align-items:center; gap:12px; color:rgba(255,255,255,0.72); font-size:13px; font-weight:500; }
.feat-icon {
    width:32px; height:32px;
    background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.15);
    border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}

/* --- Form styles (flat, no card) --- */
.lp-form { width: 100%; max-width: 380px; }

.lp-title { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.dark .lp-title { color: #f1f5f9; }
.lp-sub { font-size: 13.5px; color: #94a3b8; margin-bottom: 32px; }

/* Underline input style */
.ul-group { margin-bottom: 24px; }
.ul-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #64748b; margin-bottom: 8px; }
.dark .ul-label { color: #94a3b8; }
.ul-wrap { position: relative; border-bottom: 1.5px solid #cbd5e1; transition: border-color .2s; display: flex; align-items: center; }
.ul-wrap:focus-within { border-bottom-color: #2563eb; }
.ul-icon { color: #94a3b8; flex-shrink: 0; padding: 0 8px 0 2px; }
.ul-input {
    flex: 1; border: none; outline: none; background: transparent;
    padding: 10px 0; font-size: 14px; color: #0f172a; font-family: 'Outfit', sans-serif;
}
.dark .ul-input { color: #f1f5f9; }
.ul-input::placeholder { color: #cbd5e1; }
.ul-right { color: #94a3b8; cursor: pointer; border: none; background: transparent; padding: 4px 2px; flex-shrink: 0; }

/* Submit button */
.lp-btn {
    width: 100%; background: linear-gradient(90deg, #2563eb, #4f46e5);
    color: #fff; border: none; border-radius: 50px;
    padding: 14px; font-size: 15px; font-weight: 700;
    font-family: 'Outfit', sans-serif; cursor: pointer; transition: all .25s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 20px rgba(37,99,235,0.35); margin-top: 8px;
}
.lp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,99,235,0.45); }

.lp-row { display:flex; align-items:center; justify-content:space-between; margin: 20px 0 28px; }
.lp-remember { display:flex; align-items:center; gap:8px; font-size:12.5px; color:#64748b; cursor:pointer; }
.lp-forgot { font-size:12.5px; font-weight:700; color:#2563eb; text-decoration:none; }
.lp-forgot:hover { color:#1d4ed8; }
.lp-footer-txt { margin-top: 28px; font-size: 11.5px; color: #94a3b8; }
.lp-error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:10px; padding:12px 16px; font-size:12px; margin-bottom:20px; }

/* Stagger */
.si { opacity:0; animation: fadeUp .55s cubic-bezier(.2,.8,.2,1) forwards; }
.d1{animation-delay:.1s} .d2{animation-delay:.2s} .d3{animation-delay:.3s}
.d4{animation-delay:.4s} .d5{animation-delay:.5s}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>

<div class="login-page">

    <!-- ========== BLUE PANEL (left on desktop / top on mobile) ========== -->
    <div class="panel-blue">
        <!-- Orbs (desktop visual only) -->
        <div class="orb orb-a"></div>
        <div class="orb orb-b"></div>

        <!-- Desktop cloud wave (right edge) -->
        <div class="desktop-cloud">
            <svg viewBox="0 0 72 800" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M72,0 C36,80 60,160 24,240 C0,300 44,360 16,430 C-8,490 44,550 24,620 C4,680 44,730 36,800 L72,800 L72,0 Z" fill="white"/>
            </svg>
        </div>

        <!-- DESKTOP brand block -->
        <div class="desktop-brand si d1">
            <div class="logo-box">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <p class="text-blue-200/60 text-[10px] tracking-[0.2em] uppercase font-bold mb-1 relative z-10">SMKN 1 Ciamis</p>
            <h1 class="text-white font-extrabold text-2xl tracking-wide relative z-10">SISTEM PKL</h1>
            <p class="text-blue-100/55 text-sm mt-3 leading-relaxed max-w-xs relative z-10">Monitoring administrasi, jurnal harian, dan absensi siswa PKL terintegrasi.</p>

            <div class="feat-list si d2">
                <div class="feat-item">
                    <span class="feat-icon"><i data-lucide="shield-check" class="w-4 h-4 text-white"></i></span>
                    Akses Aman Terenkripsi
                </div>
                <div class="feat-item">
                    <span class="feat-icon"><i data-lucide="activity" class="w-4 h-4 text-white"></i></span>
                    Monitoring Harian Real-Time
                </div>
                <div class="feat-item">
                    <span class="feat-icon"><i data-lucide="briefcase" class="w-4 h-4 text-white"></i></span>
                    Evaluasi Bimbingan DUDI
                </div>
            </div>
        </div>

        <!-- MOBILE brand block (text only, no logo, no features) -->
        <div class="mobile-brand flex-col items-center si d1">
            <h1 class="text-white font-extrabold text-3xl tracking-wide mb-1 relative z-10">MAS-PKL</h1>
            <p class="text-blue-100 text-sm font-medium opacity-85 relative z-10">Monitoring & Administrasi Siswa PKL</p>
        </div>
    </div>

    <!-- ========== WHITE PANEL (right on desktop / bottom on mobile) ========== -->
    <div class="panel-white">

        <!-- Mobile: organic cloud wave transition (blue → white) -->
        <div class="mobile-cloud">
            <!-- Bumpy cloud wave identical to Spacer reference -->
            <svg viewBox="0 0 390 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                 style="fill: white;">
                <path d="
                    M0,55
                    C15,55 20,38 38,38
                    C56,38 58,52 75,48
                    C92,44 95,28 115,26
                    C135,24 138,44 158,42
                    C178,40 182,20 205,18
                    C228,16 230,38 252,36
                    C274,34 276,14 300,16
                    C324,18 326,40 348,38
                    C368,36 372,20 390,22
                    L390,80 L0,80 Z
                "/>
            </svg>
        </div>

        <!-- Mobile: white form area (no card) -->
        <div class="form-area">
            <div class="lp-form">
                <h2 class="lp-title si d1">Sign in</h2>
                <p class="lp-sub si d2">Masuk menggunakan akun MAS-PKL Anda</p>

                @if ($errors->any())
                    <div class="lp-error">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}"
                      x-data="{ username: '{{ old('username') }}', password: '', showPassword: false }">
                    @csrf

                    <div class="ul-group si d2">
                        <label class="ul-label">Username / NIS / NIP</label>
                        <div class="ul-wrap">
                            <span class="ul-icon"><i data-lucide="user" class="w-4 h-4"></i></span>
                            <input id="username" name="username" type="text" value="{{ old('username') }}"
                                   x-model="username" required autofocus
                                   class="ul-input" placeholder="Username / NIS / NIP">
                        </div>
                    </div>

                    <div class="ul-group si d3">
                        <label class="ul-label">Password</label>
                        <div class="ul-wrap">
                            <span class="ul-icon"><i data-lucide="lock" class="w-4 h-4"></i></span>
                            <input id="password" name="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   x-model="password" required
                                   class="ul-input" placeholder="Password Anda">
                            <button type="button" class="ul-right" @click="showPassword = !showPassword">
                                <i data-lucide="eye" x-show="!showPassword" class="w-4 h-4"></i>
                                <i data-lucide="eye-off" x-show="showPassword" class="w-4 h-4" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <div class="lp-row si d3">
                        <label class="lp-remember">
                            <input type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 cursor-pointer rounded">
                            Ingat Saya
                        </label>
                        <a href="#" class="lp-forgot">Lupa Password?</a>
                    </div>

                    <button type="submit" class="lp-btn si d4">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk
                    </button>
                </form>

                <p class="lp-footer-txt si d5">Protected admin area — MAS-PKL © {{ date('Y') }}</p>
            </div>
        </div>

        <!-- Desktop: form directly inside panel-white (no form-area wrapper needed) -->
        <div class="lp-form si d2" style="display: none;" id="desktop-form-placeholder"></div>
    </div>

</div>

<script>
    // On desktop, move the form from .form-area into .panel-white directly
    (function() {
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
        if (isDesktop) {
            const formArea = document.querySelector('.form-area');
            const panelWhite = document.querySelector('.panel-white');
            const mobileCloud = document.querySelector('.mobile-cloud');
            const placeholder = document.getElementById('desktop-form-placeholder');
            if (formArea && panelWhite) {
                // Move lp-form out of form-area into panel-white
                const lpForm = formArea.querySelector('.lp-form');
                if (lpForm) {
                    panelWhite.appendChild(lpForm);
                }
                formArea.style.display = 'none';
                if (mobileCloud) mobileCloud.style.display = 'none';
                if (placeholder) placeholder.remove();
            }
        }
    })();

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</x-guest-layout>
