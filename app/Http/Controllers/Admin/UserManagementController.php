<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peran;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Tampilkan daftar user yang belum aktif (Pending Approval)
    public function indexPending()
    {
        $pendingUsers = User::where('a_aktif', false)->with('peran')->get();
        return view('admin.users.pending', compact('pendingUsers'));
    }

    // Aksi Approve User & Assign Role
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'role_uuid' => 'required|exists:akun.peran,UUID'
        ]);

        $user = User::findOrFail($uuid);
        
        $user->update([
            'a_aktif' => true, // Aktifkan user
            'peran_uuid' => $request->role_uuid // Tentukan hak akses (Admin/Verifikator/Dll)
        ]);

        return back()->with('success', 'User berhasil divalidasi dan diaktifkan.');
    }
}