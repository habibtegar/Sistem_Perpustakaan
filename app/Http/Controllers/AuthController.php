<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');

        // Cari user berdasarkan username atau email
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return $this->redirectByRole($user)->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withInput($request->only('login'))->withErrors([
            'login' => 'Username/Email atau password yang Anda masukkan salah.',
        ]);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'class' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'peminjam',
        ]);

        // Buat data anggota otomatis yang terhubung ke user
        $nextId = (Member::max('id') ?? 0) + 1;
        $memberCode = 'MBR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Member::create([
            'user_id' => $user->id,
            'member_code' => $memberCode,
            'name' => $user->name,
            'class' => $request->class,
            'email' => $user->email,
            'phone' => $request->phone,
            'status' => 'Aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('peminjam.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di Sistem Perpustakaan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar (logout).');
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('peminjam.dashboard');
    }
}
