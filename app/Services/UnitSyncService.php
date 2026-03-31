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
