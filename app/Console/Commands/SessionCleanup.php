<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SessionCleanup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'session:cleanup 
                           {--hours=24 : Delete sessions older than X hours}
                           {--user= : Cleanup sessions for specific user UUID}
                           {--all : Delete all sessions (force logout all users)}';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup expired or problematic sessions from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $userUuid = $this->option('user');
        $all = $this->option('all');

        $table = config('session.table', 'sessions');

        // Cleanup all sessions (force logout everyone)
        if ($all) {
            if (!$this->confirm('This will logout ALL users. Are you sure?')) {
                $this->info('Cancelled.');
                return Command::FAILURE;
            }

            $deleted = DB::table($table)->delete();
            $this->info("Deleted all {$deleted} session(s). All users have been logged out.");
            return Command::SUCCESS;
        }

        // Cleanup specific user's sessions
        if ($userUuid) {
            $deleted = DB::table($table)
                ->where('user_id', $userUuid)
                ->delete();

            $this->info("Deleted {$deleted} session(s) for user {$userUuid}.");
            return Command::SUCCESS;
        }

        // Cleanup expired sessions (default)
        $timestamp = now()->subHours($hours)->timestamp;
        $this->info("Cleaning up sessions older than {$hours} hours...");

        $deleted = DB::table($table)
            ->where('last_activity', '<', $timestamp)
            ->delete();

        $this->info("Deleted {$deleted} expired session(s).");

        return Command::SUCCESS;
    }
}
