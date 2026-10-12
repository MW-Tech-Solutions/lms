<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'given_answer_json',
        'is_correct',
        'awarded_points',
    ];

    protected $casts = [
        'given_answer_json' => 'array',
        'is_correct' => 'boolean',
        'awarded_points' => 'float',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
