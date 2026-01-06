<?php

namespace App\Models;

use App\Models\User;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_type',
        'form_name',
        'form_name_sw',
        'farmer_id',
        'farm_id',
        'submitted_by',
        'reviewed_by',
        'form_data',
        'status',
        'reviewer_notes',
        'season',
        'form_date',
    ];

    protected $casts = [
        'form_data' => 'array',
        'form_date' => 'date',
    ];

    // Form types constants
    const FORM_1 = 'form1'; // Utambulisho wa Mkulima (Farmer Identification)
    const FORM_2 = 'form2'; // Kumbukumbu Shamba Mpya (New Farm Records)
    const FORM_3 = 'form3'; // Kumbukumbu Shamba Zamani (Old Farm Records)
    const FORM_4 = 'form4'; // Dodoso Shamba Lililoboreshwa (Improved Farm Questionnaire)
    const FORM_5 = 'form5'; // Taarifa Ufiatiaji (Compliance Information)
    const FORM_6 = 'form6'; // Monthly Performance Report
    const FORM_8 = 'form8'; // Social & Environmental Standards
    const FORM_9 = 'form9'; // Internal Inspection Report Checklist
    const FORM_10 = 'form10'; // Makisio Yanayohitajika (Required Estimates)
    const FORM_11 = 'form11'; // Historia ya Shamba (Farm History)
    const FORM_12 = 'form12'; // Training Form
    const FORM_13 = 'form13'; // Seed Distribution Form

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get all form types with their names
     */
    public static function getFormTypes(): array
    {
        return [
            self::FORM_1 => [
                'name' => 'Farmer Identification',
                'name_sw' => 'Utambulisho wa Mkulima',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_2 => [
                'name' => 'New Farm Records',
                'name_sw' => 'Kumbukumbu Shamba Mpya',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_3 => [
                'name' => 'Old Farm Records',
                'name_sw' => 'Kumbukumbu Shamba Zamani',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_4 => [
                'name' => 'Improved Farm Questionnaire',
                'name_sw' => 'Dodoso Shamba Lililoboreshwa',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_5 => [
                'name' => 'Compliance Information',
                'name_sw' => 'Taarifa Ufiatiaji',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_6 => [
                'name' => 'Monthly Performance Report',
                'name_sw' => 'Ripoti ya Utendaji wa Kila Mwezi',
                'roles' => ['supervisor', 'production_manager', 'admin'],
            ],
            self::FORM_8 => [
                'name' => 'Social & Environmental Standards',
                'name_sw' => 'Viwango vya Kijamii na Mazingira',
                'roles' => ['ics_inspector', 'supervisor', 'admin'],
            ],
            self::FORM_9 => [
                'name' => 'Internal Inspection Report Checklist',
                'name_sw' => 'Orodha ya Ukaguzi wa Ndani',
                'roles' => ['ics_inspector', 'supervisor', 'admin'],
            ],
            self::FORM_10 => [
                'name' => 'Required Estimates',
                'name_sw' => 'Makisio Yanayohitajika',
                'roles' => ['production_manager', 'supervisor', 'admin'],
            ],
            self::FORM_11 => [
                'name' => 'Farm History',
                'name_sw' => 'Historia ya Shamba',
                'roles' => ['extension_officer', 'supervisor', 'admin'],
            ],
            self::FORM_12 => [
                'name' => 'Training Form',
                'name_sw' => 'Fomu ya Mafunzo',
                'roles' => ['training_coordinator', 'supervisor', 'admin'],
            ],
            self::FORM_13 => [
                'name' => 'Seed Distribution Form',
                'name_sw' => 'Fomu ya Usambazaji wa Mbegu',
                'roles' => ['stock_manager', 'supervisor', 'admin'],
            ],
        ];
    }

    /**
     * Get forms accessible by a specific role
     */
    public static function getFormsByRole(string $role): array
    {
        $forms = [];
        foreach (self::getFormTypes() as $type => $info) {
            if (in_array($role, $info['roles'])) {
                $forms[$type] = $info;
            }
        }
        return $forms;
    }

    /**
     * Get the farmer that owns this form
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Get the farm associated with this form
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the user who submitted this form
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who reviewed this form
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to filter by form type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('form_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by season
     */
    public function scopeForSeason($query, string $season)
    {
        return $query->where('season', $season);
    }

    /**
     * Get form type label
     */
    public function getFormTypeLabelAttribute(): string
    {
        $types = self::getFormTypes();
        return $types[$this->form_type]['name'] ?? $this->form_type;
    }

    /**
     * Get form type Swahili label
     */
    public function getFormTypeSwahiliAttribute(): string
    {
        $types = self::getFormTypes();
        return $types[$this->form_type]['name_sw'] ?? $this->form_type;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-secondary',
            self::STATUS_SUBMITTED => 'bg-primary',
            self::STATUS_REVIEWED => 'bg-info',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if form can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Check if form can be submitted
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if form can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }
}
