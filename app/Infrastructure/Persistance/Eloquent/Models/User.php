<?php

namespace App\Infrastructure\Persistance\Eloquent\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password_hash', 'role', 'is_active', 'avatar_url'])]
#[Hidden(['password_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Get the courses created by this instructor.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the lesson progress records for this user.
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get the enrollments for this user (courses enrolled in).
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the course reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }
}
