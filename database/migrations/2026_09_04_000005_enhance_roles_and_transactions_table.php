<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('peminjam')->after('password');
            }
        });

        // 2. Modifikasi tabel members (relasi ke users)
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        // 3. Modifikasi tabel books (tambah cover image)
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'cover')) {
                $table->string('cover')->nullable()->after('description');
            }
        });

        // 4. Modifikasi tabel transactions (dukung status Menunggu, Ditolak, dan admin_notes)
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('notes');
            }
        });

        // Ubah kolom status dari ENUM ke VARCHAR agar mendukung nilai 'Menunggu' dan 'Ditolak'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Dipinjam'");

        // 5. Tabel settings untuk konfigurasi dinamis admin
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'cover')) {
                $table->dropColumn('cover');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
