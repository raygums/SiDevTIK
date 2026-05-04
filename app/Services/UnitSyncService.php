<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UnitSyncService
{
    /**
     * Sync units from external API.
     *
     * Expected payload can be either:
     * - Array of units
     * - Object with "data" or "units" key containing array of units
     */
    public function syncFromApi(): array
    {
        $endpoint = config('services.unit_sync.endpoint');
        $token = config('services.unit_sync.token');
        $timeout = (int) config('services.unit_sync.timeout', 20);

        if (empty($endpoint)) {
            return [
                'success' => false,
                'message' => 'UNIT_SYNC_API_URL belum dikonfigurasi.',
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ];
        }

        $request = Http::timeout($timeout)->acceptJson();

        if (!empty($token)) {
            $request = $request->withToken($token);
        }

        $response = $request->get($endpoint);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data unit dari API eksternal.',
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ];
        }

        $payload = $response->json();
        $rows = $this->extractRows($payload);

        if (empty($rows)) {
            return [
                'success' => true,
                'message' => 'Sinkronisasi selesai. Tidak ada data unit baru dari API.',
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $mapped = $this->mapRow($row);

                if (empty($mapped['kode_unit']) || empty($mapped['nm_lmbg'])) {
                    $skipped++;
                    continue;
                }

                $category = UnitCategory::firstOrCreate(
                    ['nm_kategori' => $mapped['nm_kategori']],
                    [
                        'id_creator' => auth()->id(),
                        'id_updater' => auth()->id(),
                    ]
                );

                $unit = Unit::where('kode_unit', $mapped['kode_unit'])->first();

                if ($unit) {
                    $unit->update([
                        'nm_lmbg' => $mapped['nm_lmbg'],
                        'kategori_uuid' => $category->UUID,
                        'a_aktif' => $mapped['a_aktif'],
                        'last_sync' => now(),
                        'id_updater' => auth()->id(),
                    ]);
                    $updated++;
                    continue;
                }

                Unit::create([
                    'nm_lmbg' => $mapped['nm_lmbg'],
                    'kode_unit' => $mapped['kode_unit'],
                    'kategori_uuid' => $category->UUID,
                    'a_aktif' => $mapped['a_aktif'],
                    'last_sync' => now(),
                    'id_creator' => auth()->id(),
                    'id_updater' => auth()->id(),
                ]);
                $created++;
            }
        });

        return [
            'success' => true,
            'message' => "Sinkronisasi unit selesai. Ditambahkan {$created}, diperbarui {$updated}, dilewati {$skipped}.",
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Sync units dari file CSV.
     *
     * Format CSV (header wajib ada):
     *   nm_lmbg, kode_unit, nm_kategori, a_aktif
     *
     * @param  string  $filePath  Path sementara file CSV (dari UploadedFile::getRealPath())
     * @param  string  $mode      'upsert' | 'insert'
     */
    public function syncFromCsv(string $filePath, string $mode = 'upsert'): array
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return ['success' => false, 'message' => 'Gagal membaca file CSV.', 'created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        // Baca header (baris pertama)
        $rawHeader = fgetcsv($handle, 2000, ',');

        if ($rawHeader === false) {
            fclose($handle);
            return ['success' => false, 'message' => 'File CSV kosong atau tidak valid.', 'created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        // Normalisasi header: trim + lowercase + hapus BOM UTF-8
        $header = array_map(fn($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', $h))), $rawHeader);

        // Validasi kolom wajib
        $required = ['nm_lmbg'];
        $missing  = array_diff($required, $header);
        if (!empty($missing)) {
            fclose($handle);
            return [
                'success' => false,
                'message' => 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '. Header ditemukan: ' . implode(', ', $header),
                'created' => 0, 'updated' => 0, 'skipped' => 0,
            ];
        }

        $rows    = [];
        $lineNum = 1;

        while (($line = fgetcsv($handle, 2000, ',')) !== false) {
            $lineNum++;
            if (empty(array_filter($line))) continue; // skip baris kosong

            if (count($line) !== count($header)) continue; // skip baris malformed

            $rows[] = array_combine($header, array_map('trim', $line));
        }

        fclose($handle);

        if (empty($rows)) {
            return ['success' => true, 'message' => 'File CSV tidak memiliki data (selain header).', 'created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $mode, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                // Ambil nilai kolom dengan fallback
                $nmLmbg    = trim($row['nm_lmbg'] ?? '');
                $kodeUnit  = trim($row['kode_unit'] ?? '');
                $nmKategori = trim($row['nm_kategori'] ?? 'Import CSV');
                $aAktif    = isset($row['a_aktif']) ? (bool)(int)$row['a_aktif'] : true;

                if (empty($nmLmbg)) {
                    $skipped++;
                    continue;
                }

                // Normalisasi kode_unit seperti syncFromApi
                if (empty($kodeUnit)) {
                    $kodeUnit = strtolower(\Illuminate\Support\Str::slug($nmLmbg, '-'));
                } else {
                    $kodeUnit = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', $kodeUnit));
                }

                $category = UnitCategory::firstOrCreate(
                    ['nm_kategori' => $nmKategori],
                    ['id_creator' => auth()->id(), 'id_updater' => auth()->id()]
                );

                $existing = Unit::where('kode_unit', $kodeUnit)->first();

                if ($existing) {
                    if ($mode === 'insert') {
                        $skipped++;
                        continue;
                    }
                    $existing->update([
                        'nm_lmbg'       => $nmLmbg,
                        'kategori_uuid' => $category->UUID,
                        'a_aktif'       => $aAktif,
                        'last_sync'     => now(),
                        'id_updater'    => auth()->id(),
                    ]);
                    $updated++;
                } else {
                    Unit::create([
                        'nm_lmbg'       => $nmLmbg,
                        'kode_unit'     => $kodeUnit,
                        'kategori_uuid' => $category->UUID,
                        'a_aktif'       => $aAktif,
                        'last_sync'     => now(),
                        'id_creator'    => auth()->id(),
                        'id_updater'    => auth()->id(),
                    ]);
                    $created++;
                }
            }
        });

        return [
            'success' => true,
            'message' => "Import CSV selesai. Ditambahkan {$created}, diperbarui {$updated}, dilewati {$skipped}.",
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    private function extractRows(mixed $payload): array
    {
        if (is_array($payload) && array_is_list($payload)) {
            return $payload;
        }

        if (! is_array($payload)) {
            return [];
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        if (isset($payload['units']) && is_array($payload['units'])) {
            return $payload['units'];
        }

        if (isset($payload['items']) && is_array($payload['items'])) {
            return $payload['items'];
        }

        return [];
    }

    private function mapRow(array $row): array
    {
        $rawCode = (string) ($row['kode_unit'] ?? $row['unit_code'] ?? $row['kode'] ?? $row['code'] ?? $row['subdomain'] ?? '');
        $rawName = (string) ($row['nm_lmbg'] ?? $row['unit_name'] ?? $row['nama_unit'] ?? $row['name'] ?? '');
        $rawCategory = (string) ($row['nm_kategori'] ?? $row['category_name'] ?? $row['kategori'] ?? $row['category'] ?? 'Sinkronisasi API');

        $normalizedCode = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', $rawCode));

        if (empty($normalizedCode) && ! empty($rawName)) {
            $normalizedCode = strtolower(Str::slug($rawName, '-'));
        }

        return [
            'kode_unit' => $normalizedCode,
            'nm_lmbg' => trim($rawName),
            'nm_kategori' => trim($rawCategory) ?: 'Sinkronisasi API',
            'a_aktif' => (bool) ($row['a_aktif'] ?? $row['is_active'] ?? $row['active'] ?? true),
        ];
    }
}
