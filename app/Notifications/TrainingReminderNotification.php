<?php

namespace App\Notifications;

use App\Models\Training\TrainingSession;
use Illuminate\Notifications\Messages\MailMessage;

class TrainingReminderNotification extends BaseNotification
{
    protected string $category = 'training';

    public function __construct(
        public TrainingSession $session,
        public string $reminderType = 'upcoming' // upcoming, tomorrow, today
    ) {}

    public function getType(): string
    {
        return 'training_reminder';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysUntil = now()->diffInDays($this->session->scheduled_date, false);
        $programName = $this->session->program?->name ?? 'Training';

        $subject = match ($this->reminderType) {
            'today' => "Training Today - {$this->session->title}",
            'tomorrow' => "Training Tomorrow - {$this->session->title}",
            default => "Upcoming Training - {$this->session->title}",
        };

        $urgencyLine = match ($this->reminderType) {
            'today' => "This training is happening **TODAY**!",
            'tomorrow' => "This training is happening **TOMORROW**!",
            default => "This training is scheduled in **{$daysUntil} days**.",
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($urgencyLine)
            ->line("**Training:** {$this->session->title}")
            ->line("**Program:** {$programName}")
            ->line("**Date:** {$this->session->scheduled_date->format('M d, Y')}")
            ->line("**Time:** {$this->session->scheduled_date->format('h:i A')}")
            ->line("**Venue:** {$this->session->venue}")
            ->when($this->session->trainer_name, function ($mail) {
                return $mail->line("**Trainer:** {$this->session->trainer_name}");
            })
            ->action('View Training Details', url("/training/sessions/{$this->session->id}"))
            ->line('Please make sure to attend this training session.')
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $timing = match ($this->reminderType) {
            'today' => 'TODAY',
            'tomorrow' => 'TOMORROW',
            default => "on {$this->session->scheduled_date->format('M d')}",
        };

        return "REMEI TRAINING: {$this->session->title} {$timing} at {$this->session->venue}. Time: {$this->session->scheduled_date->format('h:i A')}";
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $daysUntil = now()->diffInDays($this->session->scheduled_date, false);

        $title = match ($this->reminderType) {
            'today' => 'Training Today',
            'tomorrow' => 'Training Tomorrow',
            default => 'Upcoming Training',
        };

        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'reminder_type' => $this->reminderType,
            'title' => $title,
            'message' => "{$this->session->title} is scheduled for {$this->session->scheduled_date->format('M d, Y')} at {$this->session->venue}",
            'session_id' => $this->session->id,
            'session_title' => $this->session->title,
            'program_id' => $this->session->training_program_id,
            'program_name' => $this->session->program?->name,
            'scheduled_date' => $this->session->scheduled_date->toDateTimeString(),
            'venue' => $this->session->venue,
            'trainer_name' => $this->session->trainer_name,
            'days_until' => $daysUntil,
            'priority' => $this->reminderType === 'today' ? 'high' : ($this->reminderType === 'tomorrow' ? 'medium' : 'low'),
            'action_url' => "/training/sessions/{$this->session->id}",
            'icon' => 'fa-chalkboard-teacher',
            'color' => $this->reminderType === 'today' ? 'danger' : ($this->reminderType === 'tomorrow' ? 'warning' : 'info'),
        ];
    }
}
