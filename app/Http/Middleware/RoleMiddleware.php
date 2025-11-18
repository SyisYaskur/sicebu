<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Cek apakah relasi 'roles' ada dan tidak kosong
        if ($user->roles && $user->roles->isNotEmpty()) {
            foreach ($roles as $role) {
                // Cek apakah user memiliki role dengan 'code' yang sesuai
                if ($user->roles->contains('code', $role)) {
                    return $next($request);
                }
            }
        }

        // Jika tidak memiliki role yang diizinkan, hentikan dengan halaman 403 Forbidden
        // JANGAN redirect ke login dari sini, karena akan menyebabkan loop.
        abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MEMBUKA HALAMAN INI.');
    }
}