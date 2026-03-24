<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'description',
        'level',
        'price',
        'thumbnail_url',
        'status',
        'quota',
        'enrolled_count',
        'rating_count',
        'rating_avg',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'price' => 'integer',
        'enrolled_count' => 'integer',
        'rating_count' => 'integer',
        'rating_avg' => 'decimal:2',
        'quota' => 'integer',
        'instructor_id' => 'integer',
        'status' => 'string',
        'level' => 'string',
        'title' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'thumbnail_url' => 'string',
    ];

    /**
     * Get the categories this course belongs to (many-to-many).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_categories');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the lessons for this course.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the enrollments for this course (students enrolled).
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the reviews for this course.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }
}
