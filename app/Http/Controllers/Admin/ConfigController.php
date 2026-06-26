<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\LaporanPkl;
use App\Models\PengajuanPkl;
use App\Models\Pesan;
use App\Models\Notifikasi;
use App\Models\ActivityLog;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = KonfigurasiSistem::all();
        
        $backupFiles = [];
        if (Storage::disk('local')->exists('backups')) {
            $files = Storage::disk('local')->files('backups');
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filename = basename($file);
                    $backupFiles[] = [
                        'filename' => $filename,
                        'size' => Storage::disk('local')->size($file),
                        'created_at' => \Carbon\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file)),
                    ];
                }
            }
            // Urutkan berdasarkan created_at terbaru
            usort($backupFiles, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);
        }

        // Baca log error dari laravel.log
        $errorLogs = [];
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            preg_match_all('/^\[(.*?)\] .*?\.ERROR: (.*)$/m', $logContent, $matches, PREG_SET_ORDER);
            
            // Urutkan dari yang terbaru (reverse)
            $matches = array_reverse($matches);
            
            // Ambil maksimal 10 error terakhir
            $matches = array_slice($matches, 0, 10);
            
            foreach ($matches as $match) {
                $errorLogs[] = [
                    'timestamp' => $match[1],
                    'message' => $match[2]
                ];
            }
        }

        return view('admin.config.index', compact('configs', 'backupFiles', 'errorLogs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'tahun_ajaran' => 'nullable|string|max:50',
            'kontak_admin' => 'nullable|string|max:20',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ], [
            'app_logo.image' => 'Berkas yang diunggah harus berupa gambar.',
            'app_logo.mimes' => 'Format gambar yang diperbolehkan hanya PNG, JPG, dan JPEG.',
            'app_logo.max' => 'Ukuran gambar maksimal adalah 4 Megabyte.',
        ]);

        // Simpan input teks biasa
        $textFields = $request->only(['app_name', 'tahun_ajaran', 'kontak_admin']);
        foreach ($textFields as $key => $value) {
            if ($request->has($key)) {
                KonfigurasiSistem::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Tangani unggahan logo jika ada
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke disk 'public' di folder 'logos/'
            $path = $file->storeAs('logos', $fileName, 'public');
            $logoUrl = '/storage/' . $path;

            // Cari logo lama dan hapus berkasnya dari server jika menggunakan disk 'public'
            $oldConfig = KonfigurasiSistem::where('key', 'app_logo_url')->first();
            if ($oldConfig && $oldConfig->value) {
                // Konversi URL '/storage/logos/logo_xxxx.png' menjadi relative path 'logos/logo_xxxx.png'
                $oldPath = str_replace('/storage/', '', $oldConfig->value);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan path URL baru ke database
            KonfigurasiSistem::updateOrCreate(
                ['key' => 'app_logo_url'],
                ['value' => $logoUrl]
            );
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }

    /**
     * Generate database SQL backup and download (Supports MySQL and PostgreSQL)
     */
    public function backup()
    {
        try {
            $driver = DB::getDriverName();
            $sql = "-- Sistem Informasi PKL - Database Backup\n";
            $sql .= "-- Date: " . now()->toDateTimeString() . "\n";
            $sql .= "-- Driver: " . $driver . "\n\n";

            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $tableKey = 'Tables_in_' . $dbName;
                $tables = DB::select('SHOW TABLES');
                
                $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

                foreach ($tables as $table) {
                    $tableName = $table->$tableKey;

                    // Drop table if exists
                    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                    // Create Table statement
                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                    // Insert data statements
                    $rows = DB::table($tableName)->get();
                    if ($rows->isNotEmpty()) {
                        foreach ($rows as $row) {
                            $rowArray = (array)$row;
                            $keys = array_map(fn($k) => "`{$k}`", array_keys($rowArray));
                            $values = array_map(function($v) {
                                if (is_null($v)) {
                                    return 'NULL';
                                }
                                $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], addslashes($v));
                                return "'{$escaped}'";
                            }, array_values($rowArray));

                            $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sql .= "\n";
                    }
                }
                $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            } elseif ($driver === 'pgsql') {
                $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
                
                $sql .= "-- Disabling triggers/constraints to bypass foreign keys\n";
                $sql .= "SET CONSTRAINTS ALL DEFERRED;\n\n";

                foreach ($tables as $table) {
                    $tableName = $table->table_name;

                    $sql .= "DROP TABLE IF EXISTS \"{$tableName}\" CASCADE;\n";

                    // Fetch columns info to reconstruct CREATE TABLE
                    $columnsInfo = DB::select("
                        SELECT column_name, data_type, is_nullable, column_default 
                        FROM information_schema.columns 
                        WHERE table_name = ? AND table_schema = 'public'
                        ORDER BY ordinal_position
                    ", [$tableName]);

                    $colDefs = [];
                    foreach ($columnsInfo as $col) {
                        $def = "\"{$col->column_name}\" {$col->data_type}";
                        if ($col->is_nullable === 'NO') {
                            $def .= " NOT NULL";
                        }
                        if (!is_null($col->column_default)) {
                            $def .= " DEFAULT {$col->column_default}";
                        }
                        $colDefs[] = $def;
                    }

                    // Check for primary keys
                    $primaryKeys = DB::select("
                        SELECT kcu.column_name 
                        FROM information_schema.table_constraints tc 
                        JOIN information_schema.key_column_usage kcu 
                          ON tc.constraint_name = kcu.constraint_name 
                          AND tc.table_schema = kcu.table_schema
                        WHERE tc.constraint_type = 'PRIMARY KEY' 
                          AND tc.table_name = ?
                    ", [$tableName]);

                    if (!empty($primaryKeys)) {
                        $pkCols = array_map(fn($k) => "\"{$k->column_name}\"", $primaryKeys);
                        $colDefs[] = "PRIMARY KEY (" . implode(', ', $pkCols) . ")";
                    }

                    $sql .= "CREATE TABLE \"{$tableName}\" (\n  " . implode(",\n  ", $colDefs) . "\n);\n\n";

                    // Insert data statements
                    $rows = DB::table($tableName)->get();
                    if ($rows->isNotEmpty()) {
                        foreach ($rows as $row) {
                            $rowArray = (array)$row;
                            $keys = array_map(fn($k) => "\"{$k}\"", array_keys($rowArray));
                            $values = array_map(function($v) {
                                if (is_null($v)) {
                                    return 'NULL';
                                }
                                $escaped = str_replace("'", "''", $v);
                                return "'{$escaped}'";
                            }, array_values($rowArray));

                            $sql .= "INSERT INTO \"{$tableName}\" (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sql .= "\n";
                    }
                }
            } else {
                throw new \Exception("Database driver '{$driver}' tidak didukung untuk pencadangan otomatis.");
            }

            $filename = 'backup_pkl_' . date('Y_m_d_His') . '.sql';

            // Simpan file ke server lokal (storage/app/backups/)
            Storage::disk('local')->put('backups/' . $filename, $sql);

            session([
                'database_backed_up' => true,
                'last_backup_file' => $filename
            ]);

            return back()->with('success', 'Cadangan database berhasil dibuat dan disimpan di server. Silakan unduh file cadangan tersebut.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Backup Error', [
                'driver' => $driver ?? 'not defined',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mencadangkan database: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file from server storage
     */
    public function downloadBackup($filename)
    {
        $path = 'backups/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File cadangan tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($path);
        return response()->download($fullPath);
    }

    /**
     * Delete backup file from server storage
     */
    public function deleteBackup($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            
            // Jika file yang dihapus adalah file backup terakhir dari session, hapus juga sessionnya
            if (session('last_backup_file') === $filename) {
                session()->forget(['last_backup_file', 'database_backed_up']);
            }
            
            return back()->with('success', 'File cadangan database berhasil dihapus dari server.');
        }

        return back()->with('error', 'File cadangan tidak ditemukan.');
    }

    /**
     * Clear all transactional data and reset student placements (Supports MySQL and PostgreSQL)
     */
    public function wipe(Request $request)
    {
        try {
            if (!session('database_backed_up')) {
                return back()->with('error', 'Gagal membersihkan data: Anda wajib mencadangkan (backup) database terlebih dahulu!');
            }

            if (!$request->password || !Hash::check($request->password, $request->user()->password)) {
                return back()->with('error', 'Gagal membersihkan data: Konfirmasi password salah atau tidak diisi!');
            }

            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Absensi::truncate();
                Jurnal::truncate();
                LaporanPkl::truncate();
                PengajuanPkl::truncate();
                Pesan::truncate();
                Notifikasi::truncate();
                ActivityLog::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                // Reset Siswa placements
                Siswa::query()->update([
                    'dudi_id' => null,
                    'pembimbing_sekolah_id' => null,
                    'pembimbing_dudi_id' => null,
                    'pembimbing_dudi_nama' => null,
                    'pembimbing_dudi_jabatan' => null,
                    'unit_pekerjaan' => null,
                    'status_pkl' => 'belum_mulai',
                ]);
            } else {
                DB::transaction(function () use ($driver) {
                    if ($driver === 'pgsql') {
                        // PostgreSQL: Truncate tables with CASCADE in one statement to handle constraints
                        DB::statement('TRUNCATE TABLE absensis, jurnals, laporan_pkls, pengajuan_pkls, pesans, notifikasis, activity_logs RESTART IDENTITY CASCADE;');
                    } else {
                        // Fallback delete
                        Absensi::query()->delete();
                        Jurnal::query()->delete();
                        LaporanPkl::query()->delete();
                        PengajuanPkl::query()->delete();
                        Pesan::query()->delete();
                        Notifikasi::query()->delete();
                        ActivityLog::query()->delete();
                    }

                    // Reset Siswa placements
                    Siswa::query()->update([
                        'dudi_id' => null,
                        'pembimbing_sekolah_id' => null,
                        'pembimbing_dudi_id' => null,
                        'pembimbing_dudi_nama' => null,
                        'pembimbing_dudi_jabatan' => null,
                        'unit_pekerjaan' => null,
                        'status_pkl' => 'belum_mulai',
                    ]);
                });
            }

            session()->forget('database_backed_up');

            return back()->with('success', 'Data transaksi berhasil dibersihkan dan sistem disiapkan untuk tahun ajaran baru.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan data transaksi: ' . $e->getMessage());
        }
    }
}
