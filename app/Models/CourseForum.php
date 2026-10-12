<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseForum extends Model
{
    public const TYPE_STANDARD = 'STANDARD';
    public const TYPE_SINGLE_SIMPLE = 'SINGLE_SIMPLE';
    public const TYPE_Q_AND_A = 'Q_AND_A';
    public const TYPE_EACH_USER = 'EACH_USER';

    protected $fillable = [
        'course_id',
        'name',
        'type',
        'intro',
        'grade_enabled',
        'grade_max',
    ];

    protected $casts = [
        'grade_enabled' => 'boolean',
        'grade_max' => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(ForumDiscussion::class, 'forum_id');
    }
}
