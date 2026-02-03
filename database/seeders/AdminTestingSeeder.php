<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\JenisLayanan;
use App\Models\StatusPengajuan;
use App\Models\Submission;
use App\Models\SubmissionDetail;
use App\Models\SubmissionLog;
use App\Models\LoginLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * AdminTestingSeeder - Comprehensive seeder untuk testing fitur Admin
 * 
 * Seeder ini membuat data testing yang komprehensif untuk menguji semua fitur admin:
 * - Submissions dengan berbagai status dan kondisi
 * - Submission Logs (Audit trail pengajuan)
 * - Login History Simulation (menggunakan LoginLog)
 * - Filter & Search Functionality
 * 
 * CATATAN PENTING:
 * - Seeder ini TIDAK membuat users (users dibuat oleh seeder spesifik: AdminSeeder, VerifikatorSeeder, dll)
 * - Seeder ini HANYA membuat: Submissions, SubmissionDetails, SubmissionLogs, dan LoginLogs
 * - Harus dijalankan SETELAH semua user seeder
 * 
 * CARA PENGGUNAAN:
 * php artisan db:seed --class=AdminTestingSeeder
 */
class AdminTestingSeeder extends Seeder
{
    private $units = [];
    private $serviceTypes = [];
    private $statuses = [];
    
    private $stats = [
        'submissions' => 0,
        'submission_logs' => 0,
        'login_logs' => 0,
    ];

    public function run(): void
    {
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║     ADMIN TESTING SEEDER - Comprehensive Test Data        ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        $this->loadReferenceData();
        $this->createTestSubmissions();
        $this->simulateLoginActivities();
        $this->displaySummary();
    }

    private function loadReferenceData(): void
    {
        $this->command->info('📚 Loading reference data...');
        
        Unit::all()->each(function($unit) {
            $this->units[] = $unit;
        });
        
        JenisLayanan::all()->each(function($service) {
            $this->serviceTypes[$service->nm_layanan] = $service;
        });
        
        StatusPengajuan::all()->each(function($status) {
            $this->statuses[$status->nm_status] = $status;
        });
        
        $this->command->info('✓ Reference data loaded');
        $this->command->newLine();
    }

    private function createTestSubmissions(): void
    {
        $this->command->info('📝 Creating test submissions...');

        $regularUsers = User::whereHas('peran', function($q) {
            $q->where('nm_peran', 'Pengguna');
        })->get();

        if ($regularUsers->isEmpty()) {
            $this->command->warn('⚠ No regular users found, skipping submissions');
            return;
        }

        $this->createDraftSubmissions($regularUsers);
        $this->createSubmittedSubmissions($regularUsers);
        $this->createApprovedSubmissions($regularUsers);
        $this->createRejectedSubmissions($regularUsers);
        $this->createInProgressSubmissions($regularUsers);
        $this->createCompletedSubmissions($regularUsers);
        $this->createMixedServiceTypeSubmissions($regularUsers);

        $this->command->info("✓ Created {$this->stats['submissions']} test submissions");
        $this->command->info("✓ Created {$this->stats['submission_logs']} submission logs");
        $this->command->newLine();
    }

    private function createDraftSubmissions($users): void
    {
        $draftUsers = $users->random(min(3, $users->count()));
        
        foreach ($draftUsers as $user) {
            $this->createSubmission([
                'user' => $user,
                'service' => 'domain',
                'status' => 'Draft',
                'days_ago' => rand(1, 5),
                'domain_name' => 'draft-' . strtolower(str_replace(' ', '', $user->nm)),
            ]);
        }
    }

    private function createSubmittedSubmissions($users): void
    {
        $submittedUsers = $users->random(min(5, $users->count()));
        $services = ['domain', 'hosting', 'vps'];
        
        foreach ($submittedUsers as $user) {
            $service = $services[array_rand($services)];
            
            $submission = $this->createSubmission([
                'user' => $user,
                'service' => $service,
                'status' => 'Diajukan',
                'days_ago' => rand(1, 10),
                'domain_name' => strtolower(str_replace(' ', '', $user->nm)) . '-' . $service,
            ]);

            if ($submission) {
                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted oleh user');
            }
        }
    }

