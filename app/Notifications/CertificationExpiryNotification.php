<?php

namespace App\Notifications;

use App\Models\Farmers\Farmer;
use App\Models\ICS\FarmerCertification;
use Illuminate\Notifications\Messages\MailMessage;

class CertificationExpiryNotification extends BaseNotification
{
    protected string $category = 'certification';

    public function __construct(
        public FarmerCertification $certification,
        public Farmer $farmer
    ) {}

    public function getType(): string
    {
        return 'certification_expiry';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysRemaining = now()->diffInDays($this->certification->expiry_date, false);
        $daysText = $daysRemaining > 0 ? "{$daysRemaining} days" : "expired";

        return (new MailMessage)
            ->subject("Certification Expiring Soon - {$this->farmer->full_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that a farmer's certification is expiring soon.")
            ->line("**Farmer:** {$this->farmer->full_name}")
            ->line("**Farmer Code:** {$this->farmer->farmer_code}")
            ->line("**Certificate Number:** {$this->certification->certificate_number}")
            ->line("**Expiry Date:** {$this->certification->expiry_date->format('M d, Y')}")
            ->line("**Status:** {$daysText}")
            ->action('View Farmer Details', url("/farmers/{$this->farmer->id}"))
            ->line('Please take necessary action to renew this certification.')
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $daysRemaining = now()->diffInDays($this->certification->expiry_date, false);
        return "REMEI ALERT: Certification for {$this->farmer->full_name} expires in {$daysRemaining} days ({$this->certification->expiry_date->format('M d')}). Please take action.";
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $daysRemaining = now()->diffInDays($this->certification->expiry_date, false);

        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'title' => 'Certification Expiring Soon',
            'message' => "Certification for {$this->farmer->full_name} expires on {$this->certification->expiry_date->format('M d, Y')}",
            'farmer_id' => $this->farmer->id,
            'farmer_name' => $this->farmer->full_name,
            'farmer_code' => $this->farmer->farmer_code,
            'certification_id' => $this->certification->id,
            'certificate_number' => $this->certification->certificate_number,
            'expiry_date' => $this->certification->expiry_date->toDateString(),
            'days_remaining' => $daysRemaining,
            'priority' => $daysRemaining <= 7 ? 'high' : ($daysRemaining <= 14 ? 'medium' : 'low'),
            'action_url' => "/farmers/{$this->farmer->id}",
            'icon' => 'fa-certificate',
            'color' => $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 14 ? 'warning' : 'info'),
        ];
    }
}
