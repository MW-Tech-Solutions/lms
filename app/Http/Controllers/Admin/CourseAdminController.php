<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseAdminController extends Controller
{
    public function index(): View
    {
        $courses = Course::with(['category', 'sections.modules'])->latest()->get();

        return view('admin.courses.index', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug'],
            'target_audience' => ['required', 'in:CORPORATE,PUBLIC,BOTH'],
            'format' => ['required', 'in:WEEKLY,TOPICS,SELF_PACED'],
            'passing_grade' => ['required', 'integer', 'min:1', 'max:100'],
            'recertification_days' => ['nullable', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'target_audience' => $validated['target_audience'],
            'format' => $validated['format'],
            'passing_grade' => $validated['passing_grade'],
            'recertification_days' => $validated['recertification_days'] ?? null,
            'price_cents' => $validated['price_cents'],
            'currency' => strtoupper($validated['currency']),
            'is_published' => $request->boolean('is_published'),
        ]);

        // Automatically initialize default Section 1
        CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Core Module 1: Foundational Framework',
            'sort_order' => 0,
        ]);

        return back()->with('success', sprintf('Course "%s" created successfully with initial curriculum structure.', $course->title));
    }
}
