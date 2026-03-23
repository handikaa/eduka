<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the courses in this category (many-to-many).
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_categories');
    }
}
