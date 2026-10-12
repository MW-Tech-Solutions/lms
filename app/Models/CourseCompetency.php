<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCompetency extends Model
{
    protected $fillable = [
        'course_id',
        'idnumber',
        'framework_name',
        'description',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
