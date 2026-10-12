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
            'description' => ['nullable', 'string'],
            'target_audience' => ['required', 'in:CORPORATE,PUBLIC,BOTH'],
            'format' => ['required', 'in:WEEKLY,TOPICS,SELF_PACED'],
            'passing_grade' => ['required', 'integer', 'min:1', 'max:100'],
            'recertification_days' => ['nullable', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'is_published' => ['nullable', 'boolean'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail_file')) {
            $thumbnailPath = $request->file('thumbnail_file')->store('courses', 'public');
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = $validated['thumbnail_url'];
        }

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'thumbnail' => $thumbnailPath,
            'description' => $validated['description'] ?? null,
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

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug,' . $course->id],
            'description' => ['nullable', 'string'],
            'target_audience' => ['required', 'in:CORPORATE,PUBLIC,BOTH'],
            'format' => ['required', 'in:WEEKLY,TOPICS,SELF_PACED'],
            'passing_grade' => ['required', 'integer', 'min:1', 'max:100'],
            'recertification_days' => ['nullable', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'is_published' => ['nullable', 'boolean'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'target_audience' => $validated['target_audience'],
            'format' => $validated['format'],
            'passing_grade' => $validated['passing_grade'],
            'recertification_days' => $validated['recertification_days'] ?? null,
            'price_cents' => $validated['price_cents'],
            'currency' => strtoupper($validated['currency']),
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $request->file('thumbnail_file')->store('courses', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $data['thumbnail'] = $validated['thumbnail_url'];
        }

        $course->update($data);

        return back()->with('success', sprintf('Course "%s" updated successfully.', $course->title));
    }

    public function destroy(Course $course): RedirectResponse
    {
        $title = $course->title;
        $course->delete();

        return back()->with('success', sprintf('Course "%s" removed successfully.', $title));
    }
}
