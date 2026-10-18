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

    public const DELETION_NONE = 'NONE';
    public const DELETION_REQUESTED = 'REQUESTED';
    public const DELETION_APPROVED = 'APPROVED';
    public const DELETION_REJECTED = 'REJECTED';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'instructor_id',
        'title',
        'slug',
        'thumbnail',
        'video_url',
        'material_file',
        'material_notes',
        'description',
        'target_audience',
        'format',
        'passing_grade',
        'recertification_days',
        'price_cents',
        'proposed_price_cents',
        'special_pricing_tag',
        'price_approval_status',
        'rejection_reason',
        'material_approval_status',
        'material_rejection_reason',
        'currency',
        'is_published',
        'deletion_status',
        'deletion_reason',
        'public_preview_content',
        'extracted_material_html',
        'extracted_material_title',
    ];

    /**
     * Check if the course video URL is an embeddable stream (YouTube, Vimeo, etc.)
     */
    public function isVideoEmbed(): bool
    {
        if (empty($this->video_url)) {
            return false;
        }

        $url = trim($this->video_url);
        return (bool) preg_match('/(?:youtube\.com|youtu\.be|vimeo\.com)/i', $url);
    }

    /**
     * Convert standard YouTube or Vimeo links into secure, privacy-compliant iframe embed URLs
     */
    public function videoEmbedUrl(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // YouTube detection (handles youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID, /shorts/ID)
        if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|v\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?rel=0&modestbranding=1&enablejsapi=1";
        }

        // Vimeo detection
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $url, $matches)) {
            $vimeoId = end($matches);
            return "https://player.vimeo.com/video/{$vimeoId}?dnt=1&title=0&byline=0&portrait=0";
        }

        return $url;
    }

    public function getCourseCodeAttribute(): string
    {
        $prefix = 'CAM';
        if ($this->relationLoaded('category') && $this->category) {
            $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $this->category->name);
            if (!empty($cleaned)) {
                $prefix = strtoupper(substr($cleaned, 0, 3));
            }
        }
        return sprintf('%s-%03d-T%d', $prefix, $this->id, $this->instructor_id ?? 0);
    }

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

    public function videoUrl(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (str_starts_with($this->video_url, 'http://') || str_starts_with($this->video_url, 'https://') || str_starts_with($this->video_url, '/')) {
            return $this->video_url;
        }

        return asset('storage/' . $this->video_url);
    }

    public function materialUrl(): ?string
    {
        if (!$this->material_file) {
            return null;
        }

        if (str_starts_with($this->material_file, 'http://') || str_starts_with($this->material_file, 'https://') || str_starts_with($this->material_file, '/')) {
            return $this->material_file;
        }

        return asset('storage/' . $this->material_file);
    }

    public function isPendingDeletion(): bool
    {
        return $this->deletion_status === self::DELETION_REQUESTED;
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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
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

    /**
     * Get the total dynamic count of lessons/modules across all sections of this course.
     */
    public function getLessonsCountAttribute(): int
    {
        if ($this->relationLoaded('sections')) {
            return $this->sections->sum(function ($section) {
                return $section->relationLoaded('modules') ? $section->modules->count() : $section->modules()->count();
            });
        }

        return (int) \App\Models\CourseModule::whereIn(
            'section_id',
            $this->sections()->pluck('id')
        )->count();
    }

    /**
     * Get the dynamic enrolled students/scholars count for this course.
     */
    public function getEnrolledCountAttribute(): int
    {
        if ($this->relationLoaded('enrollments')) {
            return $this->enrollments->count();
        }

        return (int) $this->enrollments()->count();
    }

    /**
     * Human formatted enrolled scholars (e.g. 1.2k or 45).
     */
    public function formattedEnrolledCount(): string
    {
        $count = $this->enrolled_count;
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'k';
        }
        return (string) $count;
    }

    /**
     * Get estimated duration computed dynamically based on modules and lessons.
     */
    public function getEstimatedDurationAttribute(): string
    {
        $lessons = $this->lessons_count;
        if ($lessons === 0) {
            return 'Self-Paced';
        }

        // Standard pedagogy estimate: ~25 mins per curriculum module / worked solution
        $totalMinutes = $lessons * 25;
        $hours = intdiv($totalMinutes, 60);
        $mins = $totalMinutes % 60;

        if ($hours === 0) {
            return "{$mins}m";
        }
        if ($mins === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$mins}m";
    }

    /**
     * Get dynamic instructor name or professional pedagogical title.
     */
    public function getInstructorTitleAttribute(): string
    {
        if ($this->relationLoaded('instructor') && $this->instructor) {
            return $this->instructor->name;
        }

        if ($this->target_audience === self::AUDIENCE_CORPORATE) {
            return 'Corporate Compliance Board';
        }

        return 'Cambridge Senior Faculty';
    }

    public function formattedPrice(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }
        $curr = strtoupper($this->currency ?? 'USD');
        if ($curr === 'GBP') {
            return sprintf('£%.2f', $this->price_cents / 100);
        }
        if ($curr === 'EUR') {
            return sprintf('€%.2f', $this->price_cents / 100);
        }
        if ($curr === 'USD') {
            return sprintf('$%.2f', $this->price_cents / 100);
        }
        return sprintf('%s %.2f', $curr, $this->price_cents / 100);
    }
}
