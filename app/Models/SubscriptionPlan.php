<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';

    public const DURATION_3_MONTHS = '3_MONTHS';
    public const DURATION_12_MONTHS = '12_MONTHS';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'duration_term',
        'price_pence',
        'currency',
        'stripe_price_id',
        'paypal_plan_id',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price_pence' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function formattedPrice(): string
    {
        $pounds = $this->price_pence / 100;
        return sprintf('£%.2f', $pounds);
    }
}
