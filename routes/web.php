<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReturnController as AdminReturnController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Peminjam\BookCatalogController as PeminjamBookCatalogController;
use App\Http\Controllers\Peminjam\BorrowRequestController as PeminjamBorrowRequestController;
use App\Http\Controllers\Peminjam\DashboardController as PeminjamDashboardController;
use App\Http\Controllers\Peminjam\MyBorrowingController as PeminjamMyBorrowingController;
use App\Http\Controllers\Peminjam\MyHistoryController as PeminjamMyHistoryController;
use App\Http\Controllers\Peminjam\ProfileController as PeminjamProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen Perpustakaan Multi-Role
|--------------------------------------------------------------------------
*/

// Halaman Utama: Redirect berdasarkan status otentikasi & role
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('peminjam.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Autentikasi (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout (Authenticated)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| RUTE KHUSUS ADMINISTRATOR (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Kelola Buku
    Route::resource('books', AdminBookController::class);

    // Kelola Kategori
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // Kelola Anggota
    Route::resource('members', AdminMemberController::class);

    // Kelola Peminjaman & Persetujuan
    Route::get('borrowings', [AdminBorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('borrowings/create', [AdminBorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('borrowings', [AdminBorrowingController::class, 'store'])->name('borrowings.store');
    Route::post('borrowings/{transaction}/approve', [AdminBorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('borrowings/{transaction}/reject', [AdminBorrowingController::class, 'reject'])->name('borrowings.reject');

    // Kelola Pengembalian
    Route::get('returns', [AdminReturnController::class, 'index'])->name('returns.index');
    Route::post('returns/{transaction}/process', [AdminReturnController::class, 'process'])->name('returns.process');

    // Riwayat Transaksi Seluruh Perpustakaan
    Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');

    // Laporan Perpustakaan
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

    // Pengaturan Perpustakaan & Tarif Denda
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| RUTE KHUSUS PEMINJAM
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:peminjam,admin'])->prefix('peminjam')->name('peminjam.')->group(function () {
    Route::get('/', [PeminjamDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [PeminjamDashboardController::class, 'index']);

    // Katalog Buku (Read-only)
    Route::get('books', [PeminjamBookCatalogController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [PeminjamBookCatalogController::class, 'show'])->name('books.show');

    // Ajukan Peminjaman
    Route::post('borrow/{book}', [PeminjamBorrowRequestController::class, 'store'])->name('borrow.store');

    // Peminjaman Saya (Aktif & Menunggu)
    Route::get('my-borrowings', [PeminjamMyBorrowingController::class, 'index'])->name('my-borrowings');

    // Riwayat Transaksi Saya
    Route::get('history', [PeminjamMyHistoryController::class, 'index'])->name('history');

    // Profil Peminjam
    Route::get('profile', [PeminjamProfileController::class, 'index'])->name('profile');
    Route::post('profile', [PeminjamProfileController::class, 'update'])->name('profile.update');
});
