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
        $user = auth()->user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();
        $isManager = $user && $user->hasRole([\App\Models\Role::MANAGER, \App\Models\Role::CORP_MANAGER]) && !$user->isSuperAdmin();

        if ($isTeacher) {
            // Teacher-specific statistics & context
            $myCourses = \App\Models\Course::where('instructor_id', $user->id)->get();
            $myCourseIds = $myCourses->pluck('id')->all();

            $myCoursesCount = $myCourses->count();
            $myActiveCoursesCount = $myCourses->where('is_published', true)->count();
            $myDraftCoursesCount = $myCourses->where('is_published', false)->count();

            $myEnrolledStudentsCount = \App\Models\Enrollment::whereIn('course_id', $myCourseIds)->distinct('user_id')->count('user_id');
            $myTotalSubmissionsCount = \App\Models\AssignmentSubmission::whereHas('assignment.module.section', function ($q) use ($myCourseIds) {
                $q->whereIn('course_id', $myCourseIds);
            })->count();

            $myPendingGradingCount = \App\Models\AssignmentSubmission::whereHas('assignment.module.section', function ($q) use ($myCourseIds) {
                $q->whereIn('course_id', $myCourseIds);
            })->whereNull('grade')->count();

            $myAssignedLevels = $user->teachingLevels()->get();
            $pendingLevelRequests = $user->levelRequests()->where('status', 'PENDING')->with('category')->get();

            $availableCambridgeLevels = \App\Models\CourseCategory::where(function ($q) {
                $q->where('name', 'like', 'Cambridge%')
                  ->orWhereIn('slug', ['primary-checkpoint', 'lower-secondary', 'igcse', 'as-level', 'a-level']);
            })->orderBy('id')->get();

            return view('admin.dashboard', compact(
                'isTeacher',
                'myCourses',
                'myCoursesCount',
                'myActiveCoursesCount',
                'myDraftCoursesCount',
                'myEnrolledStudentsCount',
                'myTotalSubmissionsCount',
                'myPendingGradingCount',
                'myAssignedLevels',
                'pendingLevelRequests',
                'availableCambridgeLevels'
            ));
        }

        // Super Admin & Executive Manager View
        $totalUsersCount = User::count();
        $corpUsersCount = User::whereIn('user_type', ['EMPLOYEE', 'HYBRID'])->count();
        $academicUsersCount = User::where('user_type', 'STUDENT')->count();
        $teacherUsersCount = User::whereHas('roleAssignments.role', fn($q) => $q->whereIn('name', ['TEACHER', 'INSTRUCTOR', 'TEACHER_NON_EDITING']))->count();

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
        $pendingTeacherApprovalsCount = User::where('approval_status', 'PENDING')->count();
        $pendingTeacherLevelRequests = \App\Models\TeacherLevelRequest::where('status', 'PENDING')->with(['user', 'category'])->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'isTeacher',
            'totalUsersCount',
            'corpUsersCount',
            'academicUsersCount',
            'teacherUsersCount',
            'completedEnrollmentsCount',
            'complianceRate',
            'pendingRecertCount',
            'antiCheatFlagsCount',
            'departments',
            'recentCheatLogs',
            'pendingTeacherApprovalsCount',
            'pendingTeacherLevelRequests'
        ));
    }
}
