<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Matematika Dasar Kelas 10',
                'author' => 'Bambang Sudarsono',
                'published_year' => 2021,
                'category' => 'Pelajaran',
                'description' => 'Buku panduan pembelajaran matematika dasar untuk siswa SMA kelas 10.',
            ],
            [
                'title' => 'Timun Mas & Raksasa',
                'author' => 'Ki Hajar Wiyata',
                'published_year' => 2018,
                'category' => 'Cerita Rakyat',
                'description' => 'Cerita rakyat Jawa tentang petualangan keberanian seorang gadis bernama Timun Mas.',
            ],
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'published_year' => 2005,
                'category' => 'Novel',
                'description' => 'Kisah kehidupan 10 anak sekolah dari keluarga miskin di Belitung yang penuh dengan inspirasi.',
            ],
            [
                'title' => 'Petualangan Si Kancil',
                'author' => 'Rahmat Hidayat',
                'published_year' => 2019,
                'category' => 'Komik',
                'description' => 'Komik anak bercerita kecerdikan kancil dalam menghadapi binatang hutan lainnya.',
            ],
            [
                'title' => 'Filosofi Teras',
                'author' => 'Henry Manampiring',
                'published_year' => 2018,
                'category' => 'Lainnya',
                'description' => 'Buku penerapan filsafat Stoisisme dalam kehidupan sehari-hari secara relevan.',
            ],
            [
                'title' => 'Fisika Modern Terapan',
                'author' => 'Prof. Sastro',
                'published_year' => 2022,
                'category' => 'Pelajaran',
                'description' => 'Konsep fisika kuantum dan teori relativitas disajikan secara ringkas dan praktis.',
            ],
            [
                'title' => 'Malin Kundang',
                'author' => 'Sutan Iskandar',
                'published_year' => 2015,
                'category' => 'Cerita Rakyat',
                'description' => 'Legenda populer Sumatera Barat tentang anak durhaka yang dikutuk menjadi batu.',
            ],
            [
                'title' => 'Bumi',
                'author' => 'Tere Liye',
                'published_year' => 2014,
                'category' => 'Novel',
                'description' => 'Novel fantasi yang menceritakan Raib dan petualangannya di dunia paralel.',
            ],
            [
                'title' => 'Detektif Cilik Vol. 1',
                'author' => 'Ahmad Dani',
                'published_year' => 2020,
                'category' => 'Komik',
                'description' => 'Serial komik misteri penyelidikan kasus teka-teki sekolah oleh detektif cilik.',
            ],
            [
                'title' => 'Seni Berpikir Jernih',
                'author' => 'Rolf Dobelli',
                'published_year' => 2017,
                'category' => 'Lainnya',
                'description' => 'Panduan praktis menghindari sesat pikir dalam mengambil keputusan.',
            ],
            [
                'title' => 'Biologi Sel & Genetika',
                'author' => 'Dr. Sri Mulyani',
                'published_year' => 2023,
                'category' => 'Pelajaran',
                'description' => 'Pembahasan struktur sel, DNA, dan prinsip-prinsip genetika biologi modern.',
            ],
            [
                'title' => 'Sangkuriang & Gunung Tangkuban Parahu',
                'author' => 'M. Yusuf',
                'published_year' => 2016,
                'category' => 'Cerita Rakyat',
                'description' => 'Cerita rakyat legenda legenda Sunda mengenai terbentuknya Gunung Tangkuban Parahu.',
            ],
        ];

        foreach ($books as $bookData) {
            Book::firstOrCreate(
                ['title' => $bookData['title']],
                $bookData
            );
        }
    }
}

