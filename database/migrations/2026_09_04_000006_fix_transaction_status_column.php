<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status dari ENUM ke VARCHAR(50) agar mendukung nilai:
        // 'Menunggu', 'Dipinjam', 'Dikembalikan', 'Ditolak'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Dipinjam'");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (hati-hati: data 'Menunggu'/'Ditolak' akan hilang)
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('Dipinjam', 'Dikembalikan') NOT NULL DEFAULT 'Dipinjam'");
    }
};
