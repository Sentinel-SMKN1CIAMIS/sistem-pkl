<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Ubah Password') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

        @if(auth()->user()->force_password_change)
        <!-- Info Message -->
        <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="h-5 w-5 text-blue-400 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm text-blue-300 font-medium">Pengubahan Password Wajib</p>
                    <p class="text-xs text-blue-300/80 mt-1">Anda harus mengubah password pada login pertama untuk keamanan akun Anda.</p>
                </div>
            </div>
        </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.change-password.update') }}" class="space-y-6" x-data="{ password: '', password_confirmation: '', showPassword: false, showConfirmation: false }">
            @csrf
            @method('PATCH')

            <!-- Password Requirements Info -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 shadow-sm">
                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Kriteria Password Aman:</p>
                <ul class="text-xs space-y-2.5">
                    <li class="flex items-center gap-2.5 transition-colors duration-200" :class="password.length >= 8 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span class="transition-all duration-200" :class="password.length >= 8 ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Minimal 8 karakter
                    </li>
                    <li class="flex items-center gap-2.5 transition-colors duration-200" :class="/[A-Z]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span class="transition-all duration-200" :class="/[A-Z]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Mengandung huruf besar (A-Z)
                    </li>
                    <li class="flex items-center gap-2.5 transition-colors duration-200" :class="/[a-z]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span class="transition-all duration-200" :class="/[a-z]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Mengandung huruf kecil (a-z)
                    </li>
                    <li class="flex items-center gap-2.5 transition-colors duration-200" :class="/[0-9]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span class="transition-all duration-200" :class="/[0-9]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Mengandung angka (0-9)
                    </li>
                    <li class="flex items-center gap-2.5 transition-colors duration-200" :class="/[@$!%*?&#-_]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span class="transition-all duration-200" :class="/[@$!%*?&#-_]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Mengandung karakter spesial (contoh: @$!%*?&#-_)
                    </li>
                </ul>
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="h-5 w-5 text-slate-500 dark:text-slate-400"></i>
                    </div>
                    <input 
                        id="password" 
                        name="password" 
                        :type="showPassword ? 'text' : 'password'" 
                        required 
                        x-model="password"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                        placeholder="••••••••"
                    >
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                    >
                        <i data-lucide="eye" x-show="!showPassword" x-cloak class="h-5 w-5"></i>
                        <i data-lucide="eye-off" x-show="showPassword" x-cloak class="h-5 w-5"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="h-5 w-5 text-slate-500 dark:text-slate-400"></i>
                    </div>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        :type="showConfirmation ? 'text' : 'password'" 
                        required 
                        x-model="password_confirmation"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                        placeholder="••••••••"
                    >
                    <button 
                        type="button" 
                        @click="showConfirmation = !showConfirmation" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                    >
                        <i data-lucide="eye" x-show="!showConfirmation" x-cloak class="h-5 w-5"></i>
                        <i data-lucide="eye-off" x-show="showConfirmation" x-cloak class="h-5 w-5"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button 
                type="submit"
                class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-xl transition-all duration-200 flex items-center justify-center gap-2"
            >
                <i data-lucide="check-circle" class="h-5 w-5"></i>
                Ubah Password
            </button>
        </form>

            </div>
        </div>
    </div>
</x-app-layout>
