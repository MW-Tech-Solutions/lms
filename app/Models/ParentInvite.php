<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentInvite extends Model
{
    protected $table = 'parent_invites';

    protected $fillable = [
        'student_id',
        'invite_code',
        'guardian_email',
        'expires_at',
        'is_used',
        'used_by_parent_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_parent_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
