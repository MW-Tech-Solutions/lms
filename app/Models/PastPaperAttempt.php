<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PastPaperAttempt extends Model
{
    protected $table = 'past_paper_attempts';

    public const STATUS_GREEN = 'GREEN';
    public const STATUS_AMBER = 'AMBER';
    public const STATUS_RED = 'RED';

    protected $fillable = [
        'user_id',
        'course_id',
        'series_id',
        'component_id',
        'year',
        'season',
        'variant',
        'score_obtained',
        'max_score',
        'percentage',
        'status_color',
        'notes',
        'attempt_number',
        'is_latest',
    ];

    protected $casts = [
        'year' => 'integer',
        'variant' => 'integer',
        'score_obtained' => 'float',
        'max_score' => 'float',
        'percentage' => 'float',
        'attempt_number' => 'integer',
        'is_latest' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(CambridgeExamSeries::class, 'series_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(CambridgePaperComponent::class, 'component_id');
    }

    /**
     * Compute color threshold based on percentage:
     * Green: 75% - 100%
     * Amber: 50% - 74.99%
     * Red: Below 50%
     */
    public static function calculateStatusColor(float $percentage): string
    {
        if ($percentage >= 75.0) {
            return self::STATUS_GREEN;
        }

        if ($percentage >= 50.0) {
            return self::STATUS_AMBER;
        }

        return self::STATUS_RED;
    }
}
