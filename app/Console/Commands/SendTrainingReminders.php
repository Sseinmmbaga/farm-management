<?php

namespace App\Console\Commands;

use App\Services\Notifications\NotificationService;
use Illuminate\Console\Command;

class SendTrainingReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alerts:training-reminders {--days=7 : Days before training to send reminder}';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for upcoming training sessions';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $days = (int) $this->option('days');
        $this->info("Checking for training sessions within {$days} days...");

        $results = $notificationService->sendTrainingReminders($days);

        $this->info("Training reminders completed:");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Sent', $results['sent']],
                ['Failed', $results['failed']],
                ['Skipped', $results['skipped']],
            ]
        );

        return Command::SUCCESS;
    }
}
