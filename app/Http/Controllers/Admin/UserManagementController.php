<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peran;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Tampilkan halaman manajemen pengguna.
     */
    public function index(): View
    {
        $users  = User::with('peran')->orderBy('create_at', 'desc')->paginate(20);
        $perans = Peran::where('a_aktif', true)->orderBy('nm_peran')->get();
        $units  = Unit::where('a_aktif', true)->orderBy('nm_lmbg')->get();

        return view('admin.users.index', compact('users', 'perans', 'units'));
    }

    /**
     * Aktifkan / non-aktifkan akun pengguna.
     */
    public function toggleActivation(Request $request, User $user): RedirectResponse
    {
        $user->update(['a_aktif' => !$user->a_aktif]);

        $status = $user->a_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->nm} berhasil {$status}.");
    }

    /**
     * Ubah role pengguna.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'peran_uuid' => 'required|exists:akun.peran,UUID',
        ]);

        $user->update(['peran_uuid' => $request->peran_uuid]);

        return back()->with('success', "Role akun {$user->nm} berhasil diperbarui.");
    }

    /**
     * Buat akun lokal baru (oleh admin).
     */
    public function createAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'nm'         => 'required|string|max:125',
            'usn'        => 'required|string|max:100|unique:akun.pengguna,usn',
            'email'      => 'required|email|max:125|unique:akun.pengguna,email',
            'kata_sandi' => 'required|string|min:8',
            'peran_uuid' => 'required|exists:akun.peran,UUID',
            'a_aktif'    => 'nullable|boolean',
        ]);

        User::create([
            'nm'         => $request->nm,
            'usn'        => $request->usn,
            'email'      => $request->email,
            'kata_sandi' => Hash::make($request->kata_sandi),
            'peran_uuid' => $request->peran_uuid,
            'a_aktif'    => $request->boolean('a_aktif', true),
            'create_at'  => now(),
        ]);

        return back()->with('success', "Akun {$request->nm} berhasil dibuat.");
    }

    // =====================================================
    // IMPORT CSV DOMAIN & SUBDOMAIN
    // =====================================================

    /**
     * Tampilkan halaman import CSV Unit/Domain.
     */
    public function showImportCsv(): View
    {
        $categories = UnitCategory::orderBy('nm_kategori')->get();

        return view('admin.users.import-csv', compact('categories'));
    }

    /**
     * Proses upload dan import CSV.
     *
     * Format CSV yang diterima (dengan header):
     *   nm_lmbg, kode_unit, nm_kategori, a_aktif
     *
     * Contoh:
     *   Fakultas Teknik, FT, Fakultas, 1
     *   Teknik Informatika, TI, Jurusan, 1
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'mode'     => 'required|in:insert,upsert',
        ]);

        $file = $request->file('csv_file');
        $mode = $request->input('mode', 'upsert');

        // Baca isi file CSV
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        $header  = null;
        $rows    = [];
        $lineNum = 0;
        $errors  = [];

        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            $lineNum++;

            // Baris pertama = header
            if ($header === null) {
                $header = array_map('trim', $line);
                // Normalisasi header ke lowercase
                $header = array_map('strtolower', $header);
                continue;
            }

            // Skip baris kosong
            if (empty(array_filter($line))) {
                continue;
            }

            // Pastikan jumlah kolom sesuai header
            if (count($line) !== count($header)) {
                $errors[] = "Baris {$lineNum}: Jumlah kolom tidak sesuai (ditemukan " . count($line) . ", diharapkan " . count($header) . ").";
                continue;
            }

            $row = array_combine($header, array_map('trim', $line));
            $rows[] = ['line' => $lineNum, 'data' => $row];
        }

        fclose($handle);

        if (!empty($errors) && count($errors) === $lineNum - 1) {
            return back()->with('error', 'Format CSV tidak valid: ' . implode('; ', array_slice($errors, 0, 3)));
        }

        // Validasi kolom wajib
        $requiredColumns = ['nm_lmbg', 'nm_kategori'];
        if ($header && count(array_intersect($requiredColumns, $header)) < count($requiredColumns)) {
            return back()->with('error', 'CSV harus memiliki kolom: nm_lmbg, nm_kategori (dan opsional: kode_unit, a_aktif).');
        }

        // Proses import
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $item) {
                $row     = $item['data'];
                $lineNum = $item['line'];

                $nmLmbg    = $row['nm_lmbg'] ?? null;
                $nmKategori = $row['nm_kategori'] ?? null;
                $kodeUnit  = $row['kode_unit'] ?? null;
                $aAktif    = isset($row['a_aktif']) ? (bool)(int)$row['a_aktif'] : true;

                if (empty($nmLmbg) || empty($nmKategori)) {
                    $skipped++;
                    continue;
                }

                // Cari atau buat kategori
                $category = UnitCategory::firstOrCreate(
                    ['nm_kategori' => $nmKategori]
                );

                if ($mode === 'upsert') {
                    // Update jika ada, insert jika belum
                    $existing = Unit::where('nm_lmbg', $nmLmbg)->first();

                    if ($existing) {
                        $existing->update([
                            'kode_unit'    => $kodeUnit ?: $existing->kode_unit,
                            'kategori_uuid' => $category->UUID,
                            'a_aktif'      => $aAktif,
                        ]);
                        $updated++;
                    } else {
                        Unit::create([
                            'nm_lmbg'      => $nmLmbg,
                            'kode_unit'    => $kodeUnit,
                            'kategori_uuid' => $category->UUID,
                            'a_aktif'      => $aAktif,
                        ]);
                        $inserted++;
                    }
                } else {
                    // mode=insert: skip kalau sudah ada
                    $exists = Unit::where('nm_lmbg', $nmLmbg)->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    Unit::create([
                        'nm_lmbg'      => $nmLmbg,
                        'kode_unit'    => $kodeUnit,
                        'kategori_uuid' => $category->UUID,
                        'a_aktif'      => $aAktif,
                    ]);
                    $inserted++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import CSV: ' . $e->getMessage());
        }

        $msg = "Import selesai: {$inserted} data baru ditambahkan";
        if ($updated > 0) $msg .= ", {$updated} data diperbarui";
        if ($skipped > 0) $msg .= ", {$skipped} data dilewati";
        $msg .= ".";

        return back()->with('success', $msg);
    }

    /**
     * Download template CSV contoh.
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_unit.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, ['nm_lmbg', 'kode_unit', 'nm_kategori', 'a_aktif']);

            // Contoh data
            fputcsv($handle, ['Fakultas Teknik', 'FT', 'Fakultas', '1']);
            fputcsv($handle, ['Teknik Informatika', 'TI', 'Jurusan/Prodi', '1']);
            fputcsv($handle, ['Lembaga Penelitian', 'LP', 'Lembaga', '1']);
            fputcsv($handle, ['UPA Teknologi Informasi', 'TIK', 'UPA', '1']);
            fputcsv($handle, ['Rektorat', 'RKT', 'Biro/Rektorat', '1']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
