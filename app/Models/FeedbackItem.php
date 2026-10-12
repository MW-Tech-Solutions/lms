<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackItem extends Model
{
    protected $fillable = [
        'feedback_id',
        'name',
        'typ',
        'presentation',
        'is_required',
        'position',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'position' => 'integer',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(CourseFeedback::class, 'feedback_id');
    }
}
