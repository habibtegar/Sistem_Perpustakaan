<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LibraryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Administrator Bawaan
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Perpustakaan',
                'email' => 'admin@perpustakaan.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Buat Akun Peminjam Bawaan
        $peminjamUser = User::firstOrCreate(
            ['username' => 'peminjam'],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'peminjam@perpustakaan.com',
                'password' => Hash::make('peminjam123'),
                'role' => 'peminjam',
            ]
        );

        // 3. Konfigurasi Pengaturan Bawaan
        Setting::set('fine_per_day', 1000, 'Tarif denda keterlambatan per hari (Rp)');
        Setting::set('default_loan_days', 7, 'Durasi standar peminjaman (Hari)');
        Setting::set('library_name', 'Sistem Manajemen Perpustakaan Digital', 'Nama Perpustakaan');
        Setting::set('library_address', 'Jl. Pendidikan No. 123, Kampus Merdeka', 'Alamat Perpustakaan');

        // 4. Kategori Bawaan
        $defaultCategories = [
            ['name' => 'Pelajaran', 'description' => 'Buku teks dan referensi kurikulum sekolah.'],
            ['name' => 'Cerita Rakyat', 'description' => 'Kisah rakyat, dongeng nusantara, dan legenda tradisional.'],
            ['name' => 'Novel', 'description' => 'Karya fiksi prosa naratif yang panjang.'],
            ['name' => 'Komik', 'description' => 'Buku cerita bergambar dan komik edukasi.'],
            ['name' => 'Sains & Teknologi', 'description' => 'Buku sains, komputer, dan eksplorasi ilmu pengetahuan.'],
            ['name' => 'Lainnya', 'description' => 'Koleksi umum dan berbagai topik lainnya.'],
        ];

        foreach ($defaultCategories as $catData) {
            Category::firstOrCreate(['name' => $catData['name']], $catData);
        }

        // 5. Hubungkan buku lama ke tabel categories & beri stok awal
        $categoriesMap = Category::pluck('id', 'name')->toArray();
        foreach (Book::all() as $book) {
            $updated = false;
            if (!$book->stock || $book->stock < 0) {
                $book->stock = 5;
                $updated = true;
            }
            if (!$book->category_id && isset($categoriesMap[$book->category])) {
                $book->category_id = $categoriesMap[$book->category];
                $updated = true;
            }
            if ($updated) {
                $book->save();
            }
        }

        // 6. Hubungkan/Buat Data Anggota
        Member::firstOrCreate(
            ['member_code' => 'MBR-0001'],
            [
                'user_id' => $peminjamUser->id,
                'name' => 'Ahmad Fauzi',
                'class' => 'XII IPA 1',
                'email' => 'peminjam@perpustakaan.com',
                'phone' => '081234567890',
                'status' => 'Aktif',
            ]
        );

        // Tambahkan anggota lainnya jika belum ada
        if (Member::count() <= 1) {
            $additionalMembers = [
                [
                    'member_code' => 'MBR-0002',
                    'name' => 'Siti Nurhaliza',
                    'class' => 'XI IPS 2',
                    'email' => 'siti.nur@example.com',
                    'phone' => '082345678901',
                    'status' => 'Aktif',
                ],
                [
                    'member_code' => 'MBR-0003',
                    'name' => 'Budi Pratama',
                    'class' => 'X MIPA 3',
                    'email' => 'budi.pratama@example.com',
                    'phone' => '083456789012',
                    'status' => 'Aktif',
                ],
            ];

            foreach ($additionalMembers as $m) {
                Member::create($m);
            }
        }
    }
}
