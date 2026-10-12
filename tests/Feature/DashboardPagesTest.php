<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardPagesTest extends TestCase
{
    public function test_all_admin_and_user_dashboard_pages_render_successfully(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();
        $this->assertNotNull($admin);

        $adminRoutes = [
            '/admin',
            '/admin/courses',
            '/admin/compliance',
            '/admin/users',
            '/admin/scorm',
            '/admin/gradebook',
            '/admin/quizzes',
            '/admin/attendance',
            '/admin/cms',
            '/admin/settings',
            '/my-learning',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_all_public_landing_pages_render_successfully(): void
    {
        $publicRoutes = [
            '/',
            '/courses',
            '/corporate-compliance',
            '/p/pricing',
            '/p/terms',
            '/p/privacy',
            '/p/support',
            '/login',
            '/register',
        ];

        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_student_and_instructor_authentication_flow(): void
    {
        // 1. Instructor login routes to admin dashboard
        $instructorResponse = $this->post('/login', [
            'email' => 'instructor@enterpriselms.com',
            'password' => 'password123',
        ]);
        $instructorResponse->assertRedirect(route('admin.dashboard'));

        // Reset auth session for next user test
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 2. Student login routes to learner dashboard
        $studentResponse = $this->post('/login', [
            'email' => 'student@enterpriselms.com',
            'password' => 'password123',
        ]);
        $studentResponse->assertRedirect(route('my-learning'));
    }

    public function test_course_show_page_renders_with_multibyte_polyfill(): void
    {
        $course = \App\Models\Course::first();
        if ($course) {
            $response = $this->get('/courses/' . $course->slug);
            $response->assertStatus(200);
            $response->assertSee(e(\Illuminate\Support\Str::limit($course->title, 40)));
        }
    }
}
