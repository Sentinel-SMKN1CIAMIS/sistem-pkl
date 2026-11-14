<?php

namespace App\Console\Commands;

use App\Services\PemetaanImportService;
use Illuminate\Console\Command;

class ImportPemetaanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkl:import-pemetaan 
                            {file? : Path file CSV pemetaan}
                            {--filter=* : Jurusan yang diizinkan (default: AKL, PBS, MPLB, PM, PBD, KLN, HTL, PPLG - DKV dilewati)}
                            {--password=smkn1ciamis : Password default akun pembimbing baru}
                            {--dry-run : Uji coba parsing tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor pemetaan pembimbing, DUDI, dan siswa dengan smart filter & disambiguasi nama guru';

    /**
     * Execute the console command.
     */
    public function handle(PemetaanImportService $service)
    {
        $file = $this->argument('file') ?? storage_path('app/pemetaan_2026_2027.csv');

        if (!file_exists($file)) {
            $this->error("Berkas tidak ditemukan: {$file}");
            return Command::FAILURE;
        }

        $allowedJurusan = $this->option('filter');
        if (empty($allowedJurusan)) {
            // Default sesuai instruksi: DKV diabaikan
            $allowedJurusan = ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG'];
        }

        $this->info("=== MEMULAI PARSING FILE PEMETAAN ===");
        $this->line("Berkas: <comment>{$file}</comment>");
        $this->line("Filter Jurusan: <info>" . implode(', ', $allowedJurusan) . "</info> (DKV dilewati)");

        $parsed = $service->parseCsv($file, $allowedJurusan);

        $items = $parsed['items'];
        $this->info("Total baris valid yang ditemukan : " . count($items));
        $this->info("Total baris dilewati (filter)    : " . $parsed['skipped_count']);

        if (count($items) === 0) {
            $this->warn("Tidak ada data siswa yang cocok dengan filter untuk diimpor.");
            return Command::SUCCESS;
        }

        // Preview sample
        $this->newLine();
        $this->info("--- CONTOH 5 DATA HASIL NORMALISASI ---");
        $sampleRows = array_slice($items, 0, 5);
        $headers = ['Wilayah', 'Pembimbing Asli', 'Kanonikal Guru', 'DUDI', 'Siswa', 'Kelas'];
        $rows = array_map(function($it) {
            return [
                $it['wilayah'],
                $it['pembimbing_raw'],
                $it['pembimbing_canonical'],
                $it['dudi_name'],
                $it['siswa_nama'],
                $it['kelas'],
            ];
        }, $sampleRows);
        $this->table($headers, $rows);

        if ($this->option('dry-run')) {
            $this->warn("Mode Dry-Run aktif. Tidak ada perubahan yang disimpan ke database.");
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info("=== MENGEKSEKUSI IMPOR KE DATABASE ===");
        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        $stats = $service->executeImport($items, [
            'default_password' => $this->option('password'),
        ]);

        $bar->finish();
        $this->newLine(2);

        $this->info("=== HASIL IMPOR PEMETAAN ===");
        $this->line("• Pembimbing Sekolah baru dibuat : <info>{$stats['pembimbing_created']}</info>");
        $this->line("• Pembimbing Sekolah cocok       : <comment>{$stats['pembimbing_matched']}</comment>");
        $this->line("• DUDI baru didaftarkan          : <info>{$stats['dudi_created']}</info>");
        $this->line("• DUDI yang sudah ada            : <comment>{$stats['dudi_matched']}</comment>");
        $this->line("• Siswa berhasil dipetakan (PKL) : <info>{$stats['siswa_mapped']}</info>");
        $this->line("• Siswa tidak ditemukan di DB    : <error>" . count($stats['siswa_not_found']) . "</error>");

        if (!empty($stats['siswa_not_found'])) {
            $this->newLine();
            $this->warn("Daftar Siswa yang belum cocok di database:");
            foreach (array_slice($stats['siswa_not_found'], 0, 10) as $s) {
                $this->line("  - {$s}");
            }
            if (count($stats['siswa_not_found']) > 10) {
                $this->line("  ... dan " . (count($stats['siswa_not_found']) - 10) . " lainnya.");
            }
        }

        return Command::SUCCESS;
    }
}

