<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\AuditService;
use App\Services\SiteConfigService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeout
{
    public function __construct(
        protected SiteConfigService $config
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutMinutes = (int) ($this->config->get('session_timeout_minutes', '30') ?? '30');
            $timeoutSeconds = $timeoutMinutes * 60;

            $lastActivity = $request->session()->get('last_activity_timestamp');

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                $user = Auth::user();
                AuditService::log('SESSION_TIMEOUT', $user, null, [
                    'inactive_minutes' => $timeoutMinutes,
                    'email' => $user ? $user->email : null,
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('warning', sprintf(
                    'Your session timed out after %d minutes of inactivity for enterprise compliance. Please sign in again.',
                    $timeoutMinutes
                ));
            }

            $request->session()->put('last_activity_timestamp', time());
        }

        return $next($request);
    }
}
