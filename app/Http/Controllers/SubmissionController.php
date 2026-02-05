<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use App\Models\StatusPengajuan;
use App\Models\Submission;
use App\Models\SubmissionDetail;
use App\Models\SubmissionLog;
use App\Models\Unit;
use App\Models\UnitCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    /**
     * Display list of submissions for current user
     */
    public function index()
    {
        $submissions = Submission::with(['unitKerja', 'rincian', 'jenisLayanan', 'status'])
            ->where('pengguna_uuid', Auth::user()->UUID)
            ->orderBy('create_at', 'desc')
            ->paginate(10);

        return view('submissions.index', compact('submissions'));
    }

    /**
     * Show submission form
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'domain'); // domain, hosting, vps
        
        // Validate type
        if (!in_array($type, ['domain', 'hosting', 'vps'])) {
            $type = 'domain';
        }

        // Get units grouped by category
        $categories = UnitCategory::with('units')->get();
        
        // Get current user data
        $user = Auth::user();

        return view('submissions.create', compact('type', 'categories', 'user'));
    }

    /**
     * Store new submission
     */
    public function store(Request $request)
    {
        // Get tipe_pengajuan to determine validation rules
        $tipePengajuan = $request->input('tipe_pengajuan', 'pengajuan_baru');
        $requestType = $request->input('request_type', 'domain');
        $isPengajuanBaru = $tipePengajuan === 'pengajuan_baru';
        $isUpgradeDowngrade = $tipePengajuan === 'upgrade_downgrade';
        
        // Build validation rules based on tipe_pengajuan
        $rules = [
            // Tipe Pengajuan
            'tipe_pengajuan' => 'required|in:pengajuan_baru,perpanjangan,perubahan_data,upgrade_downgrade,penonaktifan,laporan_masalah',
            
            // Kategori Pemohon
            'kategori_pemohon' => 'required|in:lembaga_fakultas,kegiatan_lembaga,organisasi_mahasiswa,kegiatan_mahasiswa,lainnya',
            'nama_organisasi' => 'required|string|max:255',
            
            // Data Penanggung Jawab Administratif
            'admin_responsible_name' => 'required|string|max:255',
            'admin_responsible_position' => 'required|string|max:255',
            'admin_responsible_nip' => 'nullable|string|max:50',
            'admin_alamat_kantor' => 'nullable|string|max:255',
            'admin_alamat_rumah' => 'nullable|string|max:255',
            'admin_telepon_kantor' => 'nullable|string|max:20',
            'admin_responsible_phone' => 'required|string|max:20',
            'admin_email' => 'required|email|max:255',
            
            // Data Penanggung Jawab Teknis
            'tech_name' => 'required|string|max:255',
            'tech_nip' => 'required|string|max:50',
            'tech_phone' => 'required|string|max:20',
            'tech_alamat_kantor' => 'nullable|string|max:255',
            'tech_alamat_rumah' => 'nullable|string|max:255',
            'tech_email' => 'required|email|max:255',
            
            // Hidden fields for DB compatibility
            'unit_id' => 'nullable',
            'application_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'request_type' => 'required|in:domain,hosting,vps',
            
            // Existing service info (for non-new submissions)
            'existing_domain' => $isPengajuanBaru ? 'nullable|string' : 'required|string|max:255',
            'existing_ticket' => 'nullable|string|max:50',
            'existing_expired' => 'nullable|date',
            'existing_notes' => $isPengajuanBaru ? 'nullable|string' : 'required|string|max:2000',
        ];
        
        // Add rules for pengajuan baru or upgrade/downgrade
        if ($isPengajuanBaru) {
            $rules['requested_domain'] = 'required|string|min:2|max:12|regex:/^[a-z0-9\-]+$/';
            $rules['admin_password'] = 'required|string|min:6|max:8';
        } else {
            $rules['requested_domain'] = 'nullable|string|min:2|max:12|regex:/^[a-z0-9\-]+$/';
            $rules['admin_password'] = 'nullable|string|min:6|max:8';
        }
        
        // VPS specific rules
        if ($requestType === 'vps') {
            if ($isPengajuanBaru || $isUpgradeDowngrade) {
                $rules['vps_cpu'] = 'required|string';
                $rules['vps_ram'] = 'required|string';
                $rules['vps_storage'] = 'required|string';
                $rules['vps_os'] = $isPengajuanBaru ? 'required|string' : 'nullable|string';
                $rules['vps_purpose'] = $isPengajuanBaru ? 'required|string' : 'nullable|string';
            } else {
                $rules['vps_cpu'] = 'nullable|string';
                $rules['vps_ram'] = 'nullable|string';
                $rules['vps_storage'] = 'nullable|string';
                $rules['vps_os'] = 'nullable|string';
                $rules['vps_purpose'] = 'nullable|string';
            }
        }
        
        // Hosting specific rules
        if ($requestType === 'hosting') {
            if ($isPengajuanBaru || $isUpgradeDowngrade) {
                $rules['hosting_quota'] = 'required|string';
            } else {
                $rules['hosting_quota'] = 'nullable|string';
            }
        }
        
        // Validation messages
        $messages = [
            'tipe_pengajuan.required' => 'Tipe pengajuan wajib dipilih.',
            'kategori_pemohon.required' => 'Kategori pemohon wajib dipilih.',
            'nama_organisasi.required' => 'Nama lembaga/organisasi/kegiatan wajib diisi.',
            'admin_responsible_name.required' => 'Nama penanggung jawab administratif wajib diisi.',
            'admin_responsible_position.required' => 'Jabatan penanggung jawab administratif wajib diisi.',
            'admin_responsible_phone.required' => 'Nomor telepon rumah/HP penanggung jawab administratif wajib diisi.',
            'admin_email.required' => 'Email penanggung jawab administratif wajib diisi.',
            'tech_name.required' => 'Nama penanggung jawab teknis wajib diisi.',
            'tech_nip.required' => 'NIP/NIM penanggung jawab teknis wajib diisi.',
            'tech_phone.required' => 'Nomor telepon penanggung jawab teknis wajib diisi.',
            'tech_email.required' => 'Email penanggung jawab teknis wajib diisi.',
            'requested_domain.required' => 'Nama sub domain wajib diisi.',
            'requested_domain.min' => 'Nama sub domain minimal 2 karakter.',
            'requested_domain.max' => 'Nama sub domain maksimal 12 karakter.',
            'requested_domain.regex' => 'Nama sub domain hanya boleh huruf kecil, angka, dan tanda hubung.',
            'admin_password.required' => 'Admin password (hint) wajib diisi.',
            'admin_password.min' => 'Admin password minimal 6 karakter.',
            'admin_password.max' => 'Admin password maksimal 8 karakter.',
            'existing_domain.required' => 'Domain/Hosting/VPS existing wajib diisi.',
            'existing_notes.required' => 'Keterangan permohonan wajib diisi.',
            'vps_cpu.required' => 'Jumlah CPU wajib dipilih.',
            'vps_ram.required' => 'Kapasitas RAM wajib dipilih.',
            'vps_storage.required' => 'Kapasitas storage wajib dipilih.',
            'vps_os.required' => 'Sistem operasi wajib dipilih.',
            'vps_purpose.required' => 'Tujuan penggunaan VPS wajib diisi.',
            'hosting_quota.required' => 'Kuota storage wajib dipilih.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $tipePengajuan = $validated['tipe_pengajuan'];
            $isPengajuanBaru = $tipePengajuan === 'pengajuan_baru';
            
            // Get or create jenis layanan
            $jenisLayanan = JenisLayanan::firstOrCreate(
                ['nm_layanan' => $validated['request_type']],
                ['deskripsi' => 'Layanan ' . ucfirst($validated['request_type']), 'a_aktif' => true]
            );
            
            // Get or create status
            $status = StatusPengajuan::firstOrCreate(
                ['nm_status' => 'Draft'],
            );
            
            // Get first unit if no unit selected
            $unitKerja = Unit::first();

            // Create submission (pengajuan)
            $submission = Submission::create([
                'no_tiket' => Submission::generateTicketNumber(),
                'pengguna_uuid' => $user->UUID,
                'unit_kerja_uuid' => $unitKerja?->UUID,
                'jenis_layanan_uuid' => $jenisLayanan->UUID,
                'status_uuid' => $status->UUID,
                'tgl_pengajuan' => now(),
                'id_creator' => $user->UUID,
            ]);

            // Build keterangan_keperluan based on request type
            $keterangan = $this->buildKeterangan($validated);
            
            // Determine domain name based on tipe_pengajuan
            $domainName = $isPengajuanBaru 
                ? $this->formatDomain($validated['requested_domain'], $validated['request_type'])
                : $validated['existing_domain'];

            // Create submission detail (rincian_pengajuan)
            SubmissionDetail::create([
                'pengajuan_uuid' => $submission->UUID,
                'nm_domain' => $domainName,
                'kapasitas_penyimpanan' => $validated['hosting_quota'] ?? $validated['vps_storage'] ?? null,
                'keterangan_keperluan' => $keterangan,
                'id_creator' => $user->UUID,
            ]);
            
            // Create log title based on tipe_pengajuan
            $logTitles = [
                'pengajuan_baru' => 'Formulir pengajuan baru dibuat.',
                'perpanjangan' => 'Formulir perpanjangan layanan dibuat.',
                'perubahan_data' => 'Formulir perubahan data dibuat.',
                'upgrade_downgrade' => 'Formulir upgrade/downgrade layanan dibuat.',
                'penonaktifan' => 'Formulir penonaktifan layanan dibuat.',
                'laporan_masalah' => 'Formulir laporan masalah dibuat.',
            ];

            // Create log (riwayat_pengajuan)
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_baru_uuid' => $status->UUID,
                'catatan_log' => $logTitles[$tipePengajuan] ?? 'Formulir pengajuan dibuat.',
                'id_creator' => $user->UUID,
            ]);

            DB::commit();

            return redirect()
                ->route('forms.select', $submission->no_tiket)
                ->with('success', 'Formulir berhasil dibuat! Silakan pilih jenis form yang ingin digenerate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Build keterangan keperluan from validated data
     */
    private function buildKeterangan(array $validated): string
    {
        $data = [
            'tipe_pengajuan' => $validated['tipe_pengajuan'],
            'kategori_pemohon' => $validated['kategori_pemohon'],
            'nama_organisasi' => $validated['nama_organisasi'],
            'admin' => [
                'name' => $validated['admin_responsible_name'],
                'position' => $validated['admin_responsible_position'],
                'nip' => $validated['admin_responsible_nip'] ?? null,
                'alamat_kantor' => $validated['admin_alamat_kantor'] ?? null,
                'alamat_rumah' => $validated['admin_alamat_rumah'] ?? null,
                'telepon_kantor' => $validated['admin_telepon_kantor'] ?? null,
                'email' => $validated['admin_email'],
                'phone' => $validated['admin_responsible_phone'],
            ],
            'tech' => [
                'name' => $validated['tech_name'],
                'nip' => $validated['tech_nip'],
                'alamat_kantor' => $validated['tech_alamat_kantor'] ?? null,
                'alamat_rumah' => $validated['tech_alamat_rumah'] ?? null,
                'email' => $validated['tech_email'],
                'phone' => $validated['tech_phone'],
            ],
            'password_hint' => $validated['admin_password'] ?? null,
        ];
        
        // Add existing service info for non-new submissions
        if ($validated['tipe_pengajuan'] !== 'pengajuan_baru') {
            $data['existing'] = [
                'domain' => $validated['existing_domain'] ?? null,
                'ticket' => $validated['existing_ticket'] ?? null,
                'expired' => $validated['existing_expired'] ?? null,
                'notes' => $validated['existing_notes'] ?? null,
            ];
        }
        
        // Add VPS specific data
        if ($validated['request_type'] === 'vps') {
            $data['vps'] = [
                'cpu' => $validated['vps_cpu'] ?? null,
                'ram' => $validated['vps_ram'] ?? null,
                'storage' => $validated['vps_storage'] ?? null,
                'os' => $validated['vps_os'] ?? null,
                'purpose' => $validated['vps_purpose'] ?? null,
            ];
        }
        
        // Add hosting specific data
        if ($validated['request_type'] === 'hosting') {
            $data['hosting'] = [
                'quota' => $validated['hosting_quota'] ?? null,
            ];
        }
        
        return json_encode($data);
    }

    /**
     * Show download form page
     */
    public function downloadForm(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        $submission->load(['pengguna', 'unitKerja.category', 'rincian']);

        return view('submissions.download-form', compact('submission'));
    }

    /**
     * Show printable form page (for PDF generation)
     */
    public function printForm(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        $submission->load(['pengguna', 'unitKerja.category', 'rincian']);

        return view('partials.printable-form', compact('submission'));
    }

    /**
     * Show upload page for signed form
     */
    public function showUpload(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        return view('submissions.upload', compact('submission'));
    }

    /**
     * Store uploaded signed form
     */
    public function storeUpload(Request $request, Submission $submission)
    {
        $this->authorizeAccess($submission);

        $validated = $request->validate([
            'signed_form' => 'required|file|mimes:pdf|max:5120', // 5MB max
            'identity_attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'signed_form.required' => 'Scan formulir bertanda tangan wajib diupload.',
            'signed_form.mimes' => 'File formulir harus berformat PDF.',
            'identity_attachment.required' => 'Scan identitas (KTM/Karpeg) wajib diupload.',
        ]);

        try {
            $user = Auth::user();
            
            // Store files
            $signedFormPath = $request->file('signed_form')
                ->store("submissions/{$submission->UUID}", 'public');
            
            $identityPath = $request->file('identity_attachment')
                ->store("submissions/{$submission->UUID}", 'public');

            // Get or create submitted status
            $statusDiajukan = StatusPengajuan::firstOrCreate(
                ['nm_status' => 'Diajukan'],
            );
            
            $oldStatusUuid = $submission->status_uuid;

            // Update submission detail with file paths
            $submission->rincian()->update([
                'file_lampiran' => json_encode([
                    'signed_form' => $signedFormPath,
                    'identity' => $identityPath,
                ]),
                'id_updater' => $user->UUID,
            ]);
            
            // Update submission status
            $submission->update([
                'status_uuid' => $statusDiajukan->UUID,
                'id_updater' => $user->UUID,
            ]);

            // Create log
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_lama_uuid' => $oldStatusUuid,
                'status_baru_uuid' => $statusDiajukan->UUID,
                'catatan_log' => 'Dokumen diupload dan pengajuan dikirim untuk verifikasi.',
                'id_creator' => $user->UUID,
            ]);

            return redirect()
                ->route('submissions.show', $submission)
                ->with('success', 'Pengajuan berhasil dikirim! Tim TIK akan memverifikasi dokumen Anda.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat upload: ' . $e->getMessage());
        }
    }

    /**
     * Show submission detail
     */
    public function show(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        $submission->load(['pengguna', 'unitKerja.category', 'rincian', 'riwayat.creator']);

        return view('submissions.show', compact('submission'));
    }

    /**
     * Format domain name based on request type
     */
    private function formatDomain(?string $domain, string $requestType = 'domain'): ?string
    {
        if (!$domain) return null;
        
        // Remove .unila.ac.id if user included it
        $domain = preg_replace('/\.unila\.ac\.id$/i', '', trim($domain));
        
        // Clean up
        $domain = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', $domain));
        
        // For domain type, append .unila.ac.id
        if ($requestType === 'domain' || $requestType === 'hosting') {
            return $domain . '.unila.ac.id';
        }
        
        return $domain;
    }

    /**
     * Authorize access to submission
     */
    private function authorizeAccess(Submission $submission): void
    {
        $user = Auth::user();
        
        // Owner can always access
        if ($submission->pengguna_uuid === $user->UUID) {
            return;
        }
        
        // Admin, Pimpinan, Verifikator, Eksekutor can access all
        $userRole = $user->peran?->nm_peran ?? '';
        if (in_array(strtolower($userRole), ['admin', 'administrator', 'pimpinan', 'verifikator', 'eksekutor'])) {
            return;
        }
        
        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }

    /**
     * Quick Submit - Skip upload, directly change status to "Diajukan"
     * Only available in development/debug mode
     */
    public function quickSubmit(Submission $submission)
    {
        // Only allow in debug mode
        if (!config('app.debug')) {
            abort(403, 'Fitur ini hanya tersedia di mode development.');
        }

        $this->authorizeAccess($submission);

        // Check if status is Draft
        if ($submission->status?->nm_status !== 'Draft') {
            return back()->with('error', 'Pengajuan ini sudah disubmit sebelumnya.');
        }

        try {
            $user = Auth::user();
            
            // Get or create submitted status
            $statusDiajukan = StatusPengajuan::firstOrCreate(
                ['nm_status' => 'Diajukan'],
            );
            
            $oldStatusUuid = $submission->status_uuid;

            // Update submission status
            $submission->update([
                'status_uuid' => $statusDiajukan->UUID,
                'id_updater' => $user->UUID,
            ]);

            // Create log
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_lama_uuid' => $oldStatusUuid,
                'status_baru_uuid' => $statusDiajukan->UUID,
                'catatan_log' => 'Pengajuan dikirim untuk verifikasi (quick submit - development mode).',
                'id_creator' => $user->UUID,
            ]);

            return redirect()
                ->route('submissions.index')
                ->with('success', 'Pengajuan berhasil dikirim ke Verifikator!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Check domain availability (API endpoint)
     */
    public function checkDomainAvailability(Request $request)
    {
        $domain = $request->query('domain');
        
        if (empty($domain) || strlen($domain) < 2) {
            return response()->json([
                'available' => false,
                'message' => 'Domain minimal 2 karakter'
            ]);
        }

        // Check if domain already exists in submissions
        $exists = SubmissionDetail::where('nm_domain', 'ILIKE', $domain . '%')
            ->orWhere('nm_domain', 'ILIKE', $domain . '.unila.ac.id')
            ->exists();

        return response()->json([
            'available' => !$exists,
            'domain' => $domain,
            'message' => $exists ? 'Domain sudah digunakan' : 'Domain tersedia'
        ]);
    }

    /**
     * Get submission data by ticket number for auto-fill (API endpoint)
     */
    public function getSubmissionByTicket(string $ticketNumber)
    {
        $submission = Submission::with(['unitKerja', 'rincian', 'jenisLayanan', 'pengguna'])
            ->where('no_tiket', $ticketNumber)
            ->first();

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan'
            ], 404);
        }

        // Parse keterangan_keperluan JSON
        $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);

        // Return data for auto-fill
        return response()->json([
            'success' => true,
            'data' => [
                // Service info
                'service_type' => $submission->jenisLayanan?->nm_layanan ?? 'domain',
                'domain' => $submission->rincian?->nm_domain ?? '',
                
                // Organization info
                'kategori_pemohon' => $keterangan['kategori_pemohon'] ?? '',
                'nama_organisasi' => $keterangan['nama_organisasi'] ?? '',
                
                // Admin contact
                'admin_name' => $keterangan['admin']['name'] ?? '',
                'admin_position' => $keterangan['admin']['position'] ?? '',
                'admin_nip' => $keterangan['admin']['nip'] ?? '',
                'admin_email' => $keterangan['admin']['email'] ?? '',
                'admin_phone' => $keterangan['admin']['phone'] ?? '',
                'admin_telepon_kantor' => $keterangan['admin']['telepon_kantor'] ?? '',
                'admin_alamat_kantor' => $keterangan['admin']['alamat_kantor'] ?? '',
                'admin_alamat_rumah' => $keterangan['admin']['alamat_rumah'] ?? '',
                'kategori_admin' => $keterangan['kategori_admin'] ?? '',
                
                // Tech contact
                'tech_name' => $keterangan['tech']['name'] ?? '',
                'tech_nip' => $keterangan['tech']['nip'] ?? '',
                'tech_nik' => $keterangan['tech']['nik'] ?? '',
                'tech_email' => $keterangan['tech']['email'] ?? '',
                'tech_phone' => $keterangan['tech']['phone'] ?? '',
                'tech_alamat_kantor' => $keterangan['tech']['alamat_kantor'] ?? '',
                'tech_alamat_rumah' => $keterangan['tech']['alamat_rumah'] ?? '',
                'kategori_tech' => $keterangan['kategori_tech'] ?? '',
                
                // VPS specs (if applicable)
                'vps_cpu' => $keterangan['vps']['cpu'] ?? '',
                'vps_ram' => $keterangan['vps']['ram'] ?? '',
                'vps_storage' => $keterangan['vps']['storage'] ?? '',
                'vps_os' => $keterangan['vps']['os'] ?? '',
                'vps_purpose' => $keterangan['vps']['purpose'] ?? '',
                
                // Hosting specs (if applicable)
                'hosting_quota' => $keterangan['hosting']['quota'] ?? '',
                
                // Metadata
                'expired_date' => $submission->rincian?->tgl_expired ?? '',
            ]
        ]);
    }
}
