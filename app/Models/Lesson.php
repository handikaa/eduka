<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'content',
        'video_url',
        'file_url',
        'order_index',
        'is_preview',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'order_index' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course that owns this lesson.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    /**
     * Get the progress records for this lesson.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
