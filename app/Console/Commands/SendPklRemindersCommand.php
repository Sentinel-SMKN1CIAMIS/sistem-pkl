<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Notifikasi;
use App\Models\Siswa;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPklRemindersCommand extends Command
{
    protected $signature = 'pkl:send-reminders {--queue : Dispatch Web Push batches to the Laravel queue instead of flushing inline}';

    protected $description = 'Send multi-shift-aware Web Push and in-app reminders for PKL checkout and daily journals';

    public function handle(WebPushService $webPush): int
    {
        $now = Carbon::now();
        $todayStr = $now->toDateString();
        $yesterdayStr = $now->copy()->subDay()->toDateString();

        $checkinUserIds = [];
        $checkoutUserIds = [];
        $jurnalUserIds = [];

        // 0. Pengingat Absen Masuk (Mendukung semua shift & jam alarm custom per siswa)
        $currentTime = $now->format('H:i');
        $todayCheckinNotifications = Notifikasi::where('judul', 'Jangan Lupa Absen Masuk PKL')
            ->whereDate('created_at', $todayStr)
            ->get(['to_user_id', 'created_at'])
            ->groupBy('to_user_id');

        $studentsToRemind = Siswa::with('user')
            ->where('status_pkl', 'sedang_pkl')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->whereDoesntHave('absensi', fn($q) => $q->whereDate('tanggal', $todayStr))
            // Kecualikan siswa shift malam yang masih aktif bertugas dari kemarin (belum absen pulang)
            ->whereDoesntHave('absensi', function ($q) use ($yesterdayStr) {
                $q->whereDate('tanggal', $yesterdayStr)
                    ->where('status', 'hadir')
                    ->whereNull('waktu_pulang');
            })
            ->get();

        foreach ($studentsToRemind as $siswa) {
            $user = $siswa->user;
            if (!$user) {
                continue;
            }

            // Lewati pengingat masuk jika hari ini adalah jadwal libur rutin siswa
            if ($siswa->isRoutineDayOff($now)) {
                continue;
            }

            // Periksa apakah waktu saat ini sudah mencapai jam alarm masuk pilihan siswa (jendela 0 s/d 120 menit setelah jam target)
            $targetMasukStr = $siswa->jam_notif_masuk ?: '07:00';
            $targetMasukAt = Carbon::parse($todayStr . ' ' . $targetMasukStr);
            $minutesSinceTarget = $targetMasukAt->diffInMinutes($now, false);

            if ($minutesSinceTarget < 0 || $minutesSinceTarget > 120) {
                continue;
            }

            // Cegah duplikat jika sudah pernah diingatkan pada/setelah jam alarm target hari ini
            $alreadyRemindedForTarget = $todayCheckinNotifications->get($user->id, collect())
                ->contains(fn($notif) => Carbon::parse($notif->created_at)->greaterThanOrEqualTo($targetMasukAt));

            if ($alreadyRemindedForTarget) {
                continue;
            }

            Notifikasi::create([
                'from_user_id' => null,
                'to_user_id' => $user->id,
                'judul' => 'Jangan Lupa Absen Masuk PKL',
                'pesan' => "Pengingat jadwal ({$targetMasukStr} WIB): Silakan lakukan Absen Masuk PKL hari ini saat tiba di tempat DUDI.",
                'tipe' => 'info',
                'is_read' => false,
            ]);

            $checkinUserIds[] = $user->id;
        }

        // 1. Pengingat Absen Pulang (Checkout) Relatif terhadap Jam Masuk atau Waktu Pilihan Siswa
        $pendingCheckouts = Absensi::with('siswa.user')
            ->where('status', 'hadir')
            ->whereNotNull('waktu_datang')
            ->whereNull('waktu_pulang')
            ->whereNull('reminded_checkout_at')
            ->whereBetween('tanggal', [$yesterdayStr, $todayStr])
            ->whereHas('siswa', fn($q) => $q->where('status_pkl', 'sedang_pkl'))
            ->get();

        foreach ($pendingCheckouts as $absensi) {
            $user = $absensi->siswa?->user;
            if (!$user || !$user->is_active) {
                continue;
            }

            $tanggalStr = Carbon::parse($absensi->tanggal)->toDateString();
            $clockInAt = Carbon::parse($tanggalStr . ' ' . $absensi->waktu_datang);
            $hoursSinceClockIn = $clockInAt->diffInMinutes($now, false) / 60;

            $customPulang = $absensi->siswa?->jam_notif_pulang;
            $isCustomPulangReached = $customPulang
                && $tanggalStr === $todayStr
                && $hoursSinceClockIn >= 1
                && $clockInAt->format('H:i') < $customPulang
                && $currentTime >= $customPulang;

            // Kirim pengingat jika sudah mencapai jam pilihan siswa ATAU >= 7.5 jam sejak masuk (dalam jendela 18 jam)
            if (($hoursSinceClockIn >= 7.5 || $isCustomPulangReached) && $hoursSinceClockIn <= 18) {
                Notifikasi::create([
                    'from_user_id' => null,
                    'to_user_id' => $user->id,
                    'judul' => 'Waktunya Absen Pulang PKL',
                    'pesan' => 'Sudah waktunya absen pulang' . ($customPulang ? " (jadwal: {$customPulang})" : ' (lebih dari 7,5 jam kerja)') . '. Jangan lupa lakukan Absen Pulang!',
                    'tipe' => 'info',
                    'is_read' => false,
                ]);

                $absensi->update(['reminded_checkout_at' => $now]);
                $checkoutUserIds[] = $user->id;
            }
        }

        // 2. Pengingat Isi Jurnal Harian (Fleksibel setelah pulang atau waktu pilihan malam hari)
        $attendanceForJurnal = Absensi::with('siswa.user')
            ->where('status', 'hadir')
            ->whereNotNull('waktu_datang')
            ->whereNull('reminded_jurnal_at')
            ->whereBetween('tanggal', [$yesterdayStr, $todayStr])
            ->whereHas('siswa', fn($q) => $q->where('status_pkl', 'sedang_pkl'))
            ->get();

        if ($attendanceForJurnal->isNotEmpty()) {
            $siswaIds = $attendanceForJurnal->pluck('siswa_id')->unique()->values();
            $existingJurnals = Jurnal::whereIn('siswa_id', $siswaIds)
                ->whereBetween('tanggal', [$yesterdayStr, $todayStr])
                ->get(['siswa_id', 'tanggal'])
                ->mapWithKeys(fn($j) => [
                    $j->siswa_id . '|' . Carbon::parse($j->tanggal)->toDateString() => true,
                ]);

            foreach ($attendanceForJurnal as $absensi) {
                $user = $absensi->siswa?->user;
                if (!$user || !$user->is_active) {
                    continue;
                }

                $tanggalStr = Carbon::parse($absensi->tanggal)->toDateString();
                if ($existingJurnals->has($absensi->siswa_id . '|' . $tanggalStr)) {
                    continue;
                }

                $clockInAt = Carbon::parse($tanggalStr . ' ' . $absensi->waktu_datang);
                $targetJurnal = $absensi->siswa?->jam_notif_jurnal ?: '19:30';
                $shouldRemind = false;

                if ($absensi->waktu_pulang) {
                    $clockOutAt = Carbon::parse($tanggalStr . ' ' . $absensi->waktu_pulang);
                    if ($clockOutAt->lessThan($clockInAt)) {
                        $clockOutAt->addDay();
                    }
                    $minutesSinceOut = $clockOutAt->diffInMinutes($now, false);
                    // Kirim jika sudah 30 menit setelah absen pulang ATAU sudah mencapai target jam jurnal hari ini
                    if (($minutesSinceOut >= 30 && $minutesSinceOut <= 1440) || ($tanggalStr === $todayStr && $currentTime >= $targetJurnal)) {
                        $shouldRemind = true;
                    }
                } elseif ($tanggalStr === $todayStr && $currentTime >= $targetJurnal && $currentTime > $clockInAt->format('H:i')) {
                    // Siswa yang belum isi jurnal saat jam alarm jurnal hari ini tercapai
                    $shouldRemind = true;
                } elseif ($tanggalStr === $yesterdayStr && $currentTime >= '09:00' && $clockInAt->diffInHours($now, false) <= 24) {
                    // Shift malam kemarin atau belum terisi sampai pagi berikutnya
                    $shouldRemind = true;
                }

                if ($shouldRemind) {
                    $tglLabel = Carbon::parse($tanggalStr)->translatedFormat('d M Y');
                    Notifikasi::create([
                        'from_user_id' => null,
                        'to_user_id' => $user->id,
                        'judul' => 'Jangan Lupa Isi Jurnal PKL',
                        'pesan' => "Kegiatan PKL tanggal {$tglLabel} belum tercatat di jurnal. Yuk isi sebentar sebelum beristirahat!",
                        'tipe' => 'warning',
                        'is_read' => false,
                    ]);

                    $absensi->update(['reminded_jurnal_at' => $now]);
                    $jurnalUserIds[] = $user->id;
                }
            }
        }

        $checkinPayload = [
            'title' => 'Jangan Lupa Absen Masuk PKL',
            'body' => 'Selamat pagi! Silakan lakukan Absen Masuk PKL hari ini saat tiba di tempat DUDI.',
            'url' => '/siswa/absensi',
            'tag' => 'pkl-reminder-checkin',
        ];

        $checkoutPayload = [
            'title' => 'Waktunya Absen Pulang PKL',
            'body' => 'Sudah lebih dari 7,5 jam sejak absen masuk. Jangan lupa lakukan Absen Pulang!',
            'url' => '/siswa/absensi',
            'tag' => 'pkl-reminder-checkout',
        ];

        $jurnalPayload = [
            'title' => 'Jangan Lupa Isi Jurnal PKL',
            'body' => 'Kegiatan PKL Anda belum dicatat di jurnal. Yuk lengkapi sebelum istirahat!',
            'url' => '/siswa/jurnal/create',
            'tag' => 'pkl-reminder-jurnal',
        ];

        if ($this->option('queue')) {
            $webPush->dispatchBroadcast($checkinUserIds, $checkinPayload);
            $webPush->dispatchBroadcast($checkoutUserIds, $checkoutPayload);
            $webPush->dispatchBroadcast($jurnalUserIds, $jurnalPayload);
        } else {
            $webPush->sendToUsers($checkinUserIds, $checkinPayload);
            $webPush->sendToUsers($checkoutUserIds, $checkoutPayload);
            $webPush->sendToUsers($jurnalUserIds, $jurnalPayload);
        }

        $this->info('Reminders processed: ' . count($checkinUserIds) . ' checkin, ' . count($checkoutUserIds) . ' checkout, ' . count($jurnalUserIds) . ' jurnal.');

        return self::SUCCESS;
    }
}
