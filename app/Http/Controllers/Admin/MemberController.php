<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::with(['user'])->withCount('activeBorrowings');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'Aktif')->count();

        return view('admin.members.index', compact('members', 'totalMembers', 'activeMembers'));
    }

    public function create(): View
    {
        $nextId = (Member::max('id') ?? 0) + 1;
        $suggestedCode = 'MBR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.members.create', compact('suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'member_code' => ['required', 'string', 'max:50', 'unique:members,member_code'],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ], [
            'member_code.required' => 'ID/Nomor Anggota wajib diisi.',
            'member_code.unique' => 'ID/Nomor Anggota sudah terdaftar.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar untuk pengguna lain.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $username = $request->username ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name)) . rand(100, 999);
        $password = $request->password ?: 'peminjam123';

        // Buat akun user
        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'peminjam',
        ]);

        // Buat data member
        Member::create([
            'user_id' => $user->id,
            'member_code' => $request->member_code,
            'name' => $request->name,
            'class' => $request->class,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.members.index')->with('success', "Anggota berhasil didaftarkan dengan akun login (Username: {$username})!");
    }

    public function show(Member $member): View
    {
        $member->load(['user', 'transactions.book']);
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $member->load('user');
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $request->validate([
            'member_code' => ['required', 'string', 'max:50', 'unique:members,member_code,' . $member->id],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . ($member->user_id ?? 0)],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'member_code.required' => 'ID/Nomor Anggota wajib diisi.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
        ]);

        $member->update([
            'member_code' => $request->member_code,
            'name' => $request->name,
            'class' => $request->class,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        if ($member->user) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $member->user->update($userData);
        }

        return redirect()->route('admin.members.index')->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->activeBorrowings()->count() > 0) {
            return redirect()->route('admin.members.index')->with('error', 'Tidak dapat menghapus anggota yang masih memiliki buku pinjaman aktif!');
        }

        if ($member->user) {
            $member->user->delete();
        }

        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Anggota dan akun pengguna berhasil dihapus!');
    }
}
