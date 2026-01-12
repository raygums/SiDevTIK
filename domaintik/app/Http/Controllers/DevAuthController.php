<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevAuthController extends Controller
{
    /**
     * Login as a specific user (DEV ONLY)
     * Route: /dev/login/{id}
     */
    public function login(int $id)
    {
        // Hanya aktif di environment local/development
        if (!app()->environment(['local', 'development'])) {
            abort(403, 'Fitur ini hanya tersedia di mode development.');
        }

        $user = User::findOrFail($id);
        Auth::login($user);

        return redirect('/')->with('success', "Berhasil login sebagai {$user->name} ({$user->role})");
    }

    /**
     * Logout current user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout.');
    }
}
