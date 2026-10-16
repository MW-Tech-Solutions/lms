<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CambridgeExamSeries extends Model
{
    protected $table = 'cambridge_exam_series';

    protected $fillable = [
        'year',
        'season',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function attempts(): HasMany
    {
        return $this->hasMany(PastPaperAttempt::class, 'series_id');
    }
}
