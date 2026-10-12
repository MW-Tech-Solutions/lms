<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_GRADED = 'GRADED';
    public const STATUS_RESUBMIT_REQUESTED = 'RESUBMIT_REQUESTED';

    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_text',
        'file_paths_json',
        'grade',
        'feedback',
        'graded_by_user_id',
        'status',
    ];

    protected $casts = [
        'file_paths_json' => 'array',
        'grade' => 'float',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_user_id');
    }
}
