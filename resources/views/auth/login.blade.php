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
.dl-left {
    width: 42%; height: 100%;
    background: linear-gradient(160deg, #2563eb 0%, #1e3a8a 55%, #0f172a 100%);
    position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 56px 40px; text-align: center; flex-shrink: 0;
}
.dl-orb {
    position: absolute; border-radius: 50%;
    filter: blur(70px); opacity: 0.35; pointer-events: none; z-index: 0;
}
.dl-orb-a { width:280px; height:280px; background:#3b82f6; top:-80px; left:-60px; animation: oa 20s ease-in-out infinite alternate; }
.dl-orb-b { width:320px; height:320px; background:#6366f1; bottom:-100px; left:10%; animation: ob 24s ease-in-out infinite alternate; }
@keyframes oa { 100%{transform:translate(60px,100px) scale(1.2)} }
@keyframes ob { 100%{transform:translate(-60px,-80px) scale(1.3)} }

/* Desktop cloud: vertical fluffy cloud on right edge */
.dl-cloud {
    position: absolute; right: -1px; top: 0; bottom: 0; width: 120px; z-index: 10;
}
.dl-cloud svg { width: 100%; height: 100%; }

.dl-logo { width:66px; height:66px; background:#fff; border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; box-shadow:0 6px 24px rgba(0,0,0,0.22); padding:8px; position:relative; z-index:1; }
.dl-brand { position:relative; z-index:1; }
.dl-feats { margin-top:32px; display:flex; flex-direction:column; gap:14px; text-align:left; }
.dl-feat { display:flex; align-items:center; gap:12px; color:rgba(255,255,255,0.72); font-size:13px; font-weight:500; }
.dl-feat-icon { width:32px; height:32px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

.dl-right {
    flex:1; height:100%; background:#fff; overflow-y:auto;
    display:flex; align-items:center; justify-content:center; padding:56px 64px;
}
.dark .dl-right { background:#1e293b; }

/* ====== MOBILE LOGIN ====== */
.ml {
    position: fixed; inset: 0; z-index: 9999;
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(170deg, #1d4ed8 0%, #1e3a8a 45%, #0f172a 100%);
    overflow-y: auto; overflow-x: hidden;
    display: flex; flex-direction: column;
}
.ml-header {
    width: 100%;
    padding: 52px 24px 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
    flex-shrink: 0;
    min-height: 200px;
}
/* Mobile cloud: horizontal fluffy cloud between blue and white */
.ml-cloud {
    width: 100%; flex-shrink: 0;
    line-height: 0; margin-top: -10px;
    position: relative; z-index: 2;
}
.ml-cloud svg { display: block; width: 100%; }

.ml-sheet {
    flex: 1;
    background: #fff;
    padding: 28px 28px 48px;
    position: relative; z-index: 2;
    min-height: 400px;
}
.dark .ml-sheet { background:#1e293b; }

/* Show/hide */
@media (min-width: 1024px) { .dl{display:flex} .ml{display:none} }
@media (max-width: 1023px) { .dl{display:none} .ml{display:flex} }

/* ====== FORM STYLES (shared) ====== */
.lp-title { font-size:26px; font-weight:800; color:#0f172a; margin-bottom:6px; }
.dark .lp-title { color:#f1f5f9; }
.lp-sub { font-size:13px; color:#94a3b8; margin-bottom:28px; }
.lp-form { width:100%; max-width:380px; }
.ul-group { margin-bottom:22px; }
.ul-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; margin-bottom:8px; }
.dark .ul-label { color:#94a3b8; }
.ul-wrap { position:relative; border-bottom:1.5px solid #cbd5e1; transition:border-color .2s; display:flex; align-items:center; }
.ul-wrap:focus-within { border-bottom-color:#2563eb; }
.ul-icon { color:#94a3b8; flex-shrink:0; padding:0 8px 0 2px; }
.ul-input { flex:1; border:none; outline:none; background:transparent; padding:10px 0; font-size:14px; color:#0f172a; font-family:'Outfit',sans-serif; }
.dark .ul-input { color:#f1f5f9; }
.ul-input::placeholder { color:#cbd5e1; }
.ul-right { color:#94a3b8; cursor:pointer; border:none; background:transparent; padding:4px 2px; flex-shrink:0; }
.lp-btn { width:100%; background:linear-gradient(90deg,#2563eb,#4f46e5); color:#fff; border:none; border-radius:50px; padding:14px; font-size:15px; font-weight:700; font-family:'Outfit',sans-serif; cursor:pointer; transition:all .25s; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 20px rgba(37,99,235,0.35); margin-top:8px; }
.lp-btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(37,99,235,0.45); }
.lp-row { display:flex; align-items:center; justify-content:space-between; margin:18px 0 26px; }
.lp-remember { display:flex; align-items:center; gap:8px; font-size:12px; color:#64748b; cursor:pointer; }
.lp-forgot { font-size:12px; font-weight:700; color:#2563eb; text-decoration:none; }
.lp-forgot:hover { color:#1d4ed8; }
.lp-footer { margin-top:24px; font-size:11px; color:#94a3b8; }
.lp-error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:10px; padding:12px 16px; font-size:12px; margin-bottom:18px; }

/* Stagger */
.si { opacity:0; animation:fadeUp .55s cubic-bezier(.2,.8,.2,1) forwards; }
.d1{animation-delay:.1s}.d2{animation-delay:.2s}.d3{animation-delay:.3s}.d4{animation-delay:.4s}.d5{animation-delay:.5s}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>

{{-- ============================
     DESKTOP LAYOUT (>=1024px)
     ============================ --}}
<div class="dl">
    <div class="dl-left">
        <div class="dl-orb dl-orb-a"></div>
        <div class="dl-orb dl-orb-b"></div>

        {{-- Vertical fluffy cloud on right edge --}}
        <div class="dl-cloud">
            <svg viewBox="0 0 120 800" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                {{-- Large fluffy cloud bumps going down the right edge --}}
                <path d="
                    M120,0
                    L120,800
                    L60,800

                    C60,780 80,770 80,750
                    C80,730 50,725 50,705
                    C50,685 75,678 75,660
                    C75,640 45,632 45,612
                    C45,592 78,585 78,565
                    C78,545 42,535 42,515
                    C42,495 72,488 72,470
                    C72,450 40,442 40,422
                    C40,402 75,395 75,375
                    C75,355 48,348 48,328
                    C48,308 80,298 80,278
                    C80,258 44,248 44,228
                    C44,208 76,200 76,180
                    C76,160 46,150 46,130
                    C46,110 74,102 74,82
                    C74,62 50,52 50,32
                    C50,12 65,5 65,0

                    L120,0 Z
                " fill="white"/>
            </svg>
        </div>

        <div class="dl-brand si d1">
            <div class="dl-logo">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <p class="text-blue-200/60 text-[10px] tracking-[0.2em] uppercase font-bold mb-1">SMKN 1 Ciamis</p>
            <h1 class="text-white font-extrabold text-2xl tracking-wide">SISTEM PKL</h1>
            <p class="text-blue-100/55 text-sm mt-3 leading-relaxed max-w-xs">Monitoring administrasi, jurnal harian, dan absensi siswa PKL terintegrasi.</p>

            <div class="dl-feats si d2">
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="shield-check" class="w-4 h-4 text-white"></i></span>
                    Akses Aman Terenkripsi
                </div>
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="activity" class="w-4 h-4 text-white"></i></span>
                    Monitoring Harian Real-Time
                </div>
                <div class="dl-feat">
                    <span class="dl-feat-icon"><i data-lucide="briefcase" class="w-4 h-4 text-white"></i></span>
                    Evaluasi Bimbingan DUDI
                </div>
            </div>
        </div>
    </div>

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
    {{-- Blue header --}}
    <div class="ml-header">
        <h1 class="text-white font-extrabold text-3xl tracking-wide mb-1">MAS-PKL</h1>
        <p class="text-blue-100 text-sm font-medium opacity-85">Monitoring & Administrasi Siswa PKL</p>
    </div>

    {{-- Horizontal fluffy cloud SVG between blue and white --}}
    <div class="ml-cloud">
        <svg viewBox="0 0 400 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            {{-- Multi-layered fluffy cloud bumps exactly like Spacer reference --}}
            {{-- Back layer: softer, wider bumps --}}
            <path d="
                M0,120 L0,80
                C0,80 8,78 15,70
                C22,62 18,50 30,45
                C42,40 48,55 60,52
                C72,49 70,35 85,30
                C100,25 105,42 118,40
                C131,38 128,22 145,20
                C162,18 168,38 182,35
                C196,32 192,15 210,12
                C228,9 235,30 250,28
                C265,26 260,10 280,8
                C300,6 308,28 322,25
                C336,22 332,8 350,10
                C368,12 372,32 385,30
                C394,28 397,40 400,40
                L400,120 Z
            " fill="white" opacity="0.4"/>
            {{-- Front layer: main cloud shape --}}
            <path d="
                M0,120 L0,90
                C0,90 10,88 18,80
                C26,72 20,60 35,55
                C50,50 58,68 72,62
                C86,56 82,42 100,38
                C118,34 125,52 140,48
                C155,44 150,30 170,28
                C190,26 198,46 212,42
                C226,38 222,22 242,20
                C262,18 270,38 285,35
                C300,32 296,18 318,16
                C340,14 345,35 360,32
                C375,29 378,45 390,42
                C398,40 400,50 400,50
                L400,120 Z
            " fill="white"/>
        </svg>
    </div>

    {{-- White form sheet --}}
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
