<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const GATEWAY_STRIPE = 'STRIPE';
    public const GATEWAY_PAYPAL = 'PAYPAL';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'total_amount_cents',
        'currency',
        'payment_gateway',
        'payment_id',
        'status',
    ];

    protected $casts = [
        'total_amount_cents' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
