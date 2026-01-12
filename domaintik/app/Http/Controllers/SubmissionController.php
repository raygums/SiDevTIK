<?php

namespace App\Http\Controllers;

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
        $submissions = Submission::with(['unit', 'details'])
            ->where('applicant_id', Auth::id())
            ->orderBy('created_at', 'desc')
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

            // Create submission
            $submission = Submission::create([
                'ticket_number' => Submission::generateTicketNumber(),
                'applicant_id' => Auth::id(),
                'unit_id' => $validated['unit_id'] ?? null,
                'admin_responsible_name' => $validated['admin_responsible_name'],
                'admin_responsible_nip' => $validated['admin_responsible_nip'] ?? null,
                'admin_responsible_position' => $validated['admin_responsible_position'],
                'admin_responsible_phone' => $validated['admin_responsible_phone'],
                'application_name' => $validated['nama_organisasi'],
                'description' => 'Permohonan Sub Domain untuk ' . $validated['nama_organisasi'],
                'status' => Submission::STATUS_DRAFT,
                
                // Extended data stored as JSON in notes or separate columns
                'metadata' => json_encode([
                    'jenis_domain' => $validated['jenis_domain'],
                    'nama_organisasi' => $validated['nama_organisasi'],
                    'admin' => [
                        'name' => $validated['admin_responsible_name'],
                        'position' => $validated['admin_responsible_position'],
                        'nip' => $validated['admin_responsible_nip'] ?? null,
                        'alamat_kantor' => $validated['admin_alamat_kantor'] ?? null,
                        'alamat_rumah' => $validated['admin_alamat_rumah'] ?? null,
                        'telepon_kantor' => $validated['admin_telepon_kantor'] ?? null,
                        'telepon_rumah' => $validated['admin_responsible_phone'],
                        'email' => $validated['admin_email'],
                    ],
                    'tech' => [
                        'name' => $validated['tech_name'],
                        'nip' => $validated['tech_nip'],
                        'phone' => $validated['tech_phone'],
                        'alamat_kantor' => $validated['tech_alamat_kantor'] ?? null,
                        'alamat_rumah' => $validated['tech_alamat_rumah'] ?? null,
                        'email' => $validated['tech_email'],
                    ],
                ]),
            ]);

            // Create submission detail
            SubmissionDetail::create([
                'submission_id' => $submission->id,
                'request_type' => $validated['request_type'],
                'requested_domain' => $this->formatDomain($validated['requested_domain']),
                'requested_quota_gb' => null,
                'initial_password_hint' => $validated['admin_password'],
            ]);

            // Create log
            SubmissionLog::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'note' => 'Formulir pengajuan dibuat.',
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('submissions.download-form', $submission)
                ->with('success', 'Formulir berhasil dibuat! Silakan download, cetak, dan minta tanda tangan atasan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show download form page
     */
    public function downloadForm(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        $submission->load(['applicant', 'unit.category', 'details']);

        return view('submissions.download-form', compact('submission'));
    }

    /**
     * Show printable form page (for PDF generation)
     */
    public function printForm(Submission $submission)
    {
        $this->authorizeAccess($submission);
        
        $submission->load(['applicant', 'unit.category', 'details']);

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
            // Store files
            $signedFormPath = $request->file('signed_form')
                ->store("submissions/{$submission->id}", 'public');
            
            $identityPath = $request->file('identity_attachment')
                ->store("submissions/{$submission->id}", 'public');

            // Update submission
            $submission->update([
                'signed_form_path' => $signedFormPath,
                'attachment_identity_path' => $identityPath,
                'status' => Submission::STATUS_SUBMITTED,
            ]);

            // Create log
            SubmissionLog::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'submitted',
                'note' => 'Dokumen diupload dan pengajuan dikirim untuk verifikasi.',
                'created_at' => now(),
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
        
        $submission->load(['applicant', 'unit.category', 'details', 'logs.user']);

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
        if ($submission->applicant_id === $user->id) {
            return;
        }
        
        // Admin, Verifikator, Eksekutor can access all
        if (in_array($user->role, ['admin', 'verifikator', 'eksekutor'])) {
            return;
        }
        
        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }
}
