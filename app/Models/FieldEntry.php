<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FieldEntry extends Model
{
    use SoftDeletes;

    protected $table = 'field_entries';

    protected $fillable = [
        'entry_code',
        'title',
        'field_id',
        'farm_id',
        'farmer_id',
        'form_type',
        'form_subtype',
        'entry_date',
        'entry_time',
        'season_year',
        'season_period',
        'status',
        'latitude',
        'longitude',
        'location_notes',
        'weather_condition',
        'temperature',
        'humidity',
        'rainfall',
        'notes',
        'summary',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'rainfall' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'draft',
        'form_type' => 'production',
    ];

    // Relationships
    public function field(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Farms\Field::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Farms\Farm::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Farmers\Farmer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function data(): HasMany
    {
        return $this->hasMany(FieldEntryData::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByFormType($query, $formType)
    {
        return $query->where('form_type', $formType);
    }

    public function scopeBySeason($query, $year, $period = null)
    {
        $query = $query->where('season_year', $year);
        if ($period) {
            $query->where('season_period', $period);
        }
        return $query;
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canEdit(): bool
    {
        return $this->isDraft() || Auth::id() === $this->created_by;
    }

    public function getFormattedEntryDate(): string
    {
        return $this->entry_date->format('d/m/Y');
    }

    public function getDataBySection(string $sectionName): ?array
    {
        $data = $this->data()->where('section_name', $sectionName)->first();
        return $data ? $data->data : null;
    }

    public function getAllData(): array
    {
        $allData = [];
        foreach ($this->data()->orderBy('section_order')->get() as $section) {
            $allData[$section->section_name] = $section->data;
        }
        return $allData;
    }

    public function addDataSection(string $sectionName, array $data, int $order = 0, ?string $notes = null): void
    {
        $this->data()->create([
            'section_name' => $sectionName,
            'section_order' => $order,
            'data' => $data,
            'notes' => $notes,
        ]);
    }

    public function updateDataSection(string $sectionName, array $data, ?string $notes = null): void
    {
        $section = $this->data()->where('section_name', $sectionName)->first();
        if ($section) {
            $section->update([
                'data' => $data,
                'notes' => $notes,
            ]);
        } else {
            $this->addDataSection($sectionName, $data, 0, $notes);
        }
    }

    // Generate entry code
    public static function generateEntryCode(): string
    {
        $year = date('Y');
        $prefix = 'ENTRY';
        
        // Get last entry for this year
        $lastEntry = self::where('entry_code', 'like', "{$prefix}-{$year}-%")->orderBy('entry_code', 'desc')->first();
        
        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "{$prefix}-{$year}-{$newNumber}";
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->entry_code)) {
                $model->entry_code = self::generateEntryCode();
            }
            if (empty($model->season_year)) {
                $model->season_year = date('Y');
            }
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
