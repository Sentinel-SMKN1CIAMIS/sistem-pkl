<script>
    // Tangkap event sedini mungkin sebelum Alpine dimuat
    window.deferredPwaPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window.deferredPwaPrompt = e;
        // Jika event ini muncul, berarti aplikasi pasti BELUM di-install (atau sudah di-uninstall)
        localStorage.removeItem('pwa_installed_flag');
        window.dispatchEvent(new Event('pwa-ready'));
    });
</script>

<div x-data="pwaPrompt()" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-10"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-10"
     class="fixed bottom-4 left-4 right-4 z-[100] md:max-w-sm md:left-1/2 md:-translate-x-1/2 md:bottom-8"
     style="display: none;">
    
    <div class="bg-[#1e293b] rounded-2xl shadow-2xl p-4 border border-slate-700/50 relative overflow-hidden">
        <!-- Close button -->
        <button @click="dismiss()" class="absolute top-3 right-3 text-slate-400 hover:text-white transition-colors z-10 bg-[#1e293b] rounded-full p-1">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>

        <div class="flex items-start gap-4 mb-4 mt-1">
            <div class="w-14 h-14 flex items-center justify-center shrink-0">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-14 h-14 object-cover rounded-xl shadow-sm">
            </div>
            <div class="flex-1 pr-6">
                <h3 class="text-white font-bold text-lg leading-tight mb-1">Install {{ config('app.name', 'Sistem PKL') }}</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Akses lebih cepat langsung dari layar utama HP kamu.</p>
            </div>
        </div>

        <template x-if="!isIos">
            <button @click="install()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                <i data-lucide="download" class="w-5 h-5"></i>
                Install Sekarang
            </button>
        </template>
        
        <template x-if="isIos">
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3 text-center">
                <p class="text-xs text-blue-100 leading-relaxed">
                    Untuk install di iOS:<br>
                    Tap ikon <b class="text-white">Share</b> di bawah, lalu pilih<br>
                    <b class="text-white">"Add to Home Screen"</b>
                </p>
            </div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pwaPrompt', () => ({
            show: false,
            deferredPrompt: null,
            isIos: false,

            init() {
                const userAgent = window.navigator.userAgent.toLowerCase();
                this.isIos = /iphone|ipad|ipod/.test(userAgent);
                const isInStandaloneMode = ('standalone' in window.navigator) && (window.navigator.standalone) || window.matchMedia('(display-mode: standalone)').matches;
                const isAlreadyInstalled = localStorage.getItem('pwa_installed_flag');

                // Jangan tampilkan jika dalam mode standalone, ATAU sedang dalam proses OS installing
                if (isInStandaloneMode || isAlreadyInstalled) {
                    return;
                }

                if (window.deferredPwaPrompt) {
                    this.deferredPrompt = window.deferredPwaPrompt;
                } else {
                    window.addEventListener('pwa-ready', () => {
                        this.deferredPrompt = window.deferredPwaPrompt;
                    });
                }

                // Selalu tampilkan setelah 1.5 detik jika belum di mode standalone
                setTimeout(() => {
                    // Pastikan tidak tampil jika flag sudah diset oleh tab lain
                    if (!localStorage.getItem('pwa_installed_flag')) {
                        this.show = true;
                    }
                }, 1500);

                window.addEventListener('appinstalled', () => {
                    localStorage.setItem('pwa_installed_flag', 'true');
                    this.show = false;
                    this.deferredPrompt = null;
                });
            },

            async install() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        // Tandai di localStorage bahwa OS sedang memproses instalasi
                        localStorage.setItem('pwa_installed_flag', 'true');
                        
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sedang Menginstall...',
                                text: 'Aplikasi sedang ditambahkan ke layar utama. Tergantung tipe HP Anda, proses ini memakan waktu 5-30 detik. Silakan cek layar utama HP Anda sebentar lagi.',
                                showConfirmButton: true,
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    }
                    this.deferredPrompt = null;
                    this.show = false;
                } else {
                    // Fallback jika browser tidak memicu event (misalnya sudah di-install, Firefox, Incognito, In-App Browser)
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Status Pemasangan',
                            html: `
                                <div class="text-sm text-left text-slate-600">
                                    <p class="mb-3">Sepertinya <b>aplikasi sudah ter-install</b> di perangkat Anda. Silakan cek layar utama (Home Screen) HP Anda.</p>
                                    <p class="mb-2">Jika belum ada, browser yang Anda gunakan saat ini mungkin tidak mendukung instalasi otomatis 1-klik. Anda bisa memasangnya secara manual:</p>
                                    <ol class="list-decimal ml-4">
                                        <li class="mb-1">Tap menu <b>Titik Tiga (⋮)</b> atau <b>Share</b> di pojok browser.</li>
                                        <li>Pilih <b>"Add to Home screen"</b> atau <b>"Install app"</b>.</li>
                                    </ol>
                                </div>
                            `,
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Baik, Mengerti'
                        });
                    } else {
                        alert('Jika aplikasi sudah ter-install, silakan cek Home Screen Anda. Jika belum, tap menu "Titik Tiga" (⋮) di browser, lalu pilih "Add to Home screen".');
                    }
                }
            },

            dismiss() {
                // Hanya menyembunyikan di halaman ini saat ini. Jika di-refresh, akan muncul lagi.
                this.show = false;
            }
        }));
    });
</script>