    private function createApprovedSubmissions($users): void
    {
        $approvedUsers = $users->random(min(5, $users->count()));
        $verifikator = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Verifikator'))->first();
        
        if (!$verifikator) {
            $this->command->warn('⚠ No verifikator found, skipping approved submissions');
            return;
        }
        
        foreach ($approvedUsers as $user) {
            $service = ['domain', 'hosting', 'vps'][array_rand(['domain', 'hosting', 'vps'])];
            
            $submission = $this->createSubmission([
                'user' => $user,
                'service' => $service,
                'status' => 'Disetujui Verifikator',
                'days_ago' => rand(5, 15),
                'domain_name' => 'approved-' . strtolower(str_replace(' ', '', $user->nm)),
            ]);

            if ($submission) {
                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted');
                $this->createSubmissionLog($submission, 'Diajukan', 'Disetujui Verifikator', $verifikator, 
                    'Pengajuan telah diverifikasi dan disetujui');
            }
        }
    }

    private function createRejectedSubmissions($users): void
    {
        $rejectedUsers = $users->random(min(3, $users->count()));
        $verifikator = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Verifikator'))->first();
        
        if (!$verifikator) {
            return;
        }
        
        foreach ($rejectedUsers as $user) {
            $reasons = [
                'Domain tidak sesuai naming convention',
                'Dokumen pendukung tidak lengkap',
                'Tujuan penggunaan tidak jelas',
                'Unit kerja tidak valid'
            ];
            
            $submission = $this->createSubmission([
                'user' => $user,
                'service' => 'domain',
                'status' => 'Ditolak Verifikator',
                'days_ago' => rand(3, 20),
                'domain_name' => 'rejected-' . strtolower(str_replace(' ', '', $user->nm)),
            ]);

            if ($submission) {
                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted');
                $this->createSubmissionLog($submission, 'Diajukan', 'Ditolak Verifikator', $verifikator, 
                    $reasons[array_rand($reasons)]);
            }
        }
    }

    private function createInProgressSubmissions($users): void
    {
        $inProgressUsers = $users->random(min(4, $users->count()));
        $verifikator = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Verifikator'))->first();
        $eksekutor = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Eksekutor'))->first();
        
        if (!$verifikator || !$eksekutor) {
            return;
        }
        
        foreach ($inProgressUsers as $user) {
            $service = ['domain', 'hosting', 'vps'][array_rand(['domain', 'hosting', 'vps'])];
            
            $submission = $this->createSubmission([
                'user' => $user,
                'service' => $service,
                'status' => 'Sedang Dikerjakan',
                'days_ago' => rand(1, 7),
                'domain_name' => 'inprogress-' . strtolower(str_replace(' ', '', $user->nm)),
            ]);

            if ($submission) {
                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted');
                $this->createSubmissionLog($submission, 'Diajukan', 'Disetujui Verifikator', $verifikator, 
                    'Disetujui untuk dikerjakan');
                $this->createSubmissionLog($submission, 'Disetujui Verifikator', 'Sedang Dikerjakan', $eksekutor, 
                    'Sedang melakukan provisioning ' . $service);
            }
        }
    }

    private function createCompletedSubmissions($users): void
    {
        $completedUsers = $users->random(min(8, $users->count()));
        $verifikator = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Verifikator'))->first();
        $eksekutor = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Eksekutor'))->first();
        
        if (!$verifikator || !$eksekutor) {
            return;
        }
        
        foreach ($completedUsers as $user) {
            $service = ['domain', 'hosting', 'vps'][array_rand(['domain', 'hosting', 'vps'])];
            
            $submission = $this->createSubmission([
                'user' => $user,
                'service' => $service,
                'status' => 'Selesai',
                'days_ago' => rand(10, 60),
                'domain_name' => 'completed-' . strtolower(str_replace(' ', '', $user->nm)),
            ]);

            if ($submission) {
                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted');
                $this->createSubmissionLog($submission, 'Diajukan', 'Disetujui Verifikator', $verifikator, 
                    'Disetujui untuk dikerjakan');
                $this->createSubmissionLog($submission, 'Disetujui Verifikator', 'Sedang Dikerjakan', $eksekutor, 
                    'Mulai mengerjakan');
                $this->createSubmissionLog($submission, 'Sedang Dikerjakan', 'Selesai', $eksekutor, 
                    ucfirst($service) . ' berhasil di-deploy dan sudah aktif');
            }
        }
    }

