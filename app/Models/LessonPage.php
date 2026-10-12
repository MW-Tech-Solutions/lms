<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonPage extends Model
{
    public const TYPE_CONTENT = 'CONTENT';
    public const TYPE_QUESTION = 'QUESTION';

    protected $fillable = [
        'lesson_id',
        'title',
        'content_html',
        'page_type',
        'qtype',
        'jumps_json',
        'sort_order',
    ];

    protected $casts = [
        'jumps_json' => 'array',
        'sort_order' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}
