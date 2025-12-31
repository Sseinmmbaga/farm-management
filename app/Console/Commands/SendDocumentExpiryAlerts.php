<?php

namespace App\Console\Commands;

use App\Services\Notifications\NotificationService;
use Illuminate\Console\Command;

class SendDocumentExpiryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alerts:document-expiry {--days=30 : Days before expiry to send alert}';

    /**
     * The console command description.
     */
    protected $description = 'Send alerts for documents that are expiring soon';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $days = (int) $this->option('days');
        $this->info("Checking for documents expiring within {$days} days...");

        $results = $notificationService->sendDocumentExpiryAlerts($days);

        $this->info("Document expiry alerts completed:");
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
