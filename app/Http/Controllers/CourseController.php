<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Public course catalog organized by category / Cambridge level, with audience filtering and search.
     */
    public function index(Request $request): View
    {
        $categories = \App\Models\CourseCategory::orderBy('id', 'asc')->get();

        $selectedCategory = null;
        if ($request->filled('category') || $request->filled('level')) {
            $catSlug = $request->input('category', $request->input('level'));
            $selectedCategory = $categories->firstWhere('slug', $catSlug);
        }

        $query = Course::with('category')->where('is_published', true);

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory->id);
        }

        if ($request->filled('search')) {
            $term = '%' . $request->string('search')->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)->orWhere('slug', 'like', $term)->orWhere('description', 'like', $term);
            });
        }

        // If a specific category or search is active, paginate courses for that selection
        if ($selectedCategory || $request->filled('search')) {
            $courses = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
            $groupedCourses = null;
        } else {
            // "every program must have its own category like the IGCSE or others not all to be displayed randomly"
            // Group all published courses under their respective category
            $allCourses = $query->orderBy('category_id')->orderBy('id', 'desc')->get();
            $groupedCourses = $allCourses->groupBy(function ($course) {
                return $course->category?->name ?? 'Other Programs';
            });
            $courses = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        }

        return view('courses.index', compact('courses', 'categories', 'groupedCourses', 'selectedCategory'));
    }

    /**
     * Course details landing and syllabus overview.
     */
    public function show(string $slug): View
    {
        $query = Course::with(['category', 'sections.modules'])
            ->where('slug', $slug);

        // Regular users/guests can only view published courses. Admins & instructors can preview unpublished materials.
        $user = Auth::user();
        $canPreview = $user && (
            in_array($user->primaryRole(), ['SUPER_ADMIN', 'MANAGER', 'CORP_MANAGER', 'COURSE_CREATOR'], true)
            || (int) $user->id === (int) Course::where('slug', $slug)->value('instructor_id')
        );

        if (!$canPreview) {
            $query->where('is_published', true);
        }

        $course = $query->firstOrFail();

        $isEnrolled = false;
        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
                ->exists();
        }

        return view('courses.show', compact('course', 'isEnrolled'));
    }

    /**
     * Process course enrollment or multi-currency order.
     */
    public function enroll(Course $course, Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check already enrolled
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing && $existing->status === Enrollment::STATUS_ACTIVE) {
            return redirect()->route('learning.player', ['course' => $course->slug])
                ->with('info', 'You are already enrolled in this course.');
        }

        $now = Carbon::now();
        $expiresAt = $course->recertification_days !== null && $course->recertification_days > 0
            ? $now->copy()->addDays((int) $course->recertification_days)
            : null;

        // If paid course, simulate payment order
        if ($course->price_cents > 0) {
            $gateway = $request->input('payment_gateway', Order::GATEWAY_STRIPE);

            Order::create([
                'tenant_id' => $course->tenant_id,
                'user_id' => $user->id,
                'total_amount_cents' => $course->price_cents,
                'currency' => $course->currency,
                'payment_gateway' => $gateway,
                'payment_id' => 'ch_' . uniqid('', true),
                'status' => Order::STATUS_COMPLETED,
            ]);
        }

        Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'status' => Enrollment::STATUS_ACTIVE,
                'enrolled_at' => $now,
                'expires_at' => $expiresAt,
            ]
        );

        return redirect()->route('learning.player', ['course' => $course->slug])
            ->with('success', 'Enrollment completed successfully! You have full access to all curriculum modules.');
    }

    /**
     * Instant live search API returning matched courses as JSON.
     */
    public function searchApi(Request $request): \Illuminate\Http\JsonResponse
    {
        $term = trim($request->string('q')->toString());
        if (mb_strlen($term) < 1) {
            return response()->json(['results' => []]);
        }

        $courses = Course::with('category')
            ->where('is_published', true)
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('slug', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhereHas('category', function ($cq) use ($term) {
                      $cq->where('name', 'like', "%{$term}%");
                  });
            })
            ->limit(8)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'url' => route('courses.show', $c->slug),
                    'category' => $c->category?->name ?? 'Program',
                    'audience' => $c->target_audience,
                    'price' => $c->formattedPrice(),
                    'recertification_days' => $c->recertification_days,
                ];
            });

        return response()->json(['results' => $courses]);
    }
}
