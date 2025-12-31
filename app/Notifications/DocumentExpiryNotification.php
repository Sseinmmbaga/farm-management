<?php

namespace App\Notifications;

use App\Models\Farmers\Farmer;
use App\Models\Farmers\FarmerDocument;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentExpiryNotification extends BaseNotification
{
    protected string $category = 'document';

    public function __construct(
        public FarmerDocument $document,
        public Farmer $farmer
    ) {}

    public function getType(): string
    {
        return 'document_expiry';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysRemaining = now()->diffInDays($this->document->expiry_date, false);
        $daysText = $daysRemaining > 0 ? "{$daysRemaining} days" : "expired";

        return (new MailMessage)
            ->subject("Document Expiring Soon - {$this->farmer->full_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that a farmer's document is expiring soon.")
            ->line("**Farmer:** {$this->farmer->full_name}")
            ->line("**Farmer Code:** {$this->farmer->farmer_code}")
            ->line("**Document Type:** {$this->document->type_label}")
            ->line("**Document Title:** {$this->document->title}")
            ->line("**Expiry Date:** {$this->document->expiry_date->format('M d, Y')}")
            ->line("**Status:** {$daysText}")
            ->action('View Farmer Documents', url("/farmers/{$this->farmer->id}"))
            ->line('Please take necessary action to update this document.')
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $daysRemaining = now()->diffInDays($this->document->expiry_date, false);
        return "REMEI ALERT: {$this->document->type_label} for {$this->farmer->full_name} expires in {$daysRemaining} days. Please update.";
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $daysRemaining = now()->diffInDays($this->document->expiry_date, false);

        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'title' => 'Document Expiring Soon',
            'message' => "{$this->document->type_label} for {$this->farmer->full_name} expires on {$this->document->expiry_date->format('M d, Y')}",
            'farmer_id' => $this->farmer->id,
            'farmer_name' => $this->farmer->full_name,
            'farmer_code' => $this->farmer->farmer_code,
            'document_id' => $this->document->id,
            'document_type' => $this->document->type,
            'document_type_label' => $this->document->type_label,
            'document_title' => $this->document->title,
            'expiry_date' => $this->document->expiry_date->toDateString(),
            'days_remaining' => $daysRemaining,
            'priority' => $daysRemaining <= 7 ? 'high' : ($daysRemaining <= 14 ? 'medium' : 'low'),
            'action_url' => "/farmers/{$this->farmer->id}",
            'icon' => 'fa-file-alt',
            'color' => $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 14 ? 'warning' : 'info'),
        ];
    }
}
