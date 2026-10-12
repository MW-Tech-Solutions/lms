<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalUsersCount = User::count();
        $corpUsersCount = User::whereIn('user_type', ['EMPLOYEE', 'HYBRID'])->count();
        $academicUsersCount = User::where('user_type', 'STUDENT')->count();

        $completedEnrollmentsCount = Enrollment::where('status', Enrollment::STATUS_COMPLETED)->count();
        $totalEnrollmentsCount = Enrollment::count();
        $complianceRate = $totalEnrollmentsCount > 0
            ? round(($completedEnrollmentsCount / $totalEnrollmentsCount) * 100, 1)
            : 100.0;

        $pendingRecertCount = Enrollment::where('status', Enrollment::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', Carbon::now()->addDays(30))
            ->count();

        $antiCheatFlagsCount = AntiCheatLog::count();

        $departments = Department::with(['manager', 'users'])->get();
        $recentCheatLogs = AntiCheatLog::with('attempt.user')->latest('recorded_at')->take(8)->get();

        return view('admin.dashboard', compact(
            'totalUsersCount',
            'corpUsersCount',
            'academicUsersCount',
            'completedEnrollmentsCount',
            'complianceRate',
            'pendingRecertCount',
            'antiCheatFlagsCount',
            'departments',
            'recentCheatLogs'
        ));
    }
}
