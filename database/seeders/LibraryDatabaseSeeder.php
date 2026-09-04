<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use Illuminate\Database\Seeder;

class LibraryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        // Hubungkan buku lama ke tabel categories jika category_id belum terisi
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

        // Tambah contoh anggota jika belum ada
        if (Member::count() === 0) {
            $members = [
                [
                    'member_code' => 'MBR-001',
                    'name' => 'Ahmad Fauzi',
                    'class' => 'XII IPA 1',
                    'email' => 'ahmad.fauzi@example.com',
                    'phone' => '081234567890',
                    'status' => 'Aktif',
                ],
                [
                    'member_code' => 'MBR-002',
                    'name' => 'Siti Nurhaliza',
                    'class' => 'XI IPS 2',
                    'email' => 'siti.nur@example.com',
                    'phone' => '082345678901',
                    'status' => 'Aktif',
                ],
                [
                    'member_code' => 'MBR-003',
                    'name' => 'Budi Pratama',
                    'class' => 'X MIPA 3',
                    'email' => 'budi.pratama@example.com',
                    'phone' => '083456789012',
                    'status' => 'Aktif',
                ],
                [
                    'member_code' => 'MBR-004',
                    'name' => 'Rina Wijaya',
                    'class' => 'XII IPS 1',
                    'email' => 'rina.wijaya@example.com',
                    'phone' => '085678901234',
                    'status' => 'Aktif',
                ],
            ];

            foreach ($members as $m) {
                Member::create($m);
            }
        }
    }
}
