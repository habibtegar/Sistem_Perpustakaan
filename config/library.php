<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif Denda per Hari (dalam Rupiah)
    |--------------------------------------------------------------------------
    |
    | Jumlah denda yang dikenakan untuk setiap hari keterlambatan pengembalian buku.
    |
    */
    'fine_per_day' => env('LIBRARY_FINE_PER_DAY', 1000),

    /*
    |--------------------------------------------------------------------------
    | Durasi Standar Peminjaman (Hari)
    |--------------------------------------------------------------------------
    |
    | Jumlah hari standar peminjaman buku sejak tanggal dipinjam.
    |
    */
    'default_loan_days' => env('LIBRARY_DEFAULT_LOAN_DAYS', 7),
];
