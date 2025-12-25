<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class FieldEntryWorkflowHistory extends Model
{
    protected $table = 'field_entry_workflow_history';

    protected $fillable = [
        'field_entry_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function fieldEntry(): BelongsTo
    {
        return $this->belongsTo(FieldEntry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function getActionDescription(): string
    {
        $descriptions = [
            'created' => 'Created the entry',
            'updated' => 'Updated the entry',
            'submitted' => 'Submitted for review',
            'reviewed' => 'Reviewed the entry',
            'approved' => 'Approved the entry',
            'rejected' => 'Rejected the entry',
            'returned' => 'Returned for corrections',
            'commented' => 'Added a comment',
            'attached_file' => 'Attached a file',
            'status_changed' => 'Changed status',
        ];

        return $descriptions[$this->action] ?? ucfirst($this->action);
    }

    public function getFormattedChanges(): array
    {
        if (empty($this->changes)) {
            return [];
        }

        $formatted = [];
        foreach ($this->changes as $field => $change) {
            $formatted[] = [
                'field' => $this->getFieldLabel($field),
                'from' => $this->formatValue($change['from'] ?? null),
                'to' => $this->formatValue($change['to'] ?? null),
            ];
        }

        return $formatted;
    }

    private function getFieldLabel(string $field): string
    {
        $labels = [
            'status' => 'Status',
            'title' => 'Title',
            'entry_date' => 'Entry Date',
            'notes' => 'Notes',
            'summary' => 'Summary',
            'weather_condition' => 'Weather Condition',
            'temperature' => 'Temperature',
            'humidity' => 'Humidity',
            'rainfall' => 'Rainfall',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    private function formatValue($value): string
    {
        if (is_null($value)) {
            return 'Empty';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }

    // Static helper to log workflow actions
    public static function logAction(
        FieldEntry $entry,
        string $action,
        array $changes = [],
        ?string $notes = null
    ): self {
        return self::create([
            'field_entry_id' => $entry->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'from_status' => $entry->getOriginal('status'),
            'to_status' => $entry->status,
            'notes' => $notes,
            'changes' => $changes,
        ]);
    }
}