    private function createMixedServiceTypeSubmissions($users): void
    {
        $activeUsers = $users->filter(function($user) {
            return $user->last_login_at && $user->last_login_at->diffInDays(now()) <= 7;
        })->take(3);

        if ($activeUsers->isEmpty()) {
            return;
        }

        $verifikator = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Verifikator'))->first();
        $eksekutor = User::whereHas('peran', fn($q) => $q->where('nm_peran', 'Eksekutor'))->first();

        if (!$verifikator || !$eksekutor) {
            return;
        }

        foreach ($activeUsers as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $service = ['domain', 'hosting', 'vps'][$i % 3];
                $statuses = ['Selesai', 'Sedang Dikerjakan', 'Diajukan'];
                $statusName = $statuses[$i % 3];
                
                $submission = $this->createSubmission([
                    'user' => $user,
                    'service' => $service,
                    'status' => $statusName,
                    'days_ago' => rand(1, 30),
                    'domain_name' => $service . $i . '-' . strtolower(str_replace(' ', '', $user->nm)),
                ]);

                if (!$submission) {
                    continue;
                }

                $this->createSubmissionLog($submission, 'Draft', 'Diajukan', $user, 'Pengajuan submitted');
                
                if (in_array($statusName, ['Selesai', 'Sedang Dikerjakan'])) {
                    $this->createSubmissionLog($submission, 'Diajukan', 'Disetujui Verifikator', $verifikator, 'Disetujui');
                }
                
                if ($statusName === 'Selesai') {
                    $this->createSubmissionLog($submission, 'Disetujui Verifikator', 'Sedang Dikerjakan', $eksekutor, 'Mulai');
                    $this->createSubmissionLog($submission, 'Sedang Dikerjakan', 'Selesai', $eksekutor, 'Selesai');
                }
            }
        }
    }

    private function createSubmission(array $config)
    {
        $user = $config['user'];
        $service = $config['service'];
        $statusName = $config['status'];
        $daysAgo = $config['days_ago'];
        $domainName = $config['domain_name'] ?? 'test-domain';

        if (empty($this->units)) {
            $this->command->warn('⚠ No units available, cannot create submission');
            return null;
        }

        if (!isset($this->serviceTypes[$service]) || !isset($this->statuses[$statusName])) {
            $this->command->warn("⚠ Service type or status not found: {$service}, {$statusName}");
            return null;
        }

        $unit = $this->units[array_rand($this->units)];
        $serviceType = $this->serviceTypes[$service];
        $status = $this->statuses[$statusName];

        $submission = Submission::create([
            'UUID' => Str::uuid(),
            'no_tiket' => Submission::generateTicketNumber(),
            'pengguna_uuid' => $user->UUID,
            'unit_kerja_uuid' => $unit->UUID,
            'jenis_layanan_uuid' => $serviceType->UUID,
            'status_uuid' => $status->UUID,
            'tgl_pengajuan' => Carbon::now()->subDays($daysAgo),
            'id_creator' => $user->UUID,
            'id_updater' => $user->UUID,
            'create_at' => Carbon::now()->subDays($daysAgo),
            'last_update' => Carbon::now()->subDays(max(0, $daysAgo - rand(0, 3))),
        ]);

        $this->createSubmissionDetail($submission, $service, $domainName);
        $this->stats['submissions']++;

        return $submission;
    }

    private function createSubmissionDetail($submission, $service, $domainName): void
    {
        $baseData = [
            'UUID' => Str::uuid(),
            'pengajuan_uuid' => $submission->UUID,
            'nm_domain' => $domainName . '.unila.ac.id',
            'keterangan_keperluan' => 'Website ' . ucfirst($service) . ' untuk keperluan akademik',
            'create_at' => $submission->create_at,
        ];

        switch ($service) {
            case 'domain':
                $baseData['alamat_ip'] = '192.168.1.' . rand(1, 254);
                break;
            case 'hosting':
                $baseData['kapasitas_penyimpanan'] = rand(1, 10) . ' GB';
                $baseData['lokasi_server'] = 'Server Unila - Building ' . ['A', 'B', 'C'][array_rand(['A', 'B', 'C'])];
                break;
            case 'vps':
                $baseData['kapasitas_penyimpanan'] = rand(50, 500) . ' GB SSD';
                $baseData['lokasi_server'] = 'VPS Cluster - Node ' . rand(1, 5);
                $baseData['keterangan_keperluan'] = 'VPS dengan RAM ' . rand(2, 16) . ' GB, CPU ' . rand(2, 8) . ' Core untuk keperluan akademik';
                break;
        }

        SubmissionDetail::create($baseData);
    }

    private function createSubmissionLog($submission, $oldStatus, $newStatus, $actor, $notes = null): void
    {
        $oldStatusObj = $this->statuses[$oldStatus] ?? null;
        $newStatusObj = $this->statuses[$newStatus] ?? null;

        if (!$oldStatusObj || !$newStatusObj) {
            return;
        }

        SubmissionLog::create([
            'UUID' => Str::uuid(),
            'pengajuan_uuid' => $submission->UUID,
            'status_lama_uuid' => $oldStatusObj->UUID,
            'status_baru_uuid' => $newStatusObj->UUID,
            'id_creator' => $actor->UUID,
            'catatan_log' => $notes,
            'create_at' => $submission->create_at->addMinutes(rand(5, 120)),
        ]);

        $this->stats['submission_logs']++;
    }

    private function simulateLoginActivities(): void
    {
        $this->command->info('🔄 Simulating login activities...');

        $allUsers = User::all();
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Safari/604.1',
        ];

        foreach ($allUsers as $user) {
            // Simulate successful logins
            if ($user->last_login_at) {
                $loginCount = rand(3, 10);
                for ($i = 0; $i < $loginCount; $i++) {
                    $daysAgo = rand(1, 90);
                    LoginLog::create([
                        'UUID' => Str::uuid(),
                        'pengguna_uuid' => $user->UUID,
                        'alamat_ip' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                        'perangkat' => $userAgents[array_rand($userAgents)],
                        'status_akses' => 'BERHASIL',
                        'keterangan' => 'Login berhasil',
                        'create_at' => Carbon::now()->subDays($daysAgo)->subMinutes(rand(0, 1440)),
                    ]);
                    $this->stats['login_logs']++;
                }
            }

            // Simulate some failed login attempts
            if (rand(1, 100) <= 20) {
                $failedCount = rand(1, 3);
                for ($i = 0; $i < $failedCount; $i++) {
                    $statuses = ['GAGAL_PASSWORD', 'GAGAL_SUSPEND'];
                    $status = $statuses[array_rand($statuses)];
                    LoginLog::create([
                        'UUID' => Str::uuid(),
                        'pengguna_uuid' => $user->UUID,
                        'alamat_ip' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                        'perangkat' => $userAgents[array_rand($userAgents)],
                        'status_akses' => $status,
                        'keterangan' => 'Login gagal - ' . strtolower(str_replace('_', ' ', $status)),
                        'create_at' => Carbon::now()->subDays(rand(1, 30))->subMinutes(rand(0, 1440)),
                    ]);
                    $this->stats['login_logs']++;
                }
            }
        }

        // Simulate SSO login attempts
        $ssoUsers = $allUsers->random(min(10, $allUsers->count()));
        foreach ($ssoUsers as $user) {
            LoginLog::create([
                'UUID' => Str::uuid(),
                'pengguna_uuid' => $user->UUID,
                'alamat_ip' => '10.10.' . rand(1, 254) . '.' . rand(1, 254),
                'perangkat' => $userAgents[array_rand($userAgents)],
                'status_akses' => 'BERHASIL',
                'keterangan' => 'Login via SSO Unila berhasil',
                'create_at' => Carbon::now()->subDays(rand(1, 7)),
            ]);
            $this->stats['login_logs']++;
        }

        $this->command->info("✓ Created {$this->stats['login_logs']} login activity records");
        $this->command->newLine();
    }

    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    SEEDING SUMMARY                        ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->info(sprintf('║  Submissions Created:      %-30s ║', $this->stats['submissions']));
        $this->command->info(sprintf('║  Submission Logs Created:  %-30s ║', $this->stats['submission_logs']));
        $this->command->info(sprintf('║  Login Logs Created:       %-30s ║', $this->stats['login_logs']));
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->info('║                  TESTING SCENARIOS                        ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->info('║  ✓ Submissions in all statuses (Draft to Completed)       ║');
        $this->command->info('║  ✓ All service types (Domain, Hosting, VPS)               ║');
        $this->command->info('║  ✓ Rejected submissions with various reasons              ║');
        $this->command->info('║  ✓ Complete audit trail (submission logs)                 ║');
        $this->command->info('║  ✓ Login history with success and failed attempts         ║');
        $this->command->info('║  ✓ SSO login simulation                                   ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->info('║                  ADMIN FEATURES TO TEST                   ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->info('║  [ ] User Verification & Management                       ║');
        $this->command->info('║  [ ] View User Activity Logs                              ║');
        $this->command->info('║  [ ] Login Audit Logs with Filters                        ║');
        $this->command->info('║  [ ] Submission Audit Logs with Filters                   ║');
        $this->command->info('║  [ ] Search & Filter Functionality                        ║');
        $this->command->info('║  [ ] Date Range Filtering                                 ║');
        $this->command->info('║  [ ] Service Type Filtering                               ║');
        $this->command->info('║  [ ] Status Filtering                                     ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
        $this->command->info('🎉 Admin testing seeder completed successfully!');
        $this->command->newLine();
    }
}
