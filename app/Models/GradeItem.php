<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeItem extends Model
{
    public const TYPE_QUIZ = 'QUIZ';
    public const TYPE_ASSIGNMENT = 'ASSIGNMENT';
    public const TYPE_MANUAL = 'MANUAL';

    protected $fillable = [
        'course_id',
        'category_id',
        'item_type',
        'item_id',
        'max_score',
    ];

    protected $casts = [
        'max_score' => 'float',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GradeCategory::class, 'category_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(GradeGrade::class, 'grade_item_id');
    }
}
