<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function show()
    {
        return view('profile.change-password');
    }

    /**
     * Handle change password request.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->kata_sandi)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        // Prevent same password
        if (Hash::check($request->password, $user->kata_sandi)) {
            return back()
                ->withErrors(['password' => 'Password baru tidak boleh sama dengan password saat ini.'])
                ->withInput();
        }

        // Update password
        $user->update([
            'kata_sandi' => Hash::make($request->password),
            'id_updater' => $user->UUID,
            'last_update' => now(),
        ]);

        return back()->with('success', 'Password berhasil diperbarui. Silakan gunakan password baru Anda saat login berikutnya.');
    }
}
