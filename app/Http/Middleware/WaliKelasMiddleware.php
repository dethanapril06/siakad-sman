<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->isGuruMapel()) {
            abort(403, 'Akses hanya tersedia untuk guru.');
        }

        $guru = $user->guru;

        if (! $guru) {
            abort(403, 'Data guru tidak ditemukan.');
        }

        if (! $guru->isWaliKelasAktif()) {
            abort(
                403,
                'Anda tidak memiliki penugasan sebagai wali kelas aktif.'
            );
        }

        return $next($request);
    }
}