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
        $sessions = AttendanceSession::with(['course', 'records'])->latest('session_date')->get();
        $courses = Course::where('is_published', true)->get();

        return view('admin.attendance.index', compact('sessions', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'lat_boundary' => ['nullable', 'numeric'],
            'lng_boundary' => ['nullable', 'numeric'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:5000'],
        ]);

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
