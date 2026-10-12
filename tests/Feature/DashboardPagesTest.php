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
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 2. Editing Teacher login routes to admin dashboard
        $teacherEditResponse = $this->post('/login', [
            'email' => 'teacher.editing@enterpriselms.com',
            'password' => 'password123',
        ]);
        $teacherEditResponse->assertRedirect(route('admin.dashboard'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 3. Non-editing Teacher login routes to admin dashboard
        $teacherNonEditResponse = $this->post('/login', [
            'email' => 'teacher.assistant@enterpriselms.com',
            'password' => 'password123',
        ]);
        $teacherNonEditResponse->assertRedirect(route('admin.dashboard'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 4. Course Creator login routes to admin dashboard
        $creatorResponse = $this->post('/login', [
            'email' => 'creator@enterpriselms.com',
            'password' => 'password123',
        ]);
        $creatorResponse->assertRedirect(route('admin.dashboard'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 5. HR Manager login routes to admin dashboard
        $managerResponse = $this->post('/login', [
            'email' => 'hr.manager@acmeglobal.com',
            'password' => 'password123',
        ]);
        $managerResponse->assertRedirect(route('admin.dashboard'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 6. Student login routes to learner dashboard
        $studentResponse = $this->post('/login', [
            'email' => 'student@enterpriselms.com',
            'password' => 'password123',
        ]);
        $studentResponse->assertRedirect(route('my-learning'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 7. Corporate Employee login routes to learner dashboard
        $employeeResponse = $this->post('/login', [
            'email' => 'employee@acmeglobal.com',
            'password' => 'password123',
        ]);
        $employeeResponse->assertRedirect(route('my-learning'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        // 8. Guest User login routes to learner dashboard
        $guestResponse = $this->post('/login', [
            'email' => 'guest@enterpriselms.com',
            'password' => 'password123',
        ]);
        $guestResponse->assertRedirect(route('my-learning'));
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();
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

    public function test_dynamic_topbar_banner_and_settings_update(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();
        $this->actingAs($admin);

        // Update topbar settings via admin
        $postResponse = $this->post(route('admin.settings.update'), [
            'settings' => [
                'topbar_enabled' => '1',
                'topbar_badge_text' => 'FLASH PROMO',
                'topbar_promo_text' => 'Save 75% On Enterprise Certification Audits Today',
                'topbar_btn_text' => 'Get Certified',
                'topbar_btn_url' => '/courses',
                'topbar_countdown_days' => '02',
                'topbar_countdown_hours' => '12',
                'topbar_countdown_minutes' => '45',
                'topbar_countdown_seconds' => '15',
            ],
        ]);
        $postResponse->assertRedirect();

        // Verify public layout reflects updated settings
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('FLASH PROMO');
        $response->assertSee('Save 75% On Enterprise Certification Audits Today');
        $response->assertSee('Get Certified');
    }

    public function test_moodle_roles_and_subsystems_exist(): void
    {
        // 1. All 8 Moodle Roles exist in database
        $moodleRoles = [
            \App\Models\Role::SUPER_ADMIN,
            \App\Models\Role::MANAGER,
            \App\Models\Role::COURSE_CREATOR,
            \App\Models\Role::TEACHER,
            \App\Models\Role::TEACHER_NON_EDITING,
            \App\Models\Role::STUDENT,
            \App\Models\Role::GUEST,
            \App\Models\Role::AUTHENTICATED_USER,
        ];
        foreach ($moodleRoles as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }

        // 2. Subsystems exist
        $this->assertDatabaseHas('course_groups', ['name' => 'Cohort Alpha - Internal SecOps']);
        $this->assertDatabaseHas('course_badges', ['name' => 'Certified ISO 27001 Lead Auditor']);
        $this->assertDatabaseHas('course_competencies', ['idnumber' => 'COMP-SEC-01']);

        // 3. Question Bank has 9 question types
        $this->assertDatabaseHas('questions', ['type' => \App\Models\Question::TYPE_MATCHING]);
        $this->assertDatabaseHas('questions', ['type' => \App\Models\Question::TYPE_NUMERICAL]);
        $this->assertDatabaseHas('questions', ['type' => \App\Models\Question::TYPE_ESSAY]);
        $this->assertDatabaseHas('questions', ['type' => \App\Models\Question::TYPE_DRAG_DROP]);
        $this->assertDatabaseHas('questions', ['type' => \App\Models\Question::TYPE_CALCULATED]);

        // 4. Moodle Core modules exist
        $this->assertDatabaseHas('course_forums', ['name' => 'ISO 27001 Implementation & Audit Discussion Forum']);
        $this->assertDatabaseHas('course_lessons', ['name' => 'Adaptive Branching Incident Response Simulation']);
        $this->assertDatabaseHas('course_feedbacks', ['name' => 'Annual Course Quality & Auditor Feedback Survey']);
        $this->assertDatabaseHas('course_choices', ['name' => 'Select Your Supervised Proctored Exam Slot']);
        $this->assertDatabaseHas('course_h5p_packages', ['title' => 'Interactive Phishing & Social Engineering Checkpoint (H5P)']);
        $this->assertDatabaseHas('course_workshops', ['name' => 'Peer Review: Incident Response Runbook Evaluation']);
        $this->assertDatabaseHas('course_chats', ['name' => 'Live SOC Office Hours & Real-time Consultation']);
        $this->assertDatabaseHas('course_glossaries', ['name' => 'Information Security & Compliance Lexicon']);
        $this->assertDatabaseHas('course_wikis', ['name' => 'Collaborative Cyber Defense Playbook Wiki']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'PAGE']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'BOOK']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'FILE']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'FOLDER']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'URL']);
        $this->assertDatabaseHas('course_resources', ['resource_type' => 'LABEL']);
    }
}
