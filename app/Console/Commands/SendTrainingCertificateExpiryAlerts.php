<?php

namespace App\Console\Commands;

use App\Services\Notifications\NotificationService;
use Illuminate\Console\Command;

class SendTrainingCertificateExpiryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alerts:training-certificate-expiry {--days=30 : Days before expiry to send alert}';

    /**
     * The console command description.
     */
    protected $description = 'Send alerts for training certificates that are expiring soon';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $days = (int) $this->option('days');
        $this->info("Checking for training certificates expiring within {$days} days...");

        $results = $notificationService->sendTrainingCertificateExpiryAlerts($days);

        $this->info("Training certificate expiry alerts completed:");
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
