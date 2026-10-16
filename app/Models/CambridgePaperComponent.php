<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CambridgePaperComponent extends Model
{
    protected $table = 'cambridge_paper_components';

    protected $fillable = [
        'course_id',
        'code',
        'name',
        'default_max_score',
        'sort_order',
    ];

    protected $casts = [
        'default_max_score' => 'integer',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PastPaperAttempt::class, 'component_id');
    }
}
