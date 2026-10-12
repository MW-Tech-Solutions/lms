<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeCategory;
use App\Models\GradeGrade;
use App\Models\GradeHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradebookAdminController extends Controller
{
    public function index(): View
    {
        $categories = GradeCategory::with('course')->get();
        $grades = GradeGrade::with(['user', 'item.course', 'item.category'])->latest()->paginate(25);

        return view('admin.gradebook.index', compact('categories', 'grades'));
    }

    /**
     * Override learner grade and write immutable audit trail history.
     */
    public function override(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade_id' => ['required', 'exists:grade_grades,id'],
            'new_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'justification' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $grade = GradeGrade::findOrFail($validated['grade_id']);
        $oldScore = $grade->final_score;
        $newScore = (float) $validated['new_score'];

        $grade->update([
            'final_score' => $newScore,
        ]);

        GradeHistory::create([
            'grade_grade_id' => $grade->id,
            'old_score' => $oldScore,
            'new_score' => $newScore,
            'modified_by_user_id' => Auth::id(),
            'justification' => $validated['justification'],
        ]);

        \App\Services\AuditService::log('GRADEBOOK_OVERRIDE', $grade, ['score' => $oldScore], [
            'score' => $newScore,
            'justification' => $validated['justification'],
            'overridden_by' => Auth::user()->name ?? 'Instructor',
        ]);

        return back()->with('success', 'Grade score updated and justification logged to immutable audit trail.');
    }
}
