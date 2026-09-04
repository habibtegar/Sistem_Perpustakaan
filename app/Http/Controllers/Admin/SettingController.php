<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $finePerDay = Setting::get('fine_per_day', config('library.fine_per_day', 1000));
        $defaultLoanDays = Setting::get('default_loan_days', config('library.default_loan_days', 7));
        $libraryName = Setting::get('library_name', 'Sistem Perpustakaan Digital');
        $libraryAddress = Setting::get('library_address', 'Jl. Pendidikan No. 123');

        return view('admin.settings.index', compact(
            'finePerDay',
            'defaultLoanDays',
            'libraryName',
            'libraryAddress'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'fine_per_day' => ['required', 'integer', 'min:0'],
            'default_loan_days' => ['required', 'integer', 'min:1'],
            'library_name' => ['required', 'string', 'max:255'],
            'library_address' => ['nullable', 'string', 'max:500'],
        ], [
            'fine_per_day.required' => 'Tarif denda per hari wajib diisi.',
            'fine_per_day.min' => 'Tarif denda tidak boleh negatif.',
            'default_loan_days.required' => 'Durasi peminjaman wajib diisi.',
            'default_loan_days.min' => 'Durasi peminjaman minimal 1 hari.',
            'library_name.required' => 'Nama perpustakaan wajib diisi.',
        ]);

        Setting::set('fine_per_day', $request->fine_per_day, 'Tarif denda per hari (Rp)');
        Setting::set('default_loan_days', $request->default_loan_days, 'Durasi standar peminjaman (Hari)');
        Setting::set('library_name', $request->library_name, 'Nama Perpustakaan');
        Setting::set('library_address', $request->library_address, 'Alamat Perpustakaan');

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan perpustakaan berhasil disimpan!');
    }
}
