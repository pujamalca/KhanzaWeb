<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired sessions and reset last_session_id for users with expired sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredTime = Carbon::now()->subMinutes(config('session.lifetime', 120));

        // Remove last_session_id from users whose sessions have expired
        $expiredUsers = DB::table('users')
            ->whereIn('last_session_id', function ($query) use ($expiredTime) {
                $query->select('id')
                    ->from('sessions')
                    ->where('last_activity', '<', $expiredTime->timestamp);
            })
            ->update(['last_session_id' => null]);

        if ($expiredUsers > 0) {
            $this->info("✅ Cleaned up {$expiredUsers} user(s) with expired sessions.");
        } else {
            $this->info("ℹ️  No expired sessions to clean up.");
        }

        // Also cleanup old sessions from sessions table (Laravel's default behavior)
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime->timestamp)
            ->delete();

        if ($deletedSessions > 0) {
            $this->info("✅ Deleted {$deletedSessions} expired session record(s) from database.");
        }

        return Command::SUCCESS;
    }
}
