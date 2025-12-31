<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Models\Notifications\NotificationLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $category = 'system';

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof User) {
            $preferences = $notifiable->getNotificationPreferences();

            if ($preferences->shouldNotify($this->category, 'database')) {
                $channels[] = 'database';
            }

            if ($preferences->shouldNotify($this->category, 'mail')) {
                $channels[] = 'mail';
            }

            // SMS channel
            if ($preferences->shouldNotify($this->category, 'sms') && $notifiable->phone) {
                $channels[] = SmsChannel::class;
            }
        } else {
            // Default channels for non-user notifiables
            $channels = ['database', 'mail'];
        }

        return $channels;
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): ?string
    {
        // Override in child classes to provide SMS message
        return null;
    }

    /**
     * Get the type for database storage.
     */
    abstract public function getType(): string;

    /**
     * Get the notification category for filtering.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Log the notification.
     */
    protected function logNotification(
        string $channel,
        string $status,
        ?User $user = null,
        ?string $subject = null,
        ?string $message = null,
        ?array $metadata = null,
        ?string $errorMessage = null
    ): NotificationLog {
        return NotificationLog::log(
            type: $this->getType(),
            channel: $channel,
            status: $status,
            userId: $user?->id,
            recipientEmail: $user?->email,
            recipientPhone: $user?->phone,
            subject: $subject,
            message: $message,
            metadata: $metadata,
            errorMessage: $errorMessage
        );
    }
}
