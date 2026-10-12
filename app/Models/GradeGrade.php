<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeGrade extends Model
{
    protected $fillable = [
        'grade_item_id',
        'user_id',
        'final_score',
        'is_locked',
    ];

    protected $casts = [
        'final_score' => 'float',
        'is_locked' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(GradeItem::class, 'grade_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GradeHistory::class, 'grade_grade_id')->latest();
    }
}
