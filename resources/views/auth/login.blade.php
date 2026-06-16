<x-guest-layout>
    <div class="glass-card w-full max-w-md mx-auto p-8 relative z-20">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto object-contain mb-4 rounded-xl">
            <h1 class="text-3xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-blue-600 dark:from-blue-300 dark:to-blue-500 drop-shadow-sm transition-all duration-300 hover:scale-[1.02] hover:drop-shadow-md cursor-default">MAS-PKL</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">Monitoring & Administrasi Siswa PKL</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                <ul class="list-disc list-inside">
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
                <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username / NIS / NIP</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user" class="h-5 w-5 text-slate-500 dark:text-slate-400"></i>
                    </div>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus x-model="username"
                           class="w-full pl-10 pr-3 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                           placeholder="Masukkan username anda">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="h-5 w-5 text-slate-500 dark:text-slate-400"></i>
                    </div>
                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required x-model="password"
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                           placeholder="••••••••">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:text-slate-300 transition-colors">
                        <i data-lucide="eye" x-show="!showPassword" x-cloak class="h-5 w-5"></i>
                        <i data-lucide="eye-off" x-show="showPassword" x-cloak class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Ingat Saya</span>
                </label>
                <a href="#" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Lupa Password?</a>
            </div>

            <!-- Submit -->
            <x-button class="w-full" icon="arrow-right-circle" x-bind:error-text="!username && !password ? 'isi username/NIS/NIP dan password' : (!username ? 'Isi username/NIS/NIP' : 'Isi Password')">
                Masuk
            </x-button>
        </form>

        <div class="mt-8 text-center border-t border-slate-200/50 dark:border-slate-700/50 pt-6">
            <p class="text-xs text-slate-500 dark:text-slate-400">©2026 SMK Negeri 1. All rights reserved.</p>
        </div>
    </div>
</x-guest-layout>
