<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an immutable audit event in the database.
     */
    public static function log(
        string $event,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): AuditLog {
        $user = Auth::user();
        $actingUserId = $userId ?? ($user ? $user->id : null);

        return AuditLog::create([
            'user_id' => $actingUserId,
            'tenant_id' => null,
            'event' => strtoupper($event),
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'Unknown User Agent', 0, 500),
            'created_at' => now(),
        ]);
    }
}
