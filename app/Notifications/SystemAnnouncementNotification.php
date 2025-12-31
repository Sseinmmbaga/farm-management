<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class SystemAnnouncementNotification extends BaseNotification
{
    protected string $category = 'system';

    public function __construct(
        public string $title,
        public string $message,
        public string $priority = 'normal', // low, normal, high
        public ?string $actionUrl = null,
        public ?string $actionText = null
    ) {}

    public function getType(): string
    {
        return 'system_announcement';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("System Announcement: {$this->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message);

        if ($this->actionUrl && $this->actionText) {
            $mail->action($this->actionText, url($this->actionUrl));
        }

        return $mail->salutation('Remei Farm OS');
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'icon' => 'fa-bullhorn',
            'color' => match ($this->priority) {
                'high' => 'danger',
                'normal' => 'primary',
                default => 'secondary',
            },
        ];
    }
}
