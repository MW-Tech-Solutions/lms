<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningReflection extends Model
{
    protected $table = 'learning_reflections';

    public const CHOICE_UNDERSTOOD = 'UNDERSTOOD';
    public const CHOICE_NEED_RETURN = 'NEED_RETURN';
    public const CHOICE_NOT_UNDERSTOOD = 'NOT_UNDERSTOOD';

    protected $fillable = [
        'user_id',
        'course_id',
        'module_id',
        'reflection_choice',
        'watch_percentage',
        'is_flagged_for_teacher',
        'student_comment',
    ];

    protected $casts = [
        'watch_percentage' => 'float',
        'is_flagged_for_teacher' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }
}
