<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseWiki extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'first_page_title',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
