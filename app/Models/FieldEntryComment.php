<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class FieldEntryComment extends Model
{
    use SoftDeletes;

    protected $table = 'field_entry_comments';

    protected $fillable = [
        'field_entry_id',
        'user_id',
        'parent_id',
        'content',
        'is_internal_note',
        'type',
        'resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'comment',
        'is_internal_note' => false,
        'resolved' => false,
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FieldEntryComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FieldEntryComment::class, 'parent_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_internal_note', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal_note', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function hasReplies(): bool
    {
        return $this->replies()->count() > 0;
    }

    public function markAsResolved(?User $resolver = null): void
    {
        $this->update([
            'resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $resolver ? $resolver->id : Auth::id(),
        ]);
    }

    public function markAsUnresolved(): void
    {
        $this->update([
            'resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null,
        ]);
    }

    public function getFormattedContent(): string
    {
        // Basic formatting - could be extended with markdown or other formatting
        $content = htmlspecialchars($this->content);
        $content = nl2br($content);
        return $content;
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
}
