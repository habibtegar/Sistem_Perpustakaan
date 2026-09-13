<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            if ($user->role === 'peminjam' || $user->role === 'user') {
                return redirect()->route('peminjam.dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman Administrator.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Akses Ditolak: Anda diarahkan kembali ke Dashboard Admin.');
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat halaman ini.');
        }

        return $next($request);
    }
}
