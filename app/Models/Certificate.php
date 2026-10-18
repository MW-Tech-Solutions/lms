<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'uuid',
        'certificate_number',
        'user_id',
        'course_id',
        'issued_at',
        'expires_at',
        'pdf_url',
    ];

    public function getCertificateNumberAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $date = $this->issued_at ?? $this->created_at ?? now();
        $prefix = 'ATC';
        if (app()->bound(\App\Services\SiteConfigService::class)) {
            $prefix = (string) app(\App\Services\SiteConfigService::class)->get('certificate_prefix', 'ATC');
        }
        return sprintf('%s-%s-%s', $prefix, $date->format('Y-md'), str_pad((string) $this->id, 3, '0', STR_PAD_LEFT));
    }

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isValid(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
