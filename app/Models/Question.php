<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    public const TYPE_SINGLE_CHOICE = 'SINGLE_CHOICE';
    public const TYPE_MULTI_CHOICE = 'MULTI_CHOICE';
    public const TYPE_TRUE_FALSE = 'TRUE_FALSE';
    public const TYPE_MATCHING = 'MATCHING';
    public const TYPE_SHORT_ANSWER = 'SHORT_ANSWER';
    public const TYPE_NUMERICAL = 'NUMERICAL';
    public const TYPE_FILL_BLANK = 'FILL_BLANK';
    public const TYPE_ESSAY = 'ESSAY';
    public const TYPE_DRAG_DROP = 'DRAG_DROP';
    public const TYPE_CALCULATED = 'CALCULATED';
    public const TYPE_CLOZE = 'CLOZE';

    protected $fillable = [
        'bank_id',
        'type',
        'question_text',
        'points',
        'metadata_json',
    ];

    protected $casts = [
        'points' => 'integer',
        'metadata_json' => 'array',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'bank_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('sort_order');
    }

    public function getCourseIdAttribute(): ?int
    {
        return isset($this->metadata_json['course_id']) ? (int) $this->metadata_json['course_id'] : null;
    }

    public function getChapterAttribute(): ?string
    {
        return $this->metadata_json['chapter'] ?? null;
    }

    public function getInstructorIdAttribute(): ?int
    {
        return isset($this->metadata_json['instructor_id']) ? (int) $this->metadata_json['instructor_id'] : null;
    }

    public function getCourseTitleAttribute(): ?string
    {
        if (!empty($this->metadata_json['course_title'])) {
            return $this->metadata_json['course_title'];
        }
        if ($this->course_id) {
            return Course::find($this->course_id)?->title;
        }
        return null;
    }
}
