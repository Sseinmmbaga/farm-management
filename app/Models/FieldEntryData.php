<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldEntryData extends Model
{
    protected $table = 'field_entry_data';

    protected $fillable = [
        'field_entry_id',
        'section_name',
        'section_order',
        'data',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'section_order' => 'integer',
    ];

    // Relationships
    public function fieldEntry(): BelongsTo
    {
        return $this->belongsTo(FieldEntry::class);
    }

    // Helper Methods
    public function getDataValue(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function setDataValue(string $key, $value): void
    {
        $data = $this->data;
        $data[$key] = $value;
        $this->data = $data;
    }

    public function hasDataValue(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function removeDataValue(string $key): void
    {
        $data = $this->data;
        unset($data[$key]);
        $this->data = $data;
    }

    public function getFormattedData(): array
    {
        $formatted = [];
        foreach ($this->data as $key => $value) {
            // Format based on value type
            if (is_array($value)) {
                $formatted[$key] = implode(', ', $value);
            } elseif (is_bool($value)) {
                $formatted[$key] = $value ? 'Yes' : 'No';
            } elseif ($value === null || $value === '') {
                $formatted[$key] = 'Not specified';
            } else {
                $formatted[$key] = $value;
            }
        }
        return $formatted;
    }
}
