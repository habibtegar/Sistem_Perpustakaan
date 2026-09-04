<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\TransactionHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen Perpustakaan
|--------------------------------------------------------------------------
*/

// Dashboard Utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Manajemen Buku (CRUD)
Route::resource('books', BookController::class);

// Manajemen Kategori (CRUD)
Route::resource('categories', CategoryController::class)->except(['show']);

// Manajemen Anggota (CRUD)
Route::resource('members', MemberController::class);

// Peminjaman Buku
Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
Route::get('borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');

// Pengembalian Buku
Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
Route::post('returns/{transaction}/process', [ReturnController::class, 'process'])->name('returns.process');

// Riwayat Transaksi
Route::get('transactions', [TransactionHistoryController::class, 'index'])->name('transactions.index');

// Laporan Perpustakaan
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
