<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'session_date',
        'qr_secret',
        'lat_boundary',
        'lng_boundary',
        'radius_meters',
    ];

    protected $casts = [
        'session_date' => 'date',
        'lat_boundary' => 'float',
        'lng_boundary' => 'float',
        'radius_meters' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }
}
