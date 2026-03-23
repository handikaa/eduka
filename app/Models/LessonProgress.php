<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this progress record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson that this progress is tracking.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if lesson is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark lesson as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Mark lesson as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => 'in_progress',
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Update last accessed time.
     */
    public function updateLastAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }
}
