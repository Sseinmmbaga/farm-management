<?php

namespace App\Console\Commands;

use App\Models\Notifications\NotificationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:cleanup
                            {--days=90 : Delete notifications older than this many days}
                            {--logs-days=30 : Delete notification logs older than this many days}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old notifications and notification logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $notificationDays = (int) $this->option('days');
        $logsDays = (int) $this->option('logs-days');

        $this->info("Cleaning up notifications older than {$notificationDays} days...");

        // Delete old read notifications
        $deletedNotifications = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays($notificationDays))
            ->delete();

        $this->info("Deleted {$deletedNotifications} old notifications.");

        // Delete old notification logs
        $this->info("Cleaning up notification logs older than {$logsDays} days...");

        $deletedLogs = NotificationLog::where('created_at', '<', now()->subDays($logsDays))
            ->delete();

        $this->info("Deleted {$deletedLogs} old notification logs.");

        $this->table(
            ['Type', 'Deleted'],
            [
                ['Notifications', $deletedNotifications],
                ['Notification Logs', $deletedLogs],
            ]
        );

        return Command::SUCCESS;
    }
}
