<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau password yang Anda masukkan salah.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_active) {
            $this->performLogout($request);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sedang tidak aktif. Silakan hubungi Pegawai TU.',
                ]);
        }

        return $this->redirectByRole();
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->performLogout($request);

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    public function redirectAuthenticated(): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return $this->redirectByRole();
    }

    private function redirectByRole(): RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role?->name) {
            'pegawai_tu' => redirect()
                ->route('pegawai-tu.dashboard'),

            'guru' => redirect()
                ->route('guru.dashboard'),

            'siswa' => redirect()
                ->route('siswa.dashboard'),

            'kepala_sekolah' => redirect()
                ->route('kepala-sekolah.dashboard'),

            default => $this->logoutUnknownRole(),
        };
    }

    private function performLogout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }

    private function logoutUnknownRole(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role pengguna tidak dikenali.',
            ]);
    }
}