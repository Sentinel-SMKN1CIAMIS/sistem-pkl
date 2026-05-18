<x-guest-layout>
    <div class="flex flex-col lg:flex-row w-full min-h-screen">
        
        <!-- Left Side: Branding (Desktop Only) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 overflow-hidden flex-col justify-center items-center p-12 shadow-[4px_0_24px_rgba(0,0,0,0.1)] z-20">
            <!-- Decorative Background -->
            <div class="absolute inset-0">
                <!-- Base gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-700 via-indigo-800 to-slate-900 opacity-95 z-10"></div>
                <!-- Network / Tech Background Image -->
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80" alt="Background" class="w-full h-full object-cover opacity-20 mix-blend-overlay">
                
                <!-- Abstract glowing blobs -->
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-blue-500/40 blur-3xl z-10 animate-pulse" style="animation-duration: 4s;"></div>
                <div class="absolute bottom-10 right-10 w-72 h-72 rounded-full bg-indigo-500/30 blur-3xl z-10 animate-pulse" style="animation-duration: 5s;"></div>
            </div>

            <!-- Content -->
            <div class="relative z-20 text-center flex flex-col items-center">
                <div class="w-28 h-28 bg-white/10 backdrop-blur-md rounded-3xl p-5 shadow-2xl mb-8 border border-white/20 relative group">
                    <div class="absolute inset-0 bg-white/20 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="{{ asset('logo.png') }}" alt="Logo SMKN 1 Ciamis" class="w-full h-full object-contain filter drop-shadow-xl relative z-10">
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-6 tracking-tight drop-shadow-md">
                    MAS-<span class="text-blue-300">PKL</span>
                </h1>
                <p class="text-blue-100/90 text-lg lg:text-xl font-medium max-w-md leading-relaxed">
                    Sistem Monitoring & Administrasi Siswa Praktek Kerja Lapangan.
                </p>
                <div class="mt-16 flex items-center gap-5 text-white/70">
                    <div class="h-[2px] w-12 bg-gradient-to-r from-transparent to-white/30 rounded-full"></div>
                    <span class="text-sm font-bold tracking-[0.2em] uppercase">SMK Negeri 1 Ciamis</span>
                    <div class="h-[2px] w-12 bg-gradient-to-l from-transparent to-white/30 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form (Mobile & Desktop) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 relative bg-slate-50 dark:bg-slate-950 z-10">
            <!-- Mobile Header (Visible only on mobile) -->
            <div class="lg:hidden text-center mb-10 mt-4">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-20 h-20 mx-auto object-contain mb-5 drop-shadow-md">
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight">MAS-PKL</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 font-medium">Monitoring & Administrasi Siswa PKL</p>
            </div>

            <!-- Form Container -->
            <div class="w-full max-w-md">
                <!-- Desktop Welcome Text -->
                <div class="hidden lg:block mb-10">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">Selamat Datang 👋</h2>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Silakan masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-8 p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-sm flex items-start gap-3 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ username: '{{ old('username') }}', password: '', showPassword: false }">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Username / NIS / NIP</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                <i data-lucide="user" class="h-5 w-5"></i>
                            </div>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus x-model="username"
                                   class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-sm"
                                   placeholder="Masukkan username Anda">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Password</label>
                            <a href="#" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">Lupa Password?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                <i data-lucide="lock" class="h-5 w-5"></i>
                            </div>
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required x-model="password"
                                   class="w-full pl-12 pr-12 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-sm"
                                   placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none">
                                <i data-lucide="eye" x-show="!showPassword" class="h-5 w-5"></i>
                                <i data-lucide="eye-off" x-show="showPassword" class="h-5 w-5" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-slate-700 rounded bg-white dark:bg-slate-900 checked:bg-blue-600 checked:border-blue-600 dark:checked:bg-blue-500 dark:checked:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all cursor-pointer">
                                <i data-lucide="check" class="absolute text-white w-3.5 h-3.5 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                            </div>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Ingat Saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full relative group overflow-hidden rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-3.5 px-4 transition-all shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 flex items-center justify-center gap-2 focus:ring-4 focus:ring-blue-500/30 focus:outline-none mt-2">
                        <span class="relative z-10">Masuk ke Sistem</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 relative z-10 group-hover:translate-x-1.5 transition-transform duration-300"></i>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-16 text-center">
                    <p class="text-xs text-slate-500 dark:text-slate-500 font-medium tracking-wide">
                        &copy; {{ date('Y') }} SMK Negeri 1 Ciamis.<br class="sm:hidden" /> Hak Cipta Dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
