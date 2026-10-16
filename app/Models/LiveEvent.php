<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'level_badge',
        'description',
        'event_date',
        'duration_minutes',
        'instructor_name',
        'instructor_role',
        'instructor_avatar',
        'meeting_link',
        'max_capacity',
        'reserved_count',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'max_capacity' => 'integer',
        'reserved_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(EventReservation::class, 'event_id');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date ? $this->event_date->format('M d, Y • h:i A') : 'TBA';
    }

    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->reserved_count);
    }
}
