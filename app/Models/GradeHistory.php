<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHistory extends Model
{
    protected $fillable = [
        'grade_grade_id',
        'old_score',
        'new_score',
        'modified_by_user_id',
        'justification',
    ];

    protected $casts = [
        'old_score' => 'float',
        'new_score' => 'float',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(GradeGrade::class, 'grade_grade_id');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_user_id');
    }
}
