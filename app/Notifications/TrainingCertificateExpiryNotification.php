<?php

namespace App\Notifications;

use App\Models\Farmers\Farmer;
use App\Models\Training\TrainingCertificate;
use Illuminate\Notifications\Messages\MailMessage;

class TrainingCertificateExpiryNotification extends BaseNotification
{
    protected string $category = 'training';

    public function __construct(
        public TrainingCertificate $certificate,
        public Farmer $farmer
    ) {}

    public function getType(): string
    {
        return 'training_certificate_expiry';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysRemaining = now()->diffInDays($this->certificate->expiry_date, false);
        $daysText = $daysRemaining > 0 ? "{$daysRemaining} days" : "expired";
        $programName = $this->certificate->program?->name ?? 'Training';

        return (new MailMessage)
            ->subject("Training Certificate Expiring - {$this->farmer->full_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that a farmer's training certificate is expiring soon.")
            ->line("**Farmer:** {$this->farmer->full_name}")
            ->line("**Farmer Code:** {$this->farmer->farmer_code}")
            ->line("**Certificate Number:** {$this->certificate->certificate_number}")
            ->line("**Training Program:** {$programName}")
            ->line("**Issue Date:** {$this->certificate->issue_date->format('M d, Y')}")
            ->line("**Expiry Date:** {$this->certificate->expiry_date->format('M d, Y')}")
            ->line("**Status:** {$daysText}")
            ->action('View Certificate', url("/training/certificates/{$this->certificate->id}"))
            ->line('Please consider scheduling a refresher training for this farmer.')
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $daysRemaining = now()->diffInDays($this->certificate->expiry_date, false);

        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'title' => 'Training Certificate Expiring',
            'message' => "Training certificate for {$this->farmer->full_name} ({$this->certificate->program?->name}) expires on {$this->certificate->expiry_date->format('M d, Y')}",
            'farmer_id' => $this->farmer->id,
            'farmer_name' => $this->farmer->full_name,
            'farmer_code' => $this->farmer->farmer_code,
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'program_id' => $this->certificate->training_program_id,
            'program_name' => $this->certificate->program?->name,
            'issue_date' => $this->certificate->issue_date->toDateString(),
            'expiry_date' => $this->certificate->expiry_date->toDateString(),
            'days_remaining' => $daysRemaining,
            'priority' => $daysRemaining <= 7 ? 'high' : ($daysRemaining <= 14 ? 'medium' : 'low'),
            'action_url' => "/training/certificates/{$this->certificate->id}",
            'icon' => 'fa-award',
            'color' => $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 14 ? 'warning' : 'info'),
        ];
    }
}
