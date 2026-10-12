<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseChoice extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'allow_multiple',
        'show_results',
        'options_json',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'show_results' => 'boolean',
        'options_json' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
