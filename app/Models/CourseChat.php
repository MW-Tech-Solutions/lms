<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseChat extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'chat_time',
    ];

    protected $casts = [
        'chat_time' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
