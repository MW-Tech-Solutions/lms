<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeCategory extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'weight_percentage',
        'drop_lowest_count',
    ];

    protected $casts = [
        'weight_percentage' => 'float',
        'drop_lowest_count' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GradeItem::class, 'category_id');
    }
}
