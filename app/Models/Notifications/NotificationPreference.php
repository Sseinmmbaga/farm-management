<?php

namespace App\Models\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_enabled',
        'sms_enabled',
        'in_app_enabled',
        'certification_alerts',
        'document_alerts',
        'training_reminders',
        'service_request_updates',
        'system_announcements',
        'certification_reminder_days',
        'document_reminder_days',
        'training_reminder_days',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'certification_alerts' => 'boolean',
        'document_alerts' => 'boolean',
        'training_reminders' => 'boolean',
        'service_request_updates' => 'boolean',
        'system_announcements' => 'boolean',
        'certification_reminder_days' => 'integer',
        'document_reminder_days' => 'integer',
        'training_reminder_days' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== HELPER METHODS ====================

    public static function getOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enabled' => true,
                'sms_enabled' => false,
                'in_app_enabled' => true,
                'certification_alerts' => true,
                'document_alerts' => true,
                'training_reminders' => true,
                'service_request_updates' => true,
                'system_announcements' => true,
                'certification_reminder_days' => 30,
                'document_reminder_days' => 30,
                'training_reminder_days' => 7,
            ]
        );
    }

    public function shouldNotify(string $category, string $channel = 'database'): bool
    {
        // Check channel
        $channelEnabled = match ($channel) {
            'mail', 'email' => $this->email_enabled,
            'sms' => $this->sms_enabled,
            'database' => $this->in_app_enabled,
            default => true,
        };

        if (!$channelEnabled) {
            return false;
        }

        // Check category
        return match ($category) {
            'certification' => $this->certification_alerts,
            'document' => $this->document_alerts,
            'training' => $this->training_reminders,
            'service_request' => $this->service_request_updates,
            'system' => $this->system_announcements,
            default => true,
        };
    }
}
