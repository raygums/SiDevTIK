<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Login History Testing Data
 * 
 * Purpose:
 * - Generate realistic login history data untuk testing
 * - Berbagai skenario: successful, failed, suspicious activity
 * - Data untuk testing dashboard, security monitoring, dan reports
 * 
 * Generated Data:
 * - 500+ login attempts (mix berhasil dan gagal)
 * - Multiple users dengan different patterns
 * - Failed login attempts (password salah, suspended, not found)
 * - Suspicious activity patterns (brute force simulation)
 * - Date range: last 30 days
 * 
 * Usage:
 * - php artisan db:seed --class=LoginHistorySeeder
 * - ATAU call dari DatabaseSeeder
 * 
 * Dependencies:
 * - Requires User data (PeranSeeder & AdminTestingSeeder)
 * 
 * @author Domain TIK Development Team
 * @version 1.0.0
 * @created 2026-02-03
 */
class LoginHistorySeeder extends Seeder
{
    /**
     * Status constants
     */
    private const STATUS_SUCCESS = 'BERHASIL';
    private const STATUS_FAILED_PASSWORD = 'GAGAL_PASSWORD';
    private const STATUS_FAILED_SUSPEND = 'GAGAL_SUSPEND';
    private const STATUS_FAILED_NOT_FOUND = 'GAGAL_NOT_FOUND';
    private const STATUS_FAILED_SSO = 'GAGAL_SSO';

    /**
     * Sample IP addresses untuk testing
     */
    private array $ipAddresses = [
        '192.168.1.1',
        '192.168.1.100',
        '192.168.1.101',
        '192.168.1.102',
        '10.0.0.50',
        '10.0.0.51',
        '172.16.0.10',
        '172.16.0.20',
        '103.23.20.15', // Public IP
        '103.23.20.16', // Public IP
    ];

