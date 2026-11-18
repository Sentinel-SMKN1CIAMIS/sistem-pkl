<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Notifikasi;
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

        $checkoutUserIds = [];
        $jurnalUserIds = [];

        // 1. Pengingat Absen Pulang (Checkout) Relatif terhadap Jam Masuk masing-masing siswa (Shift Pagi/Siang/Malam)
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

            // Kirim pengingat jika sudah >= 7.5 jam sejak masuk dan masih dalam jendela 18 jam
            if ($hoursSinceClockIn >= 7.5 && $hoursSinceClockIn <= 18) {
                Notifikasi::create([
                    'from_user_id' => null,
                    'to_user_id' => $user->id,
                    'judul' => 'Waktunya Absen Pulang PKL',
                    'pesan' => 'Sudah lebih dari 7,5 jam sejak Anda absen masuk (' . $clockInAt->format('H:i') . '). Jangan lupa lakukan Absen Pulang!',
                    'tipe' => 'info',
                    'is_read' => false,
                ]);

                $absensi->update(['reminded_checkout_at' => $now]);
                $checkoutUserIds[] = $user->id;
            }
        }

        // 2. Pengingat Isi Jurnal Harian (Fleksibel setelah pulang atau malam hari)
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
                $shouldRemind = false;

                if ($absensi->waktu_pulang) {
                    $clockOutAt = Carbon::parse($tanggalStr . ' ' . $absensi->waktu_pulang);
                    if ($clockOutAt->lessThan($clockInAt)) {
                        $clockOutAt->addDay();
                    }
                    $minutesSinceOut = $clockOutAt->diffInMinutes($now, false);
                    // Kirim 30 menit s/d 24 jam setelah siswa absen pulang
                    if ($minutesSinceOut >= 30 && $minutesSinceOut <= 1440) {
                        $shouldRemind = true;
                    }
                } elseif ($tanggalStr === $todayStr && $clockInAt->hour < 15 && $now->format('H:i') >= '19:30') {
                    // Shift pagi/siang yang lupa absen pulang & belum isi jurnal pada malam hari
                    $shouldRemind = true;
                } elseif ($tanggalStr === $yesterdayStr && $now->format('H:i') >= '09:00' && $clockInAt->diffInHours($now, false) <= 24) {
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
            $webPush->dispatchBroadcast($checkoutUserIds, $checkoutPayload);
            $webPush->dispatchBroadcast($jurnalUserIds, $jurnalPayload);
        } else {
            $webPush->sendToUsers($checkoutUserIds, $checkoutPayload);
            $webPush->sendToUsers($jurnalUserIds, $jurnalPayload);
        }

        $this->info('Reminders processed: ' . count($checkoutUserIds) . ' checkout, ' . count($jurnalUserIds) . ' jurnal.');

        return self::SUCCESS;
    }
}
