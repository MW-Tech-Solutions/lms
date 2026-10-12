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
    public const TYPE_SHORT_ANSWER = 'SHORT_ANSWER';
    public const TYPE_FILL_BLANK = 'FILL_BLANK';
    public const TYPE_ESSAY = 'ESSAY';

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
}
