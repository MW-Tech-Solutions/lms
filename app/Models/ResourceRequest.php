<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceRequest extends Model
{
    protected $table = 'resource_requests';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_IN_REVIEW = 'IN_REVIEW';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_FULFILLED = 'FULFILLED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'user_id',
        'level_name',
        'subject',
        'topic',
        'resource_type',
        'details',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
