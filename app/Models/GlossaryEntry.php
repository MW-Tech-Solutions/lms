<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlossaryEntry extends Model
{
    protected $fillable = [
        'glossary_id',
        'user_id',
        'concept',
        'definition',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function glossary(): BelongsTo
    {
        return $this->belongsTo(CourseGlossary::class, 'glossary_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
