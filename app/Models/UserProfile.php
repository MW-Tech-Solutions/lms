<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar_url',
        'phone',
        'job_title',
        'timezone',
        'locale',
        'bio',
        'about_instructor',
        'teaching_methodology',
        'qualifications',
        'kyc_status',
        'kyc_submitted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
