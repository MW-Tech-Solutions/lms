<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ParentInvite;
use App\Models\ParentStudent;
use App\Models\PastPaperAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    /**
     * Display parent guardian overview with linked students.
     */
    public function index(): View
    {
        $parent = Auth::user();

        $linkedStudents = ParentStudent::with(['student.enrollments.course'])
            ->where('parent_id', $parent->id)
            ->get();

        foreach ($linkedStudents as $link) {
            $link->attempts_count = PastPaperAttempt::where('user_id', $link->student_id)->count();
            $link->mastery_count = PastPaperAttempt::where('user_id', $link->student_id)->where('status_color', PastPaperAttempt::STATUS_GREEN)->count();
            $link->developing_count = PastPaperAttempt::where('user_id', $link->student_id)->where('status_color', PastPaperAttempt::STATUS_AMBER)->count();
            $link->remedial_count = PastPaperAttempt::where('user_id', $link->student_id)->where('status_color', PastPaperAttempt::STATUS_RED)->count();
        }

        return view('parent.dashboard', compact('parent', 'linkedStudents'));
    }

    /**
     * Generate student invite code (CMP-XXXXXX, valid for 48 hours).
     */
    public function generateInvite(Request $request): JsonResponse
    {
        $student = Auth::user();

        // Expire any pending unused invites
        ParentInvite::where('student_id', $student->id)
            ->where('is_used', false)
            ->delete();

        $code = 'CMP-' . strtoupper(Str::random(6));

        $invite = ParentInvite::create([
            'student_id' => $student->id,
            'invite_code' => $code,
            'guardian_email' => $request->input('guardian_email'),
            'expires_at' => now()->addHours(48),
            'is_used' => false,
        ]);

        return response()->json([
            'success' => true,
            'invite_code' => $code,
            'expires_at' => $invite->expires_at->toIso8601String(),
            'message' => 'Parent invitation code generated. It is valid for 48 hours.',
        ]);
    }

    /**
     * Parent links a student using the 6-character code.
     */
    public function linkStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string', 'max:15'],
        ]);

        $parent = Auth::user();

        $invite = ParentInvite::where('invite_code', strtoupper(trim($validated['invite_code'])))
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invite) {
            return back()->withErrors(['invite_code' => 'Invalid or expired invitation code. Please request a new code from your child.']);
        }

        ParentStudent::firstOrCreate(
            [
                'parent_id' => $parent->id,
                'student_id' => $invite->student_id,
            ],
            [
                'relationship' => 'Parent/Guardian',
                'can_view_tracker' => true,
                'can_view_billing' => true,
            ]
        );

        $invite->update([
            'is_used' => true,
            'used_by_parent_id' => $parent->id,
        ]);

        return redirect()->route('parent.dashboard')->with('success', 'Student successfully connected to your guardian portal.');
    }

    /**
     * View child's revision tracker and academic progress (read-only privacy enforcement).
     */
    public function viewChildProgress(User $student): View
    {
        $parent = Auth::user();

        $link = ParentStudent::where('parent_id', $parent->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $attempts = PastPaperAttempt::with('course')
            ->where('user_id', $student->id)
            ->where('is_latest', true)
            ->latest()
            ->get();

        $greenCount = $attempts->where('status_color', PastPaperAttempt::STATUS_GREEN)->count();
        $amberCount = $attempts->where('status_color', PastPaperAttempt::STATUS_AMBER)->count();
        $redCount = $attempts->where('status_color', PastPaperAttempt::STATUS_RED)->count();

        return view('parent.child_progress', compact('student', 'link', 'attempts', 'greenCount', 'amberCount', 'redCount'));
    }
}
