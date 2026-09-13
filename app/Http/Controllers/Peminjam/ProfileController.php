<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        return view('peminjam.profile.index', compact('user', 'member'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'class' => ['nullable', 'string', 'max:100'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'current_password.required_with' => 'Password saat ini diperlukan untuk mengganti password baru.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if ($member) {
            $member->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'class' => $request->class,
            ]);
        }

        return redirect()->route('peminjam.profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
