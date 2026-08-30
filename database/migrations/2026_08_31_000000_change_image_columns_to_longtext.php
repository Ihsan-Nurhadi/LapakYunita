<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY image LONGTEXT NULL');
            DB::statement('ALTER TABLE employees MODIFY photo LONGTEXT NULL');
            DB::statement('ALTER TABLE outlets MODIFY image LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE products ALTER COLUMN image TYPE TEXT');
            DB::statement('ALTER TABLE employees ALTER COLUMN photo TYPE TEXT');
            DB::statement('ALTER TABLE outlets ALTER COLUMN image TYPE TEXT');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY image VARCHAR(255) NULL');
            DB::statement('ALTER TABLE employees MODIFY photo VARCHAR(255) NULL');
            DB::statement('ALTER TABLE outlets MODIFY image VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE products ALTER COLUMN image TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE employees ALTER COLUMN photo TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE outlets ALTER COLUMN image TYPE VARCHAR(255)');
        }
    }
};
