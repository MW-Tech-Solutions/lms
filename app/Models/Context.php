<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Context extends Model
{
    public const LEVEL_SYSTEM = 'SYSTEM';
    public const LEVEL_TENANT = 'TENANT';
    public const LEVEL_DEPARTMENT = 'DEPARTMENT';
    public const LEVEL_CATEGORY = 'CATEGORY';
    public const LEVEL_COURSE = 'COURSE';
    public const LEVEL_MODULE = 'MODULE';

    protected $fillable = [
        'context_level',
        'instance_id',
        'path',
    ];

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }
}
