<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Tampilkan halaman daftar unit & subdomain
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        $query = Unit::with('category')->orderBy('nm_lmbg', 'asc');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nm_lmbg', 'ilike', '%' . $search . '%')
                  ->orWhere('kode_unit', 'ilike', '%' . $search . '%');
            });
        }
        
        $units = $query->paginate(20)->withQueryString();
        $categories = \App\Models\UnitCategory::orderBy('nm_kategori', 'asc')->get();
        
        return view('admin.units.index', compact('units', 'search', 'categories'));
    }

    /**
     * Simpan data unit baru (manual)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nm_lmbg' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:50',
            'kategori_uuid' => 'nullable|exists:referensi.kategori_unit,UUID',
            'a_aktif' => 'boolean'
        ]);

        $validated['a_aktif'] = $request->has('a_aktif');
        $validated['id_creator'] = auth()->user()->UUID;

        Unit::create($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    /**
     * Update data unit (manual)
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'nm_lmbg' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:50',
            'kategori_uuid' => 'nullable|exists:referensi.kategori_unit,UUID',
            'a_aktif' => 'boolean'
        ]);

        $validated['a_aktif'] = $request->has('a_aktif');
        $validated['id_updater'] = auth()->user()->UUID;

        $unit->update($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Hapus data unit (manual)
     */
    public function destroy(Unit $unit)
    {
        // Cek apakah unit sudah digunakan di pengajuan
        if ($unit->submissions()->exists()) {
            return redirect()->route('admin.units.index')->with('error', 'Unit tidak dapat dihapus karena sudah memiliki riwayat pengajuan.');
        }

        $unit->delete();

        return redirect()->route('admin.units.index')->with('success', 'Unit berhasil dihapus.');
    }
}
