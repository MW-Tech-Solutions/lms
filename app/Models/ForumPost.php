<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumPost extends Model
{
    protected $fillable = [
        'discussion_id',
        'user_id',
        'parent_id',
        'subject',
        'message',
        'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(ForumDiscussion::class, 'discussion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
