<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseWorkshop extends Model
{
    public const PHASE_SETUP = 'SETUP';
    public const PHASE_SUBMISSION = 'SUBMISSION';
    public const PHASE_ASSESSMENT = 'ASSESSMENT';
    public const PHASE_EVALUATION = 'EVALUATION';
    public const PHASE_CLOSED = 'CLOSED';

    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'phase',
        'grade_submission_max',
        'grade_assessment_max',
        'grading_strategy',
        'rubric_criteria_json',
    ];

    protected $casts = [
        'grade_submission_max' => 'decimal:2',
        'grade_assessment_max' => 'decimal:2',
        'rubric_criteria_json' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
