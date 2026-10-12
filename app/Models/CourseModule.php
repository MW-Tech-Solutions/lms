<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseModule extends Model
{
    public const TYPE_LESSON = 'LESSON';
    public const TYPE_VIDEO = 'VIDEO';
    public const TYPE_SCORM = 'SCORM';
    public const TYPE_QUIZ = 'QUIZ';
    public const TYPE_ASSIGNMENT = 'ASSIGNMENT';
    public const TYPE_ATTENDANCE = 'ATTENDANCE';
    public const TYPE_FORUM = 'FORUM';
    public const TYPE_LIVE_SESSION = 'LIVE_SESSION';

    protected $fillable = [
        'section_id',
        'module_type',
        'instance_id',
        'sort_order',
        'is_mandatory',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_mandatory' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
}
