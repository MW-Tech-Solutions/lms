<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    public const STATE_IN_PROGRESS = 'IN_PROGRESS';
    public const STATE_SUBMITTED = 'SUBMITTED';
    public const STATE_GRADED = 'GRADED';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'state',
        'score_obtained',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'score_obtained' => 'float',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }

    public function antiCheatLogs(): HasMany
    {
        return $this->hasMany(AntiCheatLog::class, 'attempt_id');
    }
}
