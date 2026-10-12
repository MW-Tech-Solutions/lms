<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseGlossary extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'is_global',
        'allow_comments',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'allow_comments' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
