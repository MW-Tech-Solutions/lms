<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Context;
use App\Models\User;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckContextPermission
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Handle an incoming request and enforce CBAC permissions traversal.
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $contextParam = null): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $context = null;
        if ($contextParam !== null && $request->route($contextParam)) {
            $instanceId = (string) $request->route($contextParam);
            $context = Context::where('instance_id', $instanceId)->first();
        }

        if (!$this->permissionService->hasPermission($user, $permission, $context)) {
            abort(403, sprintf('CBAC Access Denied: Missing permission "%s" for this operational context.', $permission));
        }

        return $next($request);
    }
}
