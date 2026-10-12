<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'time_limit_minutes',
        'shuffle_questions',
        'max_attempts',
        'passing_percentage',
        'anti_cheat_flags',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'shuffle_questions' => 'boolean',
        'max_attempts' => 'integer',
        'passing_percentage' => 'integer',
        'anti_cheat_flags' => 'array',
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isActive(): bool
    {
        if (isset($this->attributes['is_active'])) {
            return (bool) $this->attributes['is_active'];
        }
        return (bool) ($this->anti_cheat_flags['is_active'] ?? true);
    }

    public function hasAntiCheatFlag(string $flag): bool
    {
        return !empty($this->anti_cheat_flags[$flag]);
    }
}
