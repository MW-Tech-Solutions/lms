<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseH5p extends Model
{
    protected $table = 'course_h5p_packages';

    public const TYPE_INTERACTIVE_VIDEO = 'INTERACTIVE_VIDEO';
    public const TYPE_BRANCHING_SCENARIO = 'BRANCHING_SCENARIO';
    public const TYPE_FLASHCARDS = 'FLASHCARDS';

    protected $fillable = [
        'course_id',
        'title',
        'package_type',
        'h5p_library',
        'parameters_json',
        'grade_max',
    ];

    protected $casts = [
        'parameters_json' => 'array',
        'grade_max' => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
