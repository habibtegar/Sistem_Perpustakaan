<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::withCount('activeBorrowings');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'Aktif')->count();

        return view('members.index', compact('members', 'totalMembers', 'activeMembers'));
    }

    public function create(): View
    {
        // Generate nomor anggota berikutnya secara otomatis sebagai saran
        $nextId = (Member::max('id') ?? 0) + 1;
        $suggestedCode = 'MBR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('members.create', compact('suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50', 'unique:members,member_code'],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ], [
            'member_code.required' => 'ID/Nomor Anggota wajib diisi.',
            'member_code.unique' => 'ID/Nomor Anggota sudah terdaftar.',
            'name.required' => 'Nama anggota wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'status.required' => 'Status anggota wajib dipilih.',
        ]);

        Member::create($validated);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil didaftarkan!');
    }

    public function show(Member $member): View
    {
        $member->load(['transactions.book']);
        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50', 'unique:members,member_code,' . $member->id],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ], [
            'member_code.required' => 'ID/Nomor Anggota wajib diisi.',
            'member_code.unique' => 'ID/Nomor Anggota sudah terdaftar.',
            'name.required' => 'Nama anggota wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'status.required' => 'Status anggota wajib dipilih.',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->activeBorrowings()->count() > 0) {
            return redirect()->route('members.index')->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif!');
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
