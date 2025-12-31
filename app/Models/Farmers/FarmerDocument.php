<?php

namespace App\Models\Farmers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerDocument extends Model
{
    /**
     * Document type constants.
     */
    const TYPE_NATIONAL_ID = 'national_id';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_CONTRACT = 'contract';
    const TYPE_PHOTO = 'photo';
    const TYPE_LAND_TITLE = 'land_title';
    const TYPE_ORGANIC_STANDARD_PLAN = 'organic_standard_plan';
    const TYPE_FARM_BOOK = 'farm_book';
    const TYPE_ORGANIC_GROUP_RECORD = 'organic_group_record';
    const TYPE_SOIL_TEST_REPORT = 'soil_test_report';
    const TYPE_ORGANIC_CERTIFICATE = 'organic_certificate';
    const TYPE_AUDIT_REPORT = 'audit_report';
    const TYPE_GROUP_MEMBERSHIP = 'group_membership';
    const TYPE_TRAINING_CERTIFICATE = 'training_certificate';
    const TYPE_OTHER = 'other';

    /**
     * Get all types as key‑label pairs.
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_NATIONAL_ID => 'National ID (NIDA)',
            self::TYPE_CERTIFICATE => 'Certificate',
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_PHOTO => 'Photo',
            self::TYPE_LAND_TITLE => 'Land Title',
            self::TYPE_ORGANIC_STANDARD_PLAN => 'Organic Standard Plan (OSP)',
            self::TYPE_FARM_BOOK => 'Farm Book',
            self::TYPE_ORGANIC_GROUP_RECORD => 'Organic Group Record',
            self::TYPE_SOIL_TEST_REPORT => 'Soil Test Report',
            self::TYPE_ORGANIC_CERTIFICATE => 'Organic Certificate',
            self::TYPE_AUDIT_REPORT => 'Audit Report',
            self::TYPE_GROUP_MEMBERSHIP => 'Group Membership Document',
            self::TYPE_TRAINING_CERTIFICATE => 'Training Certificate',
            self::TYPE_OTHER => 'Other Document',
        ];
    }

    protected $fillable = [
        'farmer_id',
        'type',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'issue_date',
        'expiry_date',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'file_size' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_NATIONAL_ID => 'National ID (NIDA)',
            self::TYPE_CERTIFICATE => 'Certificate',
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_PHOTO => 'Photo',
            self::TYPE_LAND_TITLE => 'Land Title',
            self::TYPE_ORGANIC_STANDARD_PLAN => 'Organic Standard Plan (OSP)',
            self::TYPE_FARM_BOOK => 'Farm Book',
            self::TYPE_ORGANIC_GROUP_RECORD => 'Organic Group Record',
            self::TYPE_SOIL_TEST_REPORT => 'Soil Test Report',
            self::TYPE_ORGANIC_CERTIFICATE => 'Organic Certificate',
            self::TYPE_AUDIT_REPORT => 'Audit Report',
            self::TYPE_GROUP_MEMBERSHIP => 'Group Membership Document',
            self::TYPE_TRAINING_CERTIFICATE => 'Training Certificate',
            self::TYPE_OTHER => 'Other Document',
            default => $this->type,
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isBetween(now(), now()->addDays(30));
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')->where('expiry_date', '<', now());
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }
}
