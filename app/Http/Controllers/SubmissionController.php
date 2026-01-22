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
        // Validate request
        $validated = $request->validate([
            // Data Sub Domain
            'jenis_domain' => 'required|in:lembaga_fakultas,kegiatan_lembaga,organisasi_mahasiswa,kegiatan_mahasiswa,lainnya',
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
            
            // Data Sub Domain yang Diminta
            'requested_domain' => 'required|string|min:2|max:12|regex:/^[a-z0-9\-]+$/',
            'admin_password' => 'required|string|min:6|max:8',
            
            // Hidden fields for DB compatibility
            'unit_id' => 'nullable',
            'application_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'request_type' => 'required|in:domain,hosting,vps',
            
            // VPS specific
            'vps_cpu' => 'nullable|string',
            'vps_ram' => 'nullable|string',
            'vps_storage' => 'nullable|string',
            'vps_os' => 'nullable|string',
            'vps_purpose' => 'nullable|string',
            
            // Hosting specific
            'hosting_quota' => 'nullable|string',
        ], [
            'jenis_domain.required' => 'Jenis domain wajib dipilih.',
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
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
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

            // Create submission detail (rincian_pengajuan)
            SubmissionDetail::create([
                'pengajuan_uuid' => $submission->UUID,
                'nm_domain' => $this->formatDomain($validated['requested_domain']),
                'kapasitas_penyimpanan' => $validated['hosting_quota'] ?? $validated['vps_storage'] ?? null,
                'keterangan_keperluan' => $keterangan,
                'id_creator' => $user->UUID,
            ]);

            // Create log (riwayat_pengajuan)
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_baru_uuid' => $status->UUID,
                'catatan_log' => 'Formulir pengajuan dibuat.',
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
            'jenis_domain' => $validated['jenis_domain'],
            'nama_organisasi' => $validated['nama_organisasi'],
            'admin' => [
                'name' => $validated['admin_responsible_name'],
                'position' => $validated['admin_responsible_position'],
                'nip' => $validated['admin_responsible_nip'] ?? null,
                'email' => $validated['admin_email'],
                'phone' => $validated['admin_responsible_phone'],
            ],
            'tech' => [
                'name' => $validated['tech_name'],
                'nip' => $validated['tech_nip'],
                'email' => $validated['tech_email'],
                'phone' => $validated['tech_phone'],
            ],
            'password_hint' => $validated['admin_password'],
        ];
        
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
     * Format domain name
     */
    private function formatDomain(?string $domain): ?string
    {
        if (!$domain) return null;
        
        // Remove .unila.ac.id if user included it
        $domain = preg_replace('/\.unila\.ac\.id$/i', '', trim($domain));
        
        // Clean up
        $domain = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', $domain));
        
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
        
        // Admin, Verifikator, Eksekutor can access all
        $userRole = $user->peran?->nm_peran ?? '';
        if (in_array(strtolower($userRole), ['admin', 'verifikator', 'eksekutor'])) {
            return;
        }
        
        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }
}
