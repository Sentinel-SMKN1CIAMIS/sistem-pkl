@php
    $allowedNis = \App\Http\Controllers\Siswa\JurnalAiAssistantController::getAllowedNis();
    $isSecretAiUser = (auth()->user()?->username === $allowedNis) 
        || (auth()->user()?->siswa?->nis === $allowedNis);
@endphp

@if($isSecretAiUser)
    <div class="md:col-span-2 p-4 rounded-2xl bg-gradient-to-r from-violet-500/10 via-indigo-500/10 to-blue-500/10 border border-indigo-500/20 shadow-sm relative overflow-hidden transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-500/30">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                </span>
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        Smart Journal Assistant
                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">Beta</span>
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tulis catatan kasar santai atau biarkan AI membuat draf jurnal natural secara otomatis.</p>
                </div>
            </div>
            <span id="ai-loading-indicator" class="hidden text-xs font-medium text-indigo-600 dark:text-indigo-400 animate-pulse flex items-center gap-1.5">
                <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Meracik jurnal...
            </span>
        </div>

        <div class="space-y-3">
            <textarea id="ai-rough-notes" rows="2"
                class="w-full px-3.5 py-2 text-sm bg-white/80 dark:bg-slate-900/80 border border-indigo-200/60 dark:border-indigo-900/50 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 placeholder:text-slate-400 resize-none transition-all"
                placeholder="Tulis kasar kegiatan hari ini (misal: 'tadi benerin layout navbar yg numpuk di hp, terus nyoba api list siswa')..."></textarea>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-ai-refine" onclick="generateAiJurnal('refine')"
                        class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                        <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
                        <span>✨ Susun & Rapikan</span>
                    </button>
                    <button type="button" id="btn-ai-auto" onclick="generateAiJurnal('auto')"
                        class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                        <i data-lucide="dices" class="w-3.5 h-3.5 text-indigo-500"></i>
                        <span>🎲 Auto Generate</span>
                    </button>
                </div>
                <span id="ai-status-msg" class="text-xs font-medium text-slate-500"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        async function generateAiJurnal(mode) {
            const roughInput = document.getElementById('ai-rough-notes');
            const roughText = roughInput ? roughInput.value.trim() : '';
            const loading = document.getElementById('ai-loading-indicator');
            const statusMsg = document.getElementById('ai-status-msg');
            const btnRefine = document.getElementById('btn-ai-refine');
            const btnAuto = document.getElementById('btn-ai-auto');
            const kegiatanInput = document.getElementById('kegiatan');
            const catatanInput = document.getElementById('catatan');

            if (mode === 'refine' && !roughText) {
                if (statusMsg) {
                    statusMsg.textContent = 'Ketik catatan kasar dulu, atau pilih tombol "Auto Generate".';
                    statusMsg.className = 'text-xs font-medium text-amber-500';
                }
                roughInput?.focus();
                return;
            }

            if (loading) loading.classList.remove('hidden');
            if (btnRefine) btnRefine.disabled = true;
            if (btnAuto) btnAuto.disabled = true;
            if (statusMsg) {
                statusMsg.textContent = 'Sedang menghubungi Groq AI...';
                statusMsg.className = 'text-xs font-medium text-indigo-500';
            }

            try {
                const response = await fetch("{{ route('siswa.jurnal.ai-assist') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mode: mode,
                        catatan_kasar: roughText
                    })
                });

                const res = await response.json();

                if (!response.ok || !res.success) {
                    throw new Error(res.message || 'Gagal memproses jurnal.');
                }

                if (res.data) {
                    if (kegiatanInput && res.data.kegiatan) {
                        kegiatanInput.value = res.data.kegiatan;
                        kegiatanInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (catatanInput && res.data.catatan) {
                        catatanInput.value = res.data.catatan;
                        catatanInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    if (statusMsg) {
                        statusMsg.textContent = '✨ Jurnal berhasil diracik! Silakan periksa di bawah.';
                        statusMsg.className = 'text-xs font-medium text-emerald-500';
                    }
                }
            } catch (err) {
                console.error(err);
                if (statusMsg) {
                    statusMsg.textContent = '⚠️ ' + (err.message || 'Terjadi kesalahan.');
                    statusMsg.className = 'text-xs font-medium text-rose-500';
                }
            } finally {
                if (loading) loading.classList.add('hidden');
                if (btnRefine) btnRefine.disabled = false;
                if (btnAuto) btnAuto.disabled = false;
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        }
    </script>
    @endpush
@endif
