<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestNotification extends BaseNotification
{
    protected string $category = 'service_request';

    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $action = 'created' // created, assigned, updated, resolved, closed
    ) {}

    public function getType(): string
    {
        return 'service_request';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->action) {
            'created' => "New Service Request - #{$this->serviceRequest->id}",
            'assigned' => "Service Request Assigned - #{$this->serviceRequest->id}",
            'updated' => "Service Request Updated - #{$this->serviceRequest->id}",
            'resolved' => "Service Request Resolved - #{$this->serviceRequest->id}",
            'closed' => "Service Request Closed - #{$this->serviceRequest->id}",
            default => "Service Request Update - #{$this->serviceRequest->id}",
        };

        $actionLine = match ($this->action) {
            'created' => "A new service request has been created.",
            'assigned' => "A service request has been assigned to you.",
            'updated' => "A service request you are following has been updated.",
            'resolved' => "A service request has been marked as resolved.",
            'closed' => "A service request has been closed.",
            default => "A service request has been updated.",
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($actionLine)
            ->line("**Request ID:** #{$this->serviceRequest->id}")
            ->line("**Title:** {$this->serviceRequest->title}")
            ->line("**Priority:** " . ucfirst($this->serviceRequest->priority))
            ->line("**Status:** " . ucfirst($this->serviceRequest->status))
            ->when($this->serviceRequest->description, function ($mail) {
                return $mail->line("**Description:** " . \Str::limit($this->serviceRequest->description, 100));
            })
            ->action('View Request', url("/service-requests/{$this->serviceRequest->id}"))
            ->salutation('Remei Farm OS');
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $title = match ($this->action) {
            'created' => 'New Service Request',
            'assigned' => 'Service Request Assigned',
            'updated' => 'Service Request Updated',
            'resolved' => 'Service Request Resolved',
            'closed' => 'Service Request Closed',
            default => 'Service Request Update',
        };

        $message = match ($this->action) {
            'created' => "New request: {$this->serviceRequest->title}",
            'assigned' => "Request #{$this->serviceRequest->id} has been assigned to you",
            'updated' => "Request #{$this->serviceRequest->id} has been updated",
            'resolved' => "Request #{$this->serviceRequest->id} has been resolved",
            'closed' => "Request #{$this->serviceRequest->id} has been closed",
            default => "Request #{$this->serviceRequest->id} update",
        };

        return [
            'type' => $this->getType(),
            'category' => $this->category,
            'action' => $this->action,
            'title' => $title,
            'message' => $message,
            'request_id' => $this->serviceRequest->id,
            'request_title' => $this->serviceRequest->title,
            'priority' => $this->serviceRequest->priority,
            'status' => $this->serviceRequest->status,
            'action_url' => "/service-requests/{$this->serviceRequest->id}",
            'icon' => 'fa-headset',
            'color' => match ($this->serviceRequest->priority) {
                'urgent' => 'danger',
                'high' => 'warning',
                'normal' => 'primary',
                default => 'secondary',
            },
        ];
    }
}
