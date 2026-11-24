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
    $defaultOffDays = is_array($siswa?->hari_libur_rutin) ? array_map('intval', $siswa->hari_libur_rutin) : [6, 7];
@endphp

@if($siswa)
<div id="reminder-settings-modal" 
     class="fixed inset-0 z-50 {{ $shouldShow ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md transition-opacity duration-300"
     data-configured="{{ $siswa->reminder_settings_configured ? '1' : '0' }}">
    
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xl p-6 sm:p-8 animate-in" onclick="event.stopPropagation()">
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

            {{-- 4. Hari Libur Rutin DUDI --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                        <i data-lucide="calendar-off" class="w-4 h-4 text-sky-500"></i>
                        Hari Libur Rutin DUDI
                    </label>
                    <span class="text-[10px] text-slate-400">Otomatis ditandai di kalender</span>
                </div>
                <div class="grid grid-cols-7 gap-1.5" id="routine-off-days-grid">
                    @foreach([1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'] as $iso => $dayLabel)
                        @php $isChecked = in_array($iso, $defaultOffDays, true); @endphp
                        <label class="cursor-pointer">
                            <input type="checkbox" name="hari_libur_rutin[]" value="{{ $iso }}" {{ $isChecked ? 'checked' : '' }} class="peer sr-only routine-off-checkbox">
                            <span class="flex items-center justify-center py-2 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 transition-colors select-none">
                                {{ $dayLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                    <span class="text-[10px] text-slate-400 font-medium">Pilihan cepat:</span>
                    <button type="button" onclick="setOffDaysPreset([6, 7])"
                            class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-sky-500 hover:text-sky-500 transition-colors cursor-pointer">
                        Sabtu &amp; Minggu
                    </button>
                    <button type="button" onclick="setOffDaysPreset([7])"
                            class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-sky-500 hover:text-sky-500 transition-colors cursor-pointer">
                        Minggu Saja
                    </button>
                    <button type="button" onclick="setOffDaysPreset([])"
                            class="px-2 py-0.5 text-[11px] font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-sky-500 hover:text-sky-500 transition-colors cursor-pointer">
                        Shift Rolling / Tidak Tetap
                    </button>
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

    function setOffDaysPreset(days) {
        document.querySelectorAll('.routine-off-checkbox').forEach(cb => {
            cb.checked = days.includes(parseInt(cb.value, 10));
        });
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
        setOffDaysPreset([6, 7]);
        document.getElementById('reminder-settings-form').requestSubmit();
    }

    async function submitReminderSettings(e) {
        e.preventDefault();
        const form = document.getElementById('reminder-settings-form');
        const btn = document.getElementById('btn-save-reminder');
        const msg = document.getElementById('reminder-msg');

        const selectedOffDays = Array.from(document.querySelectorAll('.routine-off-checkbox:checked'))
            .map(cb => parseInt(cb.value, 10));

        const data = {
            jam_notif_masuk: document.getElementById('jam_notif_masuk')?.value || '07:00',
            jam_notif_pulang: document.getElementById('jam_notif_pulang')?.value || '16:00',
            jam_notif_jurnal: document.getElementById('jam_notif_jurnal')?.value || '19:30',
            hari_libur_rutin: selectedOffDays,
        };

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
