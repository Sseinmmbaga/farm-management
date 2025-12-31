<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PerformanceReport extends Model
{
    use SoftDeletes;

    /**
     * Status constants.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Period type constants.
     */
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_QUARTERLY = 'quarterly';
    const PERIOD_YEARLY = 'yearly';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_number',
        'user_id',
        'department_id',
        'period_type',
        'period_month',
        'period_year',
        'report_date',
        'status',
        'metrics',
        'total_score',
        'summary',
        'strengths',
        'improvements',
        'recommendations',
        'reviewer_id',
        'reviewed_at',
        'review_notes',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'report_date' => 'date',
        'metrics' => 'array',
        'total_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->report_number)) {
                $model->report_number = static::generateReportNumber();
            }
        });
    }

    /**
     * Generate a unique report number.
     *
     * @return string
     */
    public static function generateReportNumber(): string
    {
        $prefix = 'PR';
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $latest = static::where('report_number', 'like', "{$prefix}{$year}{$month}{$day}%")
            ->orderBy('report_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->report_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$year}{$month}{$day}{$nextNumber}";
    }

    /**
     * Get the user (employee) associated with the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department associated with the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the reviewer user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the approver user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include reports of a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|array  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include reports for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include reports for a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|Department  $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDepartment($query, $department)
    {
        $departmentId = $department instanceof Department ? $department->id : $department;
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include reports for a specific period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @param  int|null  $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod($query, $year, $month = null)
    {
        $query->where('period_year', $year);
        if ($month) {
            $query->where('period_month', $month);
        }
        return $query;
    }

    /**
     * Scope a query to only include reports created between dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $start
     * @param  string  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Check if the report is in draft status.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the report is submitted.
     *
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Check if the report is reviewed.
     *
     * @return bool
     */
    public function isReviewed(): bool
    {
        return $this->status === self::STATUS_REVIEWED;
    }

    /**
     * Check if the report is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the report is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the report can be edited.
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Check if the report can be submitted.
     *
     * @return bool
     */
    public function canBeSubmitted(): bool
    {
        return $this->isDraft() || $this->isRejected();
    }

    /**
     * Check if the report can be reviewed.
     *
     * @return bool
     */
    public function canBeReviewed(): bool
    {
        return $this->isSubmitted();
    }

    /**
     * Check if the report can be approved.
     *
     * @return bool
     */
    public function canBeApproved(): bool
    {
        return $this->isReviewed();
    }

    /**
     * Check if the report can be rejected.
     *
     * @return bool
     */
    public function canBeRejected(): bool
    {
        return $this->isSubmitted() || $this->isReviewed();
    }

    /**
     * Get the display label for the status.
     *
     * @return string
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_REVIEWED => 'Reviewed',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the CSS color class for the status.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_REVIEWED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'light',
        };
    }

    /**
     * Get the period display label.
     *
     * @return string
     */
    public function getPeriodDisplayAttribute(): string
    {
        if ($this->period_type === self::PERIOD_YEARLY) {
            return "Year {$this->period_year}";
        }
        if ($this->period_type === self::PERIOD_QUARTERLY) {
            $quarter = ceil($this->period_month / 3);
            return "Q{$quarter} {$this->period_year}";
        }
        // monthly
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthName = $monthNames[$this->period_month - 1] ?? 'Unknown';
        return "{$monthName} {$this->period_year}";
    }

    /**
     * Calculate the total score from metrics.
     *
     * @return float|null
     */
    public function calculateTotalScore(): ?float
    {
        if (empty($this->metrics)) {
            return null;
        }
        $total = 0;
        $count = 0;
        foreach ($this->metrics as $metric) {
            if (isset($metric['score']) && is_numeric($metric['score'])) {
                $total += $metric['score'];
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 2) : null;
    }

    /**
     * Get the metrics as an array with labels.
     *
     * @return array
     */
    public function getMetricsWithLabels(): array
    {
        $defaultMetrics = [
            'productivity' => 'Productivity',
            'quality' => 'Quality',
            'timeliness' => 'Timeliness',
            'collaboration' => 'Collaboration',
            'innovation' => 'Innovation',
        ];
        $metrics = $this->metrics ?? [];
        $result = [];
        foreach ($defaultMetrics as $key => $label) {
            $result[] = [
                'key' => $key,
                'label' => $label,
                'score' => $metrics[$key]['score'] ?? null,
                'comments' => $metrics[$key]['comments'] ?? null,
            ];
        }
        return $result;
    }
}