    /**
     * Sample User Agents
     */
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.43 Mobile Safari/537.36',
    ];

    /**
     * Run the seeder
     */
    public function run(): void
    {
        $this->command->info('🔐 Generating Login History Data...');

        DB::beginTransaction();

        try {
            // ==========================================
            // 1. Get Users for Testing
            // ==========================================
            
            $activeUsers = User::whereHas('peran', function ($q) {
                $q->where('nm_peran', 'Pengguna');
            })
            ->where('a_aktif', true)
            ->limit(20)
            ->get();

            $suspendedUser = User::whereHas('peran', function ($q) {
                $q->where('nm_peran', 'Pengguna');
            })
            ->where('a_aktif', false)
            ->first();

            if ($activeUsers->isEmpty()) {
                $this->command->warn('⚠️  No active users found. Run AdminTestingSeeder first!');
                return;
            }

            $this->command->info("✓ Found {$activeUsers->count()} active users for testing");

            // ==========================================
            // 2. Generate Normal Login Activity (30 days)
            // ==========================================
            
            $this->command->info('📊 Generating normal login activity...');
            
            $normalLogins = 0;
            foreach ($activeUsers as $user) {
                // Each user: 5-15 successful logins dalam 30 hari terakhir
                $loginCount = rand(5, 15);
                
                for ($i = 0; $i < $loginCount; $i++) {
                    $this->createLoginLog([
                        'pengguna_uuid' => $user->UUID,
                        'status_akses' => self::STATUS_SUCCESS,
                        'keterangan' => 'Login berhasil via local authentication',
                        'create_at' => $this->randomDateInLast30Days(),
                    ]);
                    $normalLogins++;
                }
            }

            $this->command->info("✓ Generated {$normalLogins} normal successful logins");

            // ==========================================
            // 3. Generate Failed Login Attempts (Wrong Password)
            // ==========================================
            
            $this->command->info('🔴 Generating failed login attempts (wrong password)...');
            
            $failedPasswordCount = 0;
            $failedAttemptUsers = $activeUsers->random(min(10, $activeUsers->count()));
            foreach ($failedAttemptUsers as $user) {
                // Some users: 1-3 failed attempts (typo, lupa password)
                $failCount = rand(1, 3);
                
                for ($i = 0; $i < $failCount; $i++) {
                    $this->createLoginLog([
                        'pengguna_uuid' => $user->UUID,
                        'status_akses' => self::STATUS_FAILED_PASSWORD,
                        'keterangan' => "Password salah untuk user '{$user->usn}'",
                        'create_at' => $this->randomDateInLast30Days(),
                    ]);
                    $failedPasswordCount++;
                }
            }

            $this->command->info("✓ Generated {$failedPasswordCount} failed password attempts");

            // ==========================================
            // 4. Generate Suspended User Login Attempts
            // ==========================================
            
            if ($suspendedUser) {
                $this->command->info('🚫 Generating suspended user login attempts...');
                
                $suspendedAttempts = 0;
                for ($i = 0; $i < 8; $i++) {
                    $this->createLoginLog([
                        'pengguna_uuid' => $suspendedUser->UUID,
                        'status_akses' => self::STATUS_FAILED_SUSPEND,
                        'keterangan' => "Akun suspended - User '{$suspendedUser->usn}' tidak aktif",
                        'create_at' => $this->randomDateInLast30Days(),
                    ]);
                    $suspendedAttempts++;
                }

                $this->command->info("✓ Generated {$suspendedAttempts} suspended account attempts");
            }

            // ==========================================
            // 5. Generate "User Not Found" Attempts
            // ==========================================
            
            $this->command->info('❌ Generating "user not found" attempts...');
            
            $notFoundAttempts = 0;
            $fakeUsernames = [
                'admin123',
                'root',
                'administrator',
                'test',
                'demo',
                'guest',
                'user123',
                'hackme',
            ];

            foreach ($fakeUsernames as $fakeUsername) {
                $count = rand(1, 3);
                for ($i = 0; $i < $count; $i++) {
                    $this->createLoginLog([
                        'pengguna_uuid' => null, // User tidak ada
                        'status_akses' => self::STATUS_FAILED_NOT_FOUND,
                        'keterangan' => "Login attempt dengan username '{$fakeUsername}' - User tidak terdaftar",
                        'create_at' => $this->randomDateInLast30Days(),
                    ]);
                    $notFoundAttempts++;
                }
            }

            $this->command->info("✓ Generated {$notFoundAttempts} 'user not found' attempts");

            // ==========================================
            // 6. Generate SSO Login Successes
            // ==========================================
            
            $this->command->info('🔐 Generating SSO login successes...');
            
            $ssoLogins = 0;
            $ssoUsers = $activeUsers->random(min(5, $activeUsers->count()));
            foreach ($ssoUsers as $user) {
                // Some users use SSO: 2-5 kali
                $ssoCount = rand(2, 5);
                
                for ($i = 0; $i < $ssoCount; $i++) {
                    $this->createLoginLog([
                        'pengguna_uuid' => $user->UUID,
                        'status_akses' => self::STATUS_SUCCESS,
                        'keterangan' => "Login berhasil via SSO Unila - NIP: {$user->nip}",
                        'create_at' => $this->randomDateInLast30Days(),
                    ]);
                    $ssoLogins++;
                }
            }

            $this->command->info("✓ Generated {$ssoLogins} SSO login successes");

            // ==========================================
            // 7. Generate SSO Failed Attempts
            // ==========================================
            
            $this->command->info('🔴 Generating SSO failed attempts...');
            
            $ssoFailed = 0;
            for ($i = 0; $i < 10; $i++) {
                $this->createLoginLog([
                    'pengguna_uuid' => null,
                    'status_akses' => self::STATUS_FAILED_SSO,
                    'keterangan' => 'SSO authentication failed: Invalid token or session expired',
                    'create_at' => $this->randomDateInLast30Days(),
                ]);
                $ssoFailed++;
            }

            $this->command->info("✓ Generated {$ssoFailed} SSO failed attempts");

            // ==========================================
            // 8. Generate Suspicious Activity (Brute Force Simulation)
            // ==========================================
            
            $this->command->info('⚠️  Generating suspicious activity (brute force simulation)...');
            
            $suspiciousIp = '103.23.20.99'; // Suspicious external IP
            $bruteForceAttempts = 0;

            // Simulate brute force attack: 50 failed attempts dalam 10 menit
            $attackTime = now()->subDays(5)->setHour(14)->setMinute(30);
            
            for ($i = 0; $i < 50; $i++) {
                $this->createLoginLog([
                    'pengguna_uuid' => $activeUsers->random()->UUID,
                    'status_akses' => self::STATUS_FAILED_PASSWORD,
                    'keterangan' => 'Potential brute force attack - Multiple failed attempts',
                    'alamat_ip' => $suspiciousIp, // Same IP
                    'create_at' => $attackTime->copy()->addSeconds($i * 10), // Every 10 seconds
                ]);
                $bruteForceAttempts++;
            }

            $this->command->info("✓ Generated {$bruteForceAttempts} suspicious brute force attempts");

            // ==========================================
            // 9. Generate Today's Activity
            // ==========================================
            
            $this->command->info('📅 Generating today\'s activity...');
            
            $todayLogins = 0;
            $todayUsers = $activeUsers->random(min(8, $activeUsers->count()));
            foreach ($todayUsers as $user) {
                // Some users logged in today
                $this->createLoginLog([
                    'pengguna_uuid' => $user->UUID,
                    'status_akses' => self::STATUS_SUCCESS,
                    'keterangan' => 'Login berhasil via local authentication',
                    'create_at' => today()->addHours(rand(8, 17))->addMinutes(rand(0, 59)),
                ]);
                $todayLogins++;
            }

            // A few failed attempts today
            for ($i = 0; $i < 5; $i++) {
                $this->createLoginLog([
                    'pengguna_uuid' => $activeUsers->random()->UUID,
                    'status_akses' => self::STATUS_FAILED_PASSWORD,
                    'keterangan' => 'Password salah',
                    'create_at' => today()->addHours(rand(8, 17))->addMinutes(rand(0, 59)),
                ]);
            }

            $this->command->info("✓ Generated {$todayLogins} successful logins today (+ 5 failed)");

            // ==========================================
            // SUMMARY
            // ==========================================
            
            DB::commit();

            $totalLogs = LoginLog::count();
            $successfulLogs = LoginLog::where('status_akses', self::STATUS_SUCCESS)->count();
            $failedLogs = LoginLog::where('status_akses', '!=', self::STATUS_SUCCESS)->count();

            $this->command->newLine();
            $this->command->info('✅ Login History Seeder Completed!');
            $this->command->newLine();
            $this->command->table(
                ['Metric', 'Count'],
                [
                    ['Total Login Attempts', $totalLogs],
                    ['Successful Logins', $successfulLogs],
                    ['Failed Attempts', $failedLogs],
                    ['Success Rate', round(($successfulLogs / $totalLogs) * 100, 2) . '%'],
                    ['Users with Login History', $activeUsers->count()],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create login log dengan randomized IP dan User Agent
     */
    private function createLoginLog(array $data): void
    {
        $defaults = [
            'alamat_ip' => $this->ipAddresses[array_rand($this->ipAddresses)],
            'perangkat' => $this->userAgents[array_rand($this->userAgents)],
        ];

        LoginLog::create(array_merge($defaults, $data));
    }

    /**
     * Generate random timestamp dalam 30 hari terakhir
     */
    private function randomDateInLast30Days(): \Carbon\Carbon
    {
        $daysAgo = rand(0, 30);
        $hour = rand(7, 22); // Working hours: 7 AM - 10 PM
        $minute = rand(0, 59);
        $second = rand(0, 59);

        return now()
            ->subDays($daysAgo)
            ->setHour($hour)
            ->setMinute($minute)
            ->setSecond($second);
    }
}
