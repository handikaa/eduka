<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment',
        'is_delete',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_delete' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course this review belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user (student) who made this review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark review as deleted (soft delete using is_delete flag).
     */
    public function markAsDeleted(): void
    {
        $this->update([
            'is_delete' => true,
        ]);
    }

    /**
     * Restore deleted review.
     */
    public function restore(): void
    {
        $this->update([
            'is_delete' => false,
        ]);
    }

    /**
     * Check if review is deleted.
     */
    public function isDeleted(): bool
    {
        return $this->is_delete;
    }

    /**
     * Validate rating is between 1 and 5.
     */
    public function isValidRating(): bool
    {
        return $this->rating >= 1 && $this->rating <= 5;
    }
}
