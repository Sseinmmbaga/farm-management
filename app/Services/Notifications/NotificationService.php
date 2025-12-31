<?php

namespace App\Services\Notifications;

use App\Enums\UserRole;
use App\Models\Farmers\Farmer;
use App\Models\Farmers\FarmerDocument;
use App\Models\ICS\FarmerCertification;
use App\Models\Notifications\NotificationLog;
use App\Models\ServiceRequest;
use App\Models\Training\TrainingCertificate;
use App\Models\Training\TrainingSession;
use App\Models\User;
use App\Notifications\CertificationExpiryNotification;
use App\Notifications\DocumentExpiryNotification;
use App\Notifications\ServiceRequestNotification;
use App\Notifications\SystemAnnouncementNotification;
use App\Notifications\TrainingCertificateExpiryNotification;
use App\Notifications\TrainingReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send certification expiry notifications.
     */
    public function sendCertificationExpiryAlerts(int $days = 30): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $expiringCertifications = FarmerCertification::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->with(['farmer.extensionOfficer'])
            ->get();

        foreach ($expiringCertifications as $certification) {
            $farmer = $certification->farmer;
            if (!$farmer) {
                $results['skipped']++;
                continue;
            }

            $recipients = $this->getCertificationAlertRecipients($farmer);

            foreach ($recipients as $recipient) {
                try {
                    if (!$recipient->shouldReceiveNotification('certification', 'database')) {
                        $results['skipped']++;
                        continue;
                    }

                    $recipient->notify(new CertificationExpiryNotification($certification, $farmer));
                    $results['sent']++;

                    NotificationLog::log(
                        type: NotificationLog::TYPE_CERTIFICATION_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_SENT,
                        userId: $recipient->id,
                        subject: "Certification Expiry Alert",
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'certification_id' => $certification->id,
                            'expiry_date' => $certification->expiry_date->toDateString(),
                        ]
                    );
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Failed to send certification expiry notification: " . $e->getMessage());

                    NotificationLog::log(
                        type: NotificationLog::TYPE_CERTIFICATION_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_FAILED,
                        userId: $recipient->id,
                        errorMessage: $e->getMessage(),
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'certification_id' => $certification->id,
                        ]
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Send document expiry notifications.
     */
    public function sendDocumentExpiryAlerts(int $days = 30): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $expiringDocuments = FarmerDocument::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->with(['farmer.extensionOfficer'])
            ->get();

        foreach ($expiringDocuments as $document) {
            $farmer = $document->farmer;
            if (!$farmer) {
                $results['skipped']++;
                continue;
            }

            $recipients = $this->getDocumentAlertRecipients($farmer);

            foreach ($recipients as $recipient) {
                try {
                    if (!$recipient->shouldReceiveNotification('document', 'database')) {
                        $results['skipped']++;
                        continue;
                    }

                    $recipient->notify(new DocumentExpiryNotification($document, $farmer));
                    $results['sent']++;

                    NotificationLog::log(
                        type: NotificationLog::TYPE_DOCUMENT_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_SENT,
                        userId: $recipient->id,
                        subject: "Document Expiry Alert",
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'document_id' => $document->id,
                            'document_type' => $document->type,
                            'expiry_date' => $document->expiry_date->toDateString(),
                        ]
                    );
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Failed to send document expiry notification: " . $e->getMessage());

                    NotificationLog::log(
                        type: NotificationLog::TYPE_DOCUMENT_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_FAILED,
                        userId: $recipient->id,
                        errorMessage: $e->getMessage(),
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'document_id' => $document->id,
                        ]
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Send training reminder notifications.
     */
    public function sendTrainingReminders(int $days = 7): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $upcomingSessions = TrainingSession::where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays($days))
            ->with(['program', 'trainer', 'attendances.farmer.extensionOfficer'])
            ->get();

        foreach ($upcomingSessions as $session) {
            $daysUntil = now()->diffInDays($session->scheduled_date, false);
            $reminderType = match (true) {
                $daysUntil == 0 => 'today',
                $daysUntil == 1 => 'tomorrow',
                default => 'upcoming',
            };

            $recipients = $this->getTrainingReminderRecipients($session);

            foreach ($recipients as $recipient) {
                try {
                    if (!$recipient->shouldReceiveNotification('training', 'database')) {
                        $results['skipped']++;
                        continue;
                    }

                    $recipient->notify(new TrainingReminderNotification($session, $reminderType));
                    $results['sent']++;

                    NotificationLog::log(
                        type: NotificationLog::TYPE_TRAINING_REMINDER,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_SENT,
                        userId: $recipient->id,
                        subject: "Training Reminder",
                        metadata: [
                            'session_id' => $session->id,
                            'scheduled_date' => $session->scheduled_date->toDateTimeString(),
                            'reminder_type' => $reminderType,
                        ]
                    );
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Failed to send training reminder notification: " . $e->getMessage());

                    NotificationLog::log(
                        type: NotificationLog::TYPE_TRAINING_REMINDER,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_FAILED,
                        userId: $recipient->id,
                        errorMessage: $e->getMessage(),
                        metadata: ['session_id' => $session->id]
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Send training certificate expiry notifications.
     */
    public function sendTrainingCertificateExpiryAlerts(int $days = 30): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $expiringCertificates = TrainingCertificate::whereNotNull('expiry_date')
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->with(['farmer.extensionOfficer', 'program'])
            ->get();

        foreach ($expiringCertificates as $certificate) {
            $farmer = $certificate->farmer;
            if (!$farmer) {
                $results['skipped']++;
                continue;
            }

            $recipients = $this->getTrainingCertificateExpiryRecipients($farmer);

            foreach ($recipients as $recipient) {
                try {
                    if (!$recipient->shouldReceiveNotification('training', 'database')) {
                        $results['skipped']++;
                        continue;
                    }

                    $recipient->notify(new TrainingCertificateExpiryNotification($certificate, $farmer));
                    $results['sent']++;

                    NotificationLog::log(
                        type: NotificationLog::TYPE_TRAINING_CERTIFICATE_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_SENT,
                        userId: $recipient->id,
                        subject: "Training Certificate Expiry Alert",
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'certificate_id' => $certificate->id,
                            'expiry_date' => $certificate->expiry_date->toDateString(),
                        ]
                    );
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Failed to send training certificate expiry notification: " . $e->getMessage());

                    NotificationLog::log(
                        type: NotificationLog::TYPE_TRAINING_CERTIFICATE_EXPIRY,
                        channel: NotificationLog::CHANNEL_DATABASE,
                        status: NotificationLog::STATUS_FAILED,
                        userId: $recipient->id,
                        errorMessage: $e->getMessage(),
                        metadata: [
                            'farmer_id' => $farmer->id,
                            'certificate_id' => $certificate->id,
                        ]
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Send service request notification.
     */
    public function sendServiceRequestNotification(ServiceRequest $serviceRequest, string $action = 'created'): void
    {
        $recipients = $this->getServiceRequestRecipients($serviceRequest, $action);

        foreach ($recipients as $recipient) {
            try {
                if (!$recipient->shouldReceiveNotification('service_request', 'database')) {
                    continue;
                }

                $recipient->notify(new ServiceRequestNotification($serviceRequest, $action));

                NotificationLog::log(
                    type: NotificationLog::TYPE_SERVICE_REQUEST,
                    channel: NotificationLog::CHANNEL_DATABASE,
                    status: NotificationLog::STATUS_SENT,
                    userId: $recipient->id,
                    subject: "Service Request {$action}",
                    metadata: [
                        'request_id' => $serviceRequest->id,
                        'action' => $action,
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Failed to send service request notification: " . $e->getMessage());

                NotificationLog::log(
                    type: NotificationLog::TYPE_SERVICE_REQUEST,
                    channel: NotificationLog::CHANNEL_DATABASE,
                    status: NotificationLog::STATUS_FAILED,
                    userId: $recipient->id,
                    errorMessage: $e->getMessage(),
                    metadata: [
                        'request_id' => $serviceRequest->id,
                        'action' => $action,
                    ]
                );
            }
        }
    }

    /**
     * Send system announcement to all users or specific roles.
     */
    public function sendSystemAnnouncement(
        string $title,
        string $message,
        string $priority = 'normal',
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?array $roles = null
    ): array {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $query = User::active();

        if ($roles) {
            $query->whereIn('role', $roles);
        }

        $recipients = $query->get();

        foreach ($recipients as $recipient) {
            try {
                if (!$recipient->shouldReceiveNotification('system', 'database')) {
                    $results['skipped']++;
                    continue;
                }

                $recipient->notify(new SystemAnnouncementNotification(
                    $title,
                    $message,
                    $priority,
                    $actionUrl,
                    $actionText
                ));
                $results['sent']++;

                NotificationLog::log(
                    type: NotificationLog::TYPE_SYSTEM,
                    channel: NotificationLog::CHANNEL_DATABASE,
                    status: NotificationLog::STATUS_SENT,
                    userId: $recipient->id,
                    subject: $title,
                    message: $message
                );
            } catch (\Exception $e) {
                $results['failed']++;
                Log::error("Failed to send system announcement: " . $e->getMessage());

                NotificationLog::log(
                    type: NotificationLog::TYPE_SYSTEM,
                    channel: NotificationLog::CHANNEL_DATABASE,
                    status: NotificationLog::STATUS_FAILED,
                    userId: $recipient->id,
                    errorMessage: $e->getMessage()
                );
            }
        }

        return $results;
    }

    // ==================== RECIPIENT HELPERS ====================

    protected function getCertificationAlertRecipients(Farmer $farmer): Collection
    {
        $recipients = collect();

        // Extension officer
        if ($farmer->extensionOfficer) {
            $recipients->push($farmer->extensionOfficer);
        }

        // ICS inspectors
        $inspectors = User::active()->byRole(UserRole::ICS_INSPECTOR)->get();
        $recipients = $recipients->merge($inspectors);

        // Admins
        $admins = User::active()->byRole(UserRole::ADMIN)->get();
        $recipients = $recipients->merge($admins);

        return $recipients->unique('id');
    }

    protected function getDocumentAlertRecipients(Farmer $farmer): Collection
    {
        $recipients = collect();

        // Extension officer
        if ($farmer->extensionOfficer) {
            $recipients->push($farmer->extensionOfficer);
        }

        // Admins
        $admins = User::active()->byRole(UserRole::ADMIN)->get();
        $recipients = $recipients->merge($admins);

        return $recipients->unique('id');
    }

    protected function getTrainingReminderRecipients(TrainingSession $session): Collection
    {
        $recipients = collect();

        // Trainer
        if ($session->trainer) {
            $recipients->push($session->trainer);
        }

        // Training coordinators
        $coordinators = User::active()->byRole(UserRole::TRAINING_COORDINATOR)->get();
        $recipients = $recipients->merge($coordinators);

        // Extension officers of registered farmers
        $extensionOfficers = $session->attendances
            ->map(fn($attendance) => $attendance->farmer?->extensionOfficer)
            ->filter()
            ->unique('id');
        $recipients = $recipients->merge($extensionOfficers);

        return $recipients->unique('id');
    }

    protected function getTrainingCertificateExpiryRecipients(Farmer $farmer): Collection
    {
        $recipients = collect();

        // Extension officer
        if ($farmer->extensionOfficer) {
            $recipients->push($farmer->extensionOfficer);
        }

        // Training coordinators
        $coordinators = User::active()->byRole(UserRole::TRAINING_COORDINATOR)->get();
        $recipients = $recipients->merge($coordinators);

        return $recipients->unique('id');
    }

    protected function getServiceRequestRecipients(ServiceRequest $serviceRequest, string $action): Collection
    {
        $recipients = collect();

        switch ($action) {
            case 'created':
                // Notify admins and supervisors
                $admins = User::active()->byRole(UserRole::ADMIN)->get();
                $supervisors = User::active()->byRole(UserRole::SUPERVISOR)->get();
                $recipients = $recipients->merge($admins)->merge($supervisors);
                break;

            case 'assigned':
                // Notify assigned user
                if ($serviceRequest->assignedTo) {
                    $recipients->push($serviceRequest->assignedTo);
                }
                break;

            case 'updated':
            case 'resolved':
            case 'closed':
                // Notify requester and assigned user
                if ($serviceRequest->requestedBy) {
                    $recipients->push($serviceRequest->requestedBy);
                }
                if ($serviceRequest->assignedTo) {
                    $recipients->push($serviceRequest->assignedTo);
                }
                break;
        }

        return $recipients->unique('id');
    }
}
