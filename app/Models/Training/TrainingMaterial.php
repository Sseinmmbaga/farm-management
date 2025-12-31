<?php

namespace App\Models\Training;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingMaterial extends Model
{
    protected $table = 'training_materials';

    protected $fillable = [
        'training_program_id',
        'training_session_id',
        'title',
        'title_sw',
        'description',
        'type',
        'file_path',
        'file_type',
        'file_size',
        'external_url',
        'language',
        'is_downloadable',
        'download_count',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopePresentations($query)
    {
        return $query->where('type', 'presentation');
    }

    public function scopeForProgram($query, $programId)
    {
        return $query->where('training_program_id', $programId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('training_session_id', $sessionId);
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'document' => 'Document',
            'video' => 'Video',
            'presentation' => 'Presentation',
            'handout' => 'Handout',
            default => ucfirst($this->type),
        };
    }

    public function getLanguageLabelAttribute(): string
    {
        return match($this->language) {
            'sw' => 'Swahili',
            'en' => 'English',
            default => $this->language,
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if ($this->file_size === null) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    public function getIsExternalAttribute(): bool
    {
        return !empty($this->external_url);
    }
}