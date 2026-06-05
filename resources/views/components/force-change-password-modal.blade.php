@if($forcePasswordChange)
<div x-data="{ open: true, password: '', password_confirmation: '', showPassword: false, errors: {} }" 
     x-init="open = true"
     class="fixed inset-0 z-50 flex items-center justify-center">
    
    <!-- Backdrop -->
    <div @click="open = false" x-show="open" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    
    <!-- Modal -->
    <div x-show="open" class="relative z-50 w-full max-w-md mx-auto p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 relative">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                    Ubah Password
                </h2>
                <p class="text-blue-100 text-sm mt-1">Silakan ubah password default Anda sebelum melanjutkan</p>
            </div>

            <!-- Alert Info -->
            <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
                <div class="flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-200">Pengubahan Password Wajib</p>
                        <p class="text-xs text-blue-800 dark:text-blue-300 mt-1">Ini adalah pertama kali Anda login. Untuk keamanan akun, password harus diubah.</p>
                    </div>
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide mb-3">Requirement Password:</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Minimal 8 karakter
                    </li>
                    <li class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Huruf besar (A-Z) & kecil (a-z)
                    </li>
                    <li class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Angka (0-9) & karakter spesial (@$!%*?&)
                    </li>
                </ul>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="px-6 py-6 space-y-4">
                @csrf
                @method('PATCH')

                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            x-model="password"
                            required 
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                        >
                            <i data-lucide="eye" x-show="!showPassword" class="w-5 h-5"></i>
                            <i data-lucide="eye-off" x-show="showPassword" class="w-5 h-5" x-cloak></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            x-model="password_confirmation"
                            required 
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 transition-all"
                            placeholder="••••••••"
                        >
                    </div>
                    @error('password_confirmation')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2 mt-6"
                >
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Ubah Password
                </button>
            </form>

            <!-- Footer -->
            <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Anda tidak bisa keluar sampai password diubah</p>
            </div>
        </div>
    </div>
</div>

<script>
function submitForm() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch("{{ route('auth.change-password.update') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Validasi gagal');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    });
}

// Prevent form submission default and use fetch
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    }
});
</script>
@endif
