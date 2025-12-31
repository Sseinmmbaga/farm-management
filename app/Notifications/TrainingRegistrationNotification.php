<?php

namespace App\Notifications;

use App\Models\Training\TrainingSession;
use App\Models\Farmers\Farmer;
use Illuminate\Notifications\Messages\MailMessage;

class TrainingRegistrationNotification extends BaseNotification
{
    protected string $category = 'training';

    public function __construct(
        public TrainingSession $session,
        public Farmer $farmer
    ) {}

    public function getType(): string
    {
        return 'training_registration';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $programName = $this->session->program?->name ?? 'Training';

        return (new MailMessage)
            ->subject("Training Registration Confirmed - {$this->session->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been registered for a training session.")
            ->line("**Training:** {$this->session->title}")
            ->line("**Program:** {$programName}")
            ->line("**Date:** {$this->session->scheduled_date->format('M d, Y')}")
            ->line("**Time:** {$this->session->scheduled_date->format('h:i A')}")
            ->line("**Venue:** {$this->session->venue}")
            ->when($this->session->trainer_name, function ($mail) {
                return $mail->line("**Trainer:** {$this->session->trainer_name}");
            })
            ->action('View Training Details', url("/training/sessions/{$this->session->id}"))
            ->line('Please make sure to attend this training session on the scheduled date.')
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $date = $this->session->scheduled_date->format('M d');
        $time = $this->session->scheduled_date->format('h:i A');

        return "REMEI: You have been registered for training '{$this->session->title}' on {$date} at {$time}. Venue: {$this->session->venue}";
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'title' => 'Training Registration',
            'message' => "You have been registered for {$this->session->title} on {$this->session->scheduled_date->format('M d, Y')}",
            'session_id' => $this->session->id,
            'session_title' => $this->session->title,
            'program_id' => $this->session->training_program_id,
            'program_name' => $this->session->program?->name,
            'scheduled_date' => $this->session->scheduled_date->toDateTimeString(),
            'venue' => $this->session->venue,
            'trainer_name' => $this->session->trainer_name,
            'farmer_id' => $this->farmer->id,
            'farmer_name' => $this->farmer->full_name,
            'priority' => 'medium',
            'action_url' => "/training/sessions/{$this->session->id}",
            'icon' => 'fa-user-plus',
            'color' => 'success',
        ];
    }
}
