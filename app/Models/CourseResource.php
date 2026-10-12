<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResource extends Model
{
    public const TYPE_PAGE = 'PAGE';
    public const TYPE_BOOK = 'BOOK';
    public const TYPE_FILE = 'FILE';
    public const TYPE_FOLDER = 'FOLDER';
    public const TYPE_URL = 'URL';
    public const TYPE_LABEL = 'LABEL';

    protected $fillable = [
        'course_id',
        'section_id',
        'resource_type',
        'title',
        'intro',
        'content_html',
        'external_url',
        'files_json',
        'chapters_json',
        'sort_order',
    ];

    protected $casts = [
        'files_json' => 'array',
        'chapters_json' => 'array',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
}
