<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseFeedback extends Model
{
    protected $table = 'course_feedbacks';

    protected $fillable = [
        'course_id',
        'name',
        'intro',
        'is_anonymous',
        'multiple_submit',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'multiple_submit' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeedbackItem::class, 'feedback_id')->orderBy('position');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class, 'feedback_id');
    }
}
