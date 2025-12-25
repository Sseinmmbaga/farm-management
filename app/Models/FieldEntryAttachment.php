<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FieldEntryAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'field_entry_attachments';

    protected $fillable = [
        'field_entry_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'attachment_type',
        'caption',
        'description',
        'thumbnail_path',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function fieldEntry(): BelongsTo
    {
        return $this->belongsTo(FieldEntry::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper Methods
    public function getFileUrl(): ?string
    {
        if (Storage::exists($this->file_path)) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getThumbnailUrl(): ?string
    {
        if ($this->thumbnail_path && Storage::exists($this->thumbnail_path)) {
            return Storage::url($this->thumbnail_path);
        }
        return $this->getFileUrl(); // Fallback to original file
    }

    public function isImage(): bool
    {
        return strpos($this->mime_type ?? $this->file_type, 'image/') === 0;
    }

    public function isDocument(): bool
    {
        return in_array($this->file_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getIconClass(): string
    {
        if ($this->isImage()) {
            return 'fas fa-image';
        }
        
        switch ($this->file_type) {
            case 'application/pdf':
                return 'fas fa-file-pdf';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'fas fa-file-word';
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return 'fas fa-file-excel';
            default:
                return 'fas fa-file';
        }
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && empty($model->uploaded_by)) {
                $model->uploaded_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            // Delete physical files when model is deleted
            if (Storage::exists($model->file_path)) {
                Storage::delete($model->file_path);
            }
            if ($model->thumbnail_path && Storage::exists($model->thumbnail_path)) {
                Storage::delete($model->thumbnail_path);
            }
        });
    }
}
