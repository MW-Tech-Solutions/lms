<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseBadge extends Model
{
    public const CRITERIA_COURSE_COMPLETION = 'COURSE_COMPLETION';
    public const CRITERIA_ACTIVITY_COMPLETION = 'ACTIVITY_COMPLETION';
    public const CRITERIA_GRADE_THRESHOLD = 'GRADE_THRESHOLD';
    public const CRITERIA_MANUAL = 'MANUAL';

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'badge_image_url',
        'issuer_name',
        'criteria_type',
        'criteria_meta_json',
        'is_active',
    ];

    protected $casts = [
        'criteria_meta_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges', 'badge_id', 'user_id')
            ->withPivot('issued_at', 'verification_token')
            ->withTimestamps();
    }
}
