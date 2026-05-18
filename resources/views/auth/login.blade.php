<x-guest-layout>
<style>
/* =============================================
   LOGIN PAGE — "Spacer" style
   Desktop: blue left panel + white right form (split)
   Mobile: full-page blue top + cloud-wave + white bottom form (no card)
   ============================================= */

* { box-sizing: border-box; }

.login-wrap {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    font-family: 'Outfit', sans-serif;
    overflow: hidden;
}

/* ---- DESKTOP: Side-by-side ---- */
.lp-blue {
    width: 42%;
    height: 100%;
    background: linear-gradient(160deg, #2563eb 0%, #1e3a8a 60%, #0f172a 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 48px 40px;
    text-align: center;
}

.lp-white {
    flex: 1;
    height: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    padding: 56px 48px;
    overflow-y: auto;
    position: relative;
}
.dark .lp-white { background: #1e293b; }

/* Cloud wave divider (right edge of blue panel) */
.lp-cloud-right {
    position: absolute;
    right: -1px;
    top: 0;
    bottom: 0;
    width: 80px;
    z-index: 10;
}
.lp-cloud-right svg {
    width: 100%;
    height: 100%;
}

/* Glowing orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    opacity: 0.35;
    pointer-events: none;
}
.orb-a { width:300px; height:300px; background:#3b82f6; top:-80px; left:-80px; animation: oa 20s ease-in-out infinite alternate; }
.orb-b { width:350px; height:350px; background:#6366f1; bottom:-120px; left:20%; animation: ob 24s ease-in-out infinite alternate; }
@keyframes oa { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(60px,100px) scale(1.2)} }
@keyframes ob { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(-80px,-80px) scale(1.3)} }

/* Logo icon box */
.logo-box {
    width: 64px; height: 64px;
    background: #ffffff;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.2);
    padding: 8px;
}

/* Brand feature list */
.feat-list { margin-top: 40px; display: flex; flex-direction: column; gap: 14px; text-align: left; width: 100%; }
.feat-item { display:flex; align-items:center; gap:12px; color:rgba(255,255,255,0.7); font-size:13px; font-weight:500; }
.feat-icon { width:32px; height:32px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* Form styles (no card — flat on white) */
.lp-form-title { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.dark .lp-form-title { color: #f1f5f9; }
.lp-form-sub { font-size: 13.5px; color: #94a3b8; margin-bottom: 36px; }
.dark .lp-form-sub { color: #64748b; }

.lp-form { width: 100%; max-width: 380px; }

/* Underline inputs like reference */
.ul-group { margin-bottom: 24px; }
.ul-label { display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #475569; margin-bottom: 8px; }
.dark .ul-label { color: #94a3b8; }
.ul-input-wrap { position: relative; border-bottom: 1.5px solid #cbd5e1; transition: border-color .2s; }
.ul-input-wrap:focus-within { border-bottom-color: #2563eb; }
.ul-icon { position: absolute; left: 0; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.ul-input {
    width: 100%; border: none; outline: none; background: transparent;
    padding: 10px 36px 10px 28px;
    font-size: 14px; color: #0f172a; font-family: 'Outfit', sans-serif;
}
.dark .ul-input { color: #f1f5f9; }
.ul-input::placeholder { color: #cbd5e1; }
.ul-input-right { position: absolute; right: 0; top: 50%; transform: translateY(-50%); color: #94a3b8; cursor: pointer; border: none; background: transparent; padding: 4px; }

/* Submit button */
.lp-btn {
    width: 100%;
    background: linear-gradient(90deg, #2563eb, #4f46e5);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 14px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all .25s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 20px rgba(37,99,235,0.35);
    margin-top: 8px;
}
.lp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,99,235,0.45); }

/* Remember + forgot */
.lp-row { display: flex; align-items: center; justify-content: space-between; margin: 20px 0 28px; }
.lp-remember { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #64748b; cursor: pointer; }
.lp-forgot { font-size: 12.5px; font-weight: 700; color: #2563eb; text-decoration: none; }
.lp-forgot:hover { color: #1d4ed8; }

/* Footer */
.lp-footer { margin-top: 32px; font-size: 11.5px; color: #94a3b8; }

/* Error box */
.lp-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 10px; padding: 12px 16px; font-size: 12px; margin-bottom: 20px; }

/* =============================================
   MOBILE OVERRIDES (≤ 1023px)
   Full page: blue top + cloud blob + white bottom, NO card
   ============================================= */
@media (max-width: 1023px) {
    .login-wrap {
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Blue top section */
    .lp-blue {
        width: 100%;
        height: auto;
        min-height: 240px;
        padding: 48px 24px 100px; /* extra bottom for wave overlap */
        border-right: none;
    }

    /* Hide cloud right divider on mobile */
    .lp-cloud-right { display: none; }

    /* Hide desktop-only: logo box, feat list */
    .lp-desktop-only { display: none !important; }

    /* White bottom section */
    .lp-white {
        width: 100%;
        height: auto;
        flex: none;
        padding: 36px 24px 48px;
        align-items: stretch;
        position: relative;
    }

    /* Mobile cloud wave — sits between blue and white, overlapping blue bottom */
    .lp-mobile-wave {
        display: block !important; /* shown on mobile */
        position: relative;
        width: 100%;
        margin-top: -90px; /* pulls it up over the blue section */
        z-index: 5;
        line-height: 0;
        background: #fff;
    }
    .dark .lp-mobile-wave { background: #1e293b; }
    .lp-mobile-wave svg { display: block; width: 100%; }

    /* Form: no card, just flat on white */
    .lp-form { max-width: 100%; }
    .lp-form-title { font-size: 26px; }
    .lp-form-sub { margin-bottom: 28px; }

    /* Mobile: show logo smaller + title inline */
    .lp-mobile-brand { display: flex !important; flex-direction: column; align-items: center; }
}
/* Hide mobile wave on desktop */
.lp-mobile-wave { display: none; }

/* Stagger animations */
.si { opacity:0; animation: fadeUp .6s cubic-bezier(.2,.8,.2,1) forwards; }
.d1{animation-delay:.1s} .d2{animation-delay:.2s} .d3{animation-delay:.3s} .d4{animation-delay:.4s} .d5{animation-delay:.5s}
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>

<div class="login-wrap">

    <!-- ========== BLUE PANEL ========== -->
    <div class="lp-blue">
        <div class="orb orb-a"></div>
        <div class="orb orb-b"></div>

        <!-- DESKTOP: Cloud wave on right edge -->
        <div class="lp-cloud-right">
            <svg viewBox="0 0 80 800" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M80,0 C40,80 70,160 30,240 C0,300 50,360 20,420 C-10,480 50,540 30,600 C10,660 50,720 40,800 L80,800 L80,0 Z" fill="white"/>
            </svg>
        </div>

        <!-- Mobile wave (bumpy clouds, shown only on mobile via CSS) -->
        <div class="lp-mobile-wave">
            <svg viewBox="0 0 390 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <!-- Bumpy cloud-like wave exactly like Spacer reference -->
                <path d="
                    M0,70
                    C15,70 15,45 30,45
                    C45,45 45,60 60,55
                    C75,50 75,30 95,30
                    C115,30 115,50 135,48
                    C155,46 158,25 178,22
                    C198,19 200,40 220,38
                    C240,36 242,15 265,15
                    C288,15 290,38 310,36
                    C330,34 332,18 355,20
                    C375,22 378,45 390,45
                    L390,100 L0,100 Z
                " fill="white"/>
            </svg>
        </div>

        <!-- Desktop: logo + brand + features -->
        <div class="lp-desktop-only flex flex-col items-center text-center si d1">
            <div class="logo-box">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <p class="text-blue-200/70 text-[10px] tracking-[0.2em] uppercase font-bold mb-1">SMKN 1 Ciamis</p>
            <h1 class="text-white font-extrabold text-2xl tracking-wide leading-tight">SISTEM PKL</h1>
            <p class="text-blue-100/60 text-sm mt-3 leading-relaxed max-w-xs">Monitoring administrasi, jurnal harian, dan absensi siswa PKL terintegrasi.</p>

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

        <!-- Mobile: only text brand (no logo, no features) -->
        <div class="lp-mobile-brand hidden text-center si d1">
            <h1 class="text-white font-extrabold text-3xl tracking-wide mb-1">MAS-PKL</h1>
            <p class="text-blue-100 text-sm opacity-90">Monitoring & Administrasi Siswa PKL</p>
        </div>
    </div>

    <!-- ========== WHITE PANEL (form — no card box) ========== -->
    <div class="lp-white">
        <div class="lp-form si d2">

            <h2 class="lp-form-title">Sign in</h2>
            <p class="lp-form-sub">Masuk menggunakan akun MAS-PKL Anda</p>

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

                <!-- Username -->
                <div class="ul-group">
                    <label class="ul-label">Username / NIS / NIP</label>
                    <div class="ul-input-wrap">
                        <span class="ul-icon">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input id="username" name="username" type="text"
                               value="{{ old('username') }}"
                               x-model="username" required autofocus
                               class="ul-input"
                               placeholder="Username / NIS / NIP">
                    </div>
                </div>

                <!-- Password -->
                <div class="ul-group">
                    <label class="ul-label">Password</label>
                    <div class="ul-input-wrap">
                        <span class="ul-icon">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input id="password" name="password"
                               :type="showPassword ? 'text' : 'password'"
                               x-model="password" required
                               class="ul-input"
                               placeholder="Password Anda">
                        <button type="button" class="ul-input-right" @click="showPassword = !showPassword">
                            <i data-lucide="eye" x-show="!showPassword" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="showPassword" class="w-4 h-4" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Remember + Forgot -->
                <div class="lp-row">
                    <label class="lp-remember">
                        <input type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 cursor-pointer rounded">
                        Ingat Saya
                    </label>
                    <a href="#" class="lp-forgot">Lupa Password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="lp-btn"
                        x-bind:style="(!username || !password) ? 'background: linear-gradient(90deg,#94a3b8,#a5b4fc); box-shadow:none; cursor:not-allowed' : ''">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>

            <p class="lp-footer">Protected admin area — MAS-PKL © {{ date('Y') }}</p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</x-guest-layout>
