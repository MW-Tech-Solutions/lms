<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentStudent extends Model
{
    protected $table = 'parent_students';

    protected $fillable = [
        'parent_id',
        'student_id',
        'relationship',
        'can_view_tracker',
        'can_view_billing',
    ];

    protected $casts = [
        'can_view_tracker' => 'boolean',
        'can_view_billing' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
