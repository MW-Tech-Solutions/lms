<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    public const AUDIENCE_CORPORATE = 'CORPORATE';
    public const AUDIENCE_PUBLIC = 'PUBLIC';
    public const AUDIENCE_BOTH = 'BOTH';

    public const FORMAT_WEEKLY = 'WEEKLY';
    public const FORMAT_TOPICS = 'TOPICS';
    public const FORMAT_SELF_PACED = 'SELF_PACED';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'title',
        'slug',
        'thumbnail',
        'description',
        'target_audience',
        'format',
        'passing_grade',
        'recertification_days',
        'price_cents',
        'currency',
        'is_published',
    ];

    public function thumbnailUrl(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://') || str_starts_with($this->thumbnail, '/')) {
            return $this->thumbnail;
        }

        return asset('storage/' . $this->thumbnail);
    }

    protected $casts = [
        'passing_grade' => 'integer',
        'recertification_days' => 'integer',
        'price_cents' => 'integer',
        'is_published' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_course_id'
        );
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function gradeCategories(): HasMany
    {
        return $this->hasMany(GradeCategory::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function formattedPrice(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }
        return sprintf('%s %.2f', $this->currency, $this->price_cents / 100);
    }
}
