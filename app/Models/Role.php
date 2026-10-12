<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN = 'SUPER_ADMIN';
    public const CORP_MANAGER = 'CORP_MANAGER';
    public const INSTRUCTOR = 'INSTRUCTOR';
    public const TRAINER = 'TRAINER';
    public const LEARNER_EMPLOYEE = 'LEARNER_EMPLOYEE';
    public const LEARNER_STUDENT = 'LEARNER_STUDENT';

    protected $fillable = [
        'name',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }
}
