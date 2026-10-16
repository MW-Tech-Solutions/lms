<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $table = 'user_subscriptions';

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAST_DUE = 'PAST_DUE';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_EXPIRED = 'EXPIRED';

    protected $fillable = [
        'user_id',
        'plan_id',
        'category_id',
        'status',
        'gateway',
        'gateway_subscription_id',
        'starts_at',
        'ends_at',
        'canceled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function isValid(): bool
    {
        return $this->status === self::STATUS_ACTIVE && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
