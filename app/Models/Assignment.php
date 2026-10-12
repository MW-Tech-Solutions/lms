<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'instructions',
        'max_file_size_mb',
        'allowed_mime_types',
        'due_date',
        'max_points',
        'rubric_json',
    ];

    protected $casts = [
        'max_file_size_mb' => 'integer',
        'max_points' => 'integer',
        'rubric_json' => 'array',
        'due_date' => 'datetime',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
