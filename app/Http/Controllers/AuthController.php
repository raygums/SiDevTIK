<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Menampilkan halaman login.
     */
    public function index(): View
    {
        return view('auth.login'); 
    }

    /**
     * Memproses login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'usn' => 'required|string',
            'kata_sandi' => 'required|string',
        ]);

        $credentials = [
            'usn' => $request->usn,
            'password' => $request->kata_sandi,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'usn' => 'Username atau password salah.',
        ])->onlyInput('usn');
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (method_exists($this->authService, 'logout')) {
            $this->authService->logout();
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}