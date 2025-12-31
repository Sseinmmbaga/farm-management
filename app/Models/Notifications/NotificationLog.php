<?php

namespace App\Models\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'channel',
        'status',
        'recipient_email',
        'recipient_phone',
        'subject',
        'message',
        'metadata',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    // ==================== CONSTANTS ====================

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const TYPE_CERTIFICATION_EXPIRY = 'certification_expiry';
    const TYPE_DOCUMENT_EXPIRY = 'document_expiry';
    const TYPE_TRAINING_REMINDER = 'training_reminder';
    const TYPE_TRAINING_CERTIFICATE_EXPIRY = 'training_certificate_expiry';
    const TYPE_SERVICE_REQUEST = 'service_request';
    const TYPE_SYSTEM = 'system';

    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_DATABASE = 'database';

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ==================== HELPER METHODS ====================

    public static function log(
        string $type,
        string $channel,
        string $status,
        ?int $userId = null,
        ?string $recipientEmail = null,
        ?string $recipientPhone = null,
        ?string $subject = null,
        ?string $message = null,
        ?array $metadata = null,
        ?string $errorMessage = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'status' => $status,
            'recipient_email' => $recipientEmail,
            'recipient_phone' => $recipientPhone,
            'subject' => $subject,
            'message' => $message,
            'metadata' => $metadata,
            'error_message' => $errorMessage,
            'sent_at' => $status === self::STATUS_SENT ? now() : null,
        ]);
    }

    public function markAsSent(): self
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed(string $errorMessage): self
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);

        return $this;
    }
}
