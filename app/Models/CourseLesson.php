<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'grade_max',
        'allow_retakes',
    ];

    protected $casts = [
        'grade_max' => 'decimal:2',
        'allow_retakes' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(LessonPage::class, 'lesson_id')->orderBy('sort_order');
    }
}
