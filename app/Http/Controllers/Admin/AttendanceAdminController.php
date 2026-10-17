<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceAdminController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        $isApproved = true;
        if ($isTeacher) {
            $isApproved = ($user->approval_status === 'APPROVED');
            $authorizedCategoryIds = $user->getAuthorizedCategoryIds();

            // Teachers can ONLY see attendance sessions for their own courses and authorized levels
            $sessions = AttendanceSession::whereHas('course', function ($q) use ($user, $authorizedCategoryIds) {
                $q->where('instructor_id', $user->id);
                if (!empty($authorizedCategoryIds)) {
                    $q->whereIn('category_id', $authorizedCategoryIds);
                }
            })->with(['course', 'records'])->latest('session_date')->get();

            // Teachers can ONLY select their own courses within their authorized levels
            $coursesQuery = Course::where('instructor_id', $user->id)->where('is_published', true);
            if (!empty($authorizedCategoryIds)) {
                $coursesQuery->whereIn('category_id', $authorizedCategoryIds);
            }
            $courses = $coursesQuery->get();
        } else {
            // Administrators see all attendance sessions and courses
            $sessions = AttendanceSession::with(['course', 'records'])->latest('session_date')->get();
            $courses = Course::where('is_published', true)->get();
        }

        return view('admin.attendance.index', compact('sessions', 'courses', 'isTeacher', 'isApproved'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        // 1. Unapproved teachers cannot create attendance
        if ($isTeacher && $user->approval_status !== 'APPROVED') {
            return back()->with('error', 'Your teacher account is pending administrator approval. You cannot create attendance sessions yet.');
        }

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'lat_boundary' => ['nullable', 'numeric'],
            'lng_boundary' => ['nullable', 'numeric'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:5000'],
        ]);

        $course = Course::findOrFail((int) $validated['course_id']);

        // 2. Teachers can ONLY create attendance for their own class / course
        if ($isTeacher) {
            if ((int) $course->instructor_id !== (int) $user->id) {
                return back()->with('error', 'Unauthorized: You can only create attendance sessions for your own assigned courses.');
            }

            // 3. Teachers can ONLY create attendance for their assigned / authorized Cambridge level
            $authorizedCategoryIds = $user->getAuthorizedCategoryIds();
            if (!empty($authorizedCategoryIds) && !in_array((int) $course->category_id, array_map('intval', $authorizedCategoryIds), true)) {
                return back()->with('error', 'Unauthorized: This course does not belong to your approved teaching level.');
            }
        }

        $session = AttendanceSession::create([
            'course_id' => (int) $validated['course_id'],
            'title' => $validated['title'],
            'session_date' => $validated['session_date'],
            'qr_secret' => Str::random(32),
            'lat_boundary' => $validated['lat_boundary'] ? (float) $validated['lat_boundary'] : null,
            'lng_boundary' => $validated['lng_boundary'] ? (float) $validated['lng_boundary'] : null,
            'radius_meters' => (int) $validated['radius_meters'],
        ]);

        return back()->with('success', sprintf('Attendance session "%s" created with rolling QR secret.', $session->title));
    }
}
