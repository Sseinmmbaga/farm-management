<?php

namespace App\Models\Tasks;

use App\Enums\LaborType;
use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farmers\Farmer;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Labor extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail;

    protected $table = 'labor_records';

    protected $fillable = [
        'code',
        'task_id',
        'farm_id',
        'field_id',
        'worker_type',
        'worker_id',
        'worker_name',
        'labor_type',
        'work_date',
        'start_time',
        'end_time',
        'hours_worked',
        'overtime_hours',
        'break_minutes',
        'hourly_rate',
        'overtime_rate',
        'total_cost',
        'payment_status',
        'paid_at',
        'paid_by',
        'payment_reference',
        'work_description',
        'notes',
        'weather_conditions',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'labor_type' => LaborType::class,
        'work_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'break_minutes' => 'integer',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function worker(): MorphTo
    {
        return $this->morphTo();
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ==================== ACCESSORS ====================

    public function getLaborTypeLabelAttribute(): string
    {
        return $this->labor_type->label();
    }

    public function getLaborTypeColorAttribute(): string
    {
        return $this->labor_type->color();
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->payment_status),
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'warning',
            'approved' => 'info',
            'paid' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getWorkerDisplayNameAttribute(): string
    {
        if ($this->worker_name) {
            return $this->worker_name;
        }

        if ($this->worker) {
            return $this->worker->name ?? $this->worker->full_name ?? 'Unknown';
        }

        return 'Unknown Worker';
    }

    public function getWorkDurationAttribute(): string
    {
        $hours = floor($this->hours_worked);
        $minutes = ($this->hours_worked - $hours) * 60;

        if ($minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$hours}h";
    }

    public function getEffectiveHoursAttribute(): float
    {
        return $this->hours_worked - ($this->break_minutes / 60);
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->hours_worked + ($this->overtime_hours ?? 0);
    }

    // ==================== SCOPES ====================

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeForField($query, $fieldId)
    {
        return $query->where('field_id', $fieldId);
    }

    public function scopeForWorker($query, $workerType, $workerId)
    {
        return $query->where('worker_type', $workerType)
                     ->where('worker_id', $workerId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('work_date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('work_date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('payment_status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('payment_status', ['pending', 'approved']);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeOfType($query, LaborType $type)
    {
        return $query->where('labor_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('worker_name', 'like', "%{$search}%")
              ->orWhere('work_description', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->payment_status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return "LBR{$year}{$month}" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function calculateCost(): float
    {
        $regularCost = $this->hours_worked * $this->hourly_rate;
        $overtimeCost = ($this->overtime_hours ?? 0) * ($this->overtime_rate ?? $this->hourly_rate * 1.5);

        return $regularCost + $overtimeCost;
    }

    public function approve(): bool
    {
        if ($this->payment_status !== 'pending') {
            return false;
        }

        $this->payment_status = 'approved';
        return $this->save();
    }

    public function markAsPaid(?string $reference = null): bool
    {
        if (!in_array($this->payment_status, ['pending', 'approved'])) {
            return false;
        }

        $this->payment_status = 'paid';
        $this->paid_at = now();
        $this->paid_by = auth()->id();
        $this->payment_reference = $reference;
        return $this->save();
    }

    public function verify(): bool
    {
        if ($this->is_verified) {
            return false;
        }

        $this->is_verified = true;
        $this->verified_by = auth()->id();
        $this->verified_at = now();
        return $this->save();
    }

    public static function getTotalHoursForTask($taskId): float
    {
        return self::forTask($taskId)->sum('hours_worked');
    }

    public static function getTotalCostForTask($taskId): float
    {
        return self::forTask($taskId)->sum('total_cost');
    }

    public static function getHoursSummaryForField($fieldId, $startDate = null, $endDate = null): array
    {
        $query = self::forField($fieldId);

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        return [
            'total_hours' => $query->sum('hours_worked'),
            'overtime_hours' => $query->sum('overtime_hours'),
            'total_cost' => $query->sum('total_cost'),
            'records_count' => $query->count(),
        ];
    }
}
