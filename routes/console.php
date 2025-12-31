<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Notification Jobs
|--------------------------------------------------------------------------
|
| These scheduled jobs run automatically to send notifications for
| document expiry, certification expiry, and training reminders.
|
*/

// Daily at 8:00 AM - Send certification expiry alerts (30 days warning)
Schedule::command('alerts:certification-expiry --days=30')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/certification-alerts.log'));

// Daily at 8:15 AM - Send document expiry alerts (30 days warning)
Schedule::command('alerts:document-expiry --days=30')
    ->dailyAt('08:15')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/document-alerts.log'));

// Daily at 8:30 AM - Send training reminders (7 days warning)
Schedule::command('alerts:training-reminders --days=7')
    ->dailyAt('08:30')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/training-reminders.log'));

// Daily at 8:45 AM - Send training certificate expiry alerts (30 days warning)
Schedule::command('alerts:training-certificate-expiry --days=30')
    ->dailyAt('08:45')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/training-certificate-alerts.log'));

// Weekly on Sunday at 2:00 AM - Cleanup old notifications
Schedule::command('notifications:cleanup --days=90 --logs-days=30')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/notifications-cleanup.log'));

// Additional daily check at 6:00 PM for urgent reminders (training tomorrow/today)
Schedule::command('alerts:training-reminders --days=1')
    ->dailyAt('18:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/training-reminders-urgent.log'));
