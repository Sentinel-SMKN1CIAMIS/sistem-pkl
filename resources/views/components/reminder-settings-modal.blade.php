@props(['forceOpen' => false])

@php
    $user = auth()->user();
    $siswa = $user?->siswa;
    $shouldShow = $siswa
        && !$user->force_password_change
        && (!$siswa->reminder_settings_configured || $forceOpen);
    $defaultMasuk = $siswa?->jam_notif_masuk ?: '07:00';
    $defaultPulang = $siswa?->jam_notif_pulang ?: '16:00';
    $defaultJurnal = $siswa?->jam_notif_jurnal ?: '19:30';
@endphp

@if($siswa)
<div id="reminder-settings-modal" 
     class="fixed inset-0 z-50 {{ $shouldShow ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md transition-opacity duration-300"
     data-configured="{{ $siswa->reminder_settings_configured ? '1' : '0' }}">
    
    <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xl p-6 sm:p-8 animate-in overflow-hidden" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-sm">
                    <i data-lucide="bell-ring" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        Atur Alarm Pengingat PKL
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Sesuaikan jam alarm dengan jadwal jam kerja tempat DUDI Anda.
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeReminderModal(true)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="reminder-settings-form" class="space-y-5" onsubmit="submitReminderSettings(event)">
            @csrf

            {{-- 1. Alarm Masuk --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                        <i data-lucide="sunrise" class="w-4 h-4 text-amber-500"></i>
                        Alarm Absen Masuk
                    </label>
                    <input type="time" name="jam_notif_masuk" id="jam_notif_masuk" required value="{{ $defaultMasuk }}"
                           class="px-3 py-1.5 text-sm font-mono font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100 shadow-xs">
                </div>
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] text-slate-400 font-medium">Pilihan cepat:</span>
                    @foreach(['07:00', '07:30', '08:00', '08:30', '09:00'] as $preset)
                        <button type="button" onclick="setPreset('jam_notif_masuk', '{{ $preset }}')"
                                class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-amber-500 hover:text-amber-500 transition-all cursor-pointer">
                            {{ $preset }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 2. Alarm Pulang --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                        <i data-lucide="sunset" class="w-4 h-4 text-orange-500"></i>
                        Alarm Absen Pulang
                    </label>
                    <input type="time" name="jam_notif_pulang" id="jam_notif_pulang" required value="{{ $defaultPulang }}"
                           class="px-3 py-1.5 text-sm font-mono font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-100 shadow-xs">
                </div>
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] text-slate-400 font-medium">Pilihan cepat:</span>
                    @foreach(['15:30', '16:00', '16:30', '17:00', '17:30'] as $preset)
                        <button type="button" onclick="setPreset('jam_notif_pulang', '{{ $preset }}')"
                                class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-orange-500 hover:text-orange-500 transition-all cursor-pointer">
                            {{ $preset }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. Alarm Jurnal --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                        <i data-lucide="book-open" class="w-4 h-4 text-blue-500"></i>
                        Alarm Isi Jurnal
                    </label>
                    <input type="time" name="jam_notif_jurnal" id="jam_notif_jurnal" required value="{{ $defaultJurnal }}"
                           class="px-3 py-1.5 text-sm font-mono font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-100 shadow-xs">
                </div>
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] text-slate-400 font-medium">Pilihan cepat:</span>
                    @foreach(['17:00', '18:00', '19:30', '20:30'] as $preset)
                        <button type="button" onclick="setPreset('jam_notif_jurnal', '{{ $preset }}')"
                                class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-blue-500 hover:text-blue-500 transition-all cursor-pointer">
                            {{ $preset }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="reminder-msg" class="hidden text-xs font-medium text-center py-1"></div>

            {{-- Action buttons --}}
            <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-2">
                <button type="submit" id="btn-save-reminder"
                        class="w-full sm:flex-1 py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors cursor-pointer">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Alarm Pengingat
                </button>
                <button type="button" onclick="useDefaultReminder()"
                        class="w-full sm:w-auto py-3 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                    Gunakan Standar
                </button>
            </div>
            <div class="text-center">
                <button type="button" onclick="closeReminderModal(true)" class="text-[11px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                    Nanti Saja
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setPreset(inputId, time) {
        const el = document.getElementById(inputId);
        if (el) el.value = time;
    }

    function closeReminderModal(rememberSession = false) {
        const modal = document.getElementById('reminder-settings-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (rememberSession) {
                sessionStorage.setItem('dismissed_reminder_modal', '1');
            }
        }
    }

    function openReminderModal() {
        const modal = document.getElementById('reminder-settings-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function useDefaultReminder() {
        setPreset('jam_notif_masuk', '07:00');
        setPreset('jam_notif_pulang', '16:00');
        setPreset('jam_notif_jurnal', '19:30');
        document.getElementById('reminder-settings-form').requestSubmit();
    }

    async function submitReminderSettings(e) {
        e.preventDefault();
        const form = document.getElementById('reminder-settings-form');
        const btn = document.getElementById('btn-save-reminder');
        const msg = document.getElementById('reminder-msg');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        if (btn) btn.disabled = true;
        if (msg) {
            msg.className = 'text-xs font-medium text-center text-amber-500 py-1 animate-pulse';
            msg.textContent = 'Menyimpan jadwal alarm...';
            msg.classList.remove('hidden');
        }

        try {
            const res = await fetch("{{ route('siswa.profile.reminder-settings') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal menyimpan pengaturan.');
            }

            if (msg) {
                msg.className = 'text-xs font-medium text-center text-emerald-500 py-1';
                msg.textContent = '✨ Jadwal alarm berhasil disimpan!';
            }

            const modal = document.getElementById('reminder-settings-modal');
            if (modal) modal.setAttribute('data-configured', '1');

            setTimeout(() => {
                closeReminderModal(false);
                if (window.location.pathname.includes('/siswa/profile')) {
                    window.location.reload();
                }
            }, 800);
        } catch (err) {
            if (msg) {
                msg.className = 'text-xs font-medium text-center text-rose-500 py-1';
                msg.textContent = '⚠️ ' + err.message;
            }
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('reminder-settings-modal');
        if (modal) {
            const isConfigured = modal.getAttribute('data-configured') === '1';
            const isDismissed = sessionStorage.getItem('dismissed_reminder_modal') === '1';
            if (isConfigured || isDismissed) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
</script>
@endif
