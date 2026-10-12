<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseGroup extends Model
{
    public const MODE_SEPARATE = 'SEPARATE';
    public const MODE_VISIBLE = 'VISIBLE';

    protected $fillable = [
        'course_id',
        'name',
        'group_mode',
        'description',
        'enrollment_key',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_group_members', 'group_id', 'user_id')->withTimestamps();
    }
}
