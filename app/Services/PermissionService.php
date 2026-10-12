<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Context;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    private const CACHE_TTL_SECONDS = 3600; // 1 hour

    /**
     * Check if a user possesses a specific permission at a given context or any ancestor context.
     * Traversal order: Specific Context -> Materialized Path Ancestors -> SYSTEM Root
     */
    public function hasPermission(User $user, string $permissionName, ?Context $context = null): bool
    {
        // Super Admin bypass at system level
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $contextId = $context ? (string) $context->id : 'SYSTEM';
        $cacheKey = "cbac:user:{$user->id}:perm:{$permissionName}:ctx:{$contextId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($user, $permissionName, $context): bool {
            return $this->evaluateContextHierarchy($user, $permissionName, $context);
        });
    }

    /**
     * Traverses upward from current context along the materialized path to SYSTEM root.
     */
    protected function evaluateContextHierarchy(User $user, string $permissionName, ?Context $context): bool
    {
        // 1. Check direct context if provided
        if ($context !== null) {
            if ($this->hasDirectPermissionAtContext($user, $permissionName, $context->id)) {
                return true;
            }

            // Materialized path contains ancestor context IDs (e.g. "1/4/12")
            $ancestorIds = array_filter(explode('/', $context->path));
            foreach (array_reverse($ancestorIds) as $ancestorId) {
                if ((int) $ancestorId !== (int) $context->id) {
                    if ($this->hasDirectPermissionAtContext($user, $permissionName, (int) $ancestorId)) {
                        return true;
                    }
                }
            }
        }

        // 2. Check tenant level context if user belongs to a tenant
        if ($user->tenant_id) {
            $tenantContext = Context::where('context_level', Context::LEVEL_TENANT)
                ->where('instance_id', (string) $user->tenant_id)
                ->first();

            if ($tenantContext && $this->hasDirectPermissionAtContext($user, $permissionName, $tenantContext->id)) {
                return true;
            }
        }

        // 3. Check system root context
        $systemContext = Context::where('context_level', Context::LEVEL_SYSTEM)->first();
        if ($systemContext && $this->hasDirectPermissionAtContext($user, $permissionName, $systemContext->id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user holds the permission directly assigned at this specific context ID.
     */
    protected function hasDirectPermissionAtContext(User $user, string $permissionName, int $contextId): bool
    {
        return DB::table('role_assignments')
            ->join('role_permissions', 'role_assignments.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_assignments.user_id', $user->id)
            ->where('role_assignments.context_id', $contextId)
            ->where('permissions.name', $permissionName)
            ->exists();
    }

    /**
     * Check if user is assigned the SUPER_ADMIN role at any context.
     */
    public function isSuperAdmin(User $user): bool
    {
        return Cache::remember("cbac:user:{$user->id}:is_super_admin", self::CACHE_TTL_SECONDS, function () use ($user): bool {
            return DB::table('role_assignments')
                ->join('roles', 'role_assignments.role_id', '=', 'roles.id')
                ->where('role_assignments.user_id', $user->id)
                ->where('roles.name', 'SUPER_ADMIN')
                ->exists();
        });
    }

    /**
     * Flush CBAC cache for a specific user.
     */
    public function flushUserCache(User $user): void
    {
        Cache::forget("cbac:user:{$user->id}:is_super_admin");
        // Clear tagged or wildcards if Redis taggable, or specific keys
        if (Cache::supportsTags()) {
            Cache::tags(["user_{$user->id}_cbac"])->flush();
        }
    }
}
