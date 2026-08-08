<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer data dari SQLite ke database Production (MySQL/Postgres)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetConnection = env('DB_TRANSFER_TARGET', 'mysql_prod');

        $this->info('--- Memulai Transfer Database ---');
        $this->info("Target Koneksi: {$targetConnection}");

        // Daftar tabel terurut berdasarkan dependensi (foreign key)
        $tables = [
            'users',
            'customer_tiers',
            'customers',
            'outlets',
            'employees',
            'products',
            'discount_rules',
            'product_outlet',
            'pos_transactions',
            'transaction_items',
            'password_reset_tokens',
            'personal_access_tokens'
        ];

        // Pastikan target koneksi terkonfigurasi
        try {
            $driver = DB::connection($targetConnection)->getDriverName();
            $this->info("Driver target terdeteksi: {$driver}");
        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke koneksi '{$targetConnection}'. Pastikan env DB_PROD_* sudah dikonfigurasi di file .env Anda.");
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        // Nonaktifkan constraint foreign key pada database target
        $this->info('Menonaktifkan check foreign key...');
        if ($driver === 'mysql') {
            DB::connection($targetConnection)->statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Truncate CASCADE akan digunakan untuk pembersihan, 
            // dan kita set trigger disable jika diperlukan, atau jalankan tanpa check
            DB::connection($targetConnection)->statement('SET CONSTRAINTS ALL DEFERRED;');
        }

        foreach ($tables as $table) {
            // Cek apakah tabel ada di SQLite lokal
            if (!Schema::connection('sqlite')->hasTable($table)) {
                $this->warn("Tabel '{$table}' tidak ditemukan di SQLite, dilewati.");
                continue;
            }

            $this->info("Mentransfer tabel: {$table}...");

            // Kosongkan data lama di database target
            if ($driver === 'mysql') {
                DB::connection($targetConnection)->table($table)->truncate();
            } elseif ($driver === 'pgsql') {
                // Gunakan TRUNCATE CASCADE untuk PostgreSQL agar menghapus data berelasi
                DB::connection($targetConnection)->statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
            } else {
                DB::connection($targetConnection)->table($table)->truncate();
            }

            // Ambil seluruh baris data dari SQLite lokal
            $rows = DB::connection('sqlite')->table($table)->get()->map(function ($row) {
                return (array) $row;
            })->toArray();

            // Masukkan data ke target database
            if (!empty($rows)) {
                $count = count($rows);
                
                // Chunk insert jika datanya sangat banyak agar tidak melebihi limit query parameter
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    DB::connection($targetConnection)->table($table)->insert($chunk);
                }
                
                $this->line("-> Berhasil mentransfer {$count} baris.");
            } else {
                $this->line("-> Tabel kosong.");
            }
        }

        // Aktifkan kembali check foreign key pada database target
        $this->info('Mengaktifkan kembali check foreign key...');
        if ($driver === 'mysql') {
            DB::connection($targetConnection)->statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info('==============================================');
        $this->info('Selesai! Seluruh data berhasil ditransfer.');
        $this->info('==============================================');

        return 0;
    }
}
