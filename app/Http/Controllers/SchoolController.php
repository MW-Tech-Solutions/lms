<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * Display public/authenticated landing portal for a school: /schools/{slug}
     * Example: allthingscambridge.co.uk/schools/mundramodelschool
     */
    public function show(string $slug): View
    {
        $school = Tenant::where('slug', $slug)
            ->orWhere('subdomain', $slug)
            ->firstOrFail();

        $branding = $school->branding_json ?? [];
        $schoolCode = $school->school_code ?? ('ATC-EMP-' . substr(md5($school->id), 0, 5));

        // Teachers linked to this school
        $teachers = User::where('tenant_id', $school->id)
            ->whereHas('roleAssignments.role', function ($q) {
                $q->whereIn('name', ['TEACHER', 'INSTRUCTOR', 'TEACHER_NON_EDITING']);
            })
            ->with('profile')
            ->get();

        // Enrolled students count in this school
        $studentCount = User::where('tenant_id', $school->id)
            ->where('user_type', 'STUDENT')
            ->count();

        // Cambridge Levels and Syllabuses available to this school
        $categories = CourseCategory::withCount(['courses' => function ($q) {
            $q->where('is_published', true);
        }])->get();

        $featuredCourses = Course::where('is_published', true)
            ->with(['category', 'instructor'])
            ->take(6)
            ->get();

        $inviteUrl = url('/register?school_code=' . urlencode($schoolCode));

        return view('schools.show', compact(
            'school',
            'branding',
            'schoolCode',
            'teachers',
            'studentCount',
            'categories',
            'featuredCourses',
            'inviteUrl'
        ));
    }
}
