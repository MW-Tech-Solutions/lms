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

    public function test_course_create_edit_and_thumbnail_flow(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();
        $this->assertNotNull($admin);

        $testSlug = 'k8s-sec-' . uniqid();
        $updatedSlug = $testSlug . '-updated';

        // 1. Create a new course with thumbnail URL
        $createResponse = $this->actingAs($admin)->post('/admin/courses', [
            'title' => 'Kubernetes Security Mastery',
            'slug' => $testSlug,
            'description' => 'Comprehensive enterprise Kubernetes hardening guide.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9',
            'target_audience' => 'CORPORATE',
            'format' => 'SELF_PACED',
            'passing_grade' => 80,
            'recertification_days' => 365,
            'price_cents' => 9900,
            'currency' => 'USD',
            'is_published' => 1,
        ]);
        $createResponse->assertSessionHasNoErrors();
        $createResponse->assertRedirect();

        $course = \App\Models\Course::where('slug', $testSlug)->first();
        $this->assertNotNull($course);
        $this->assertEquals('https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9', $course->thumbnail);
        $this->assertEquals('https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9', $course->thumbnailUrl());

        // 2. Edit existing course and update thumbnail
        $updateResponse = $this->actingAs($admin)->put('/admin/courses/' . $course->id, [
            'title' => 'Kubernetes Security Mastery (Updated)',
            'slug' => $updatedSlug,
            'description' => 'Updated syllabus description.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b',
            'target_audience' => 'BOTH',
            'format' => 'TOPICS',
            'passing_grade' => 85,
            'recertification_days' => 180,
            'price_cents' => 14900,
            'currency' => 'USD',
            'is_published' => 1,
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $updateResponse->assertRedirect();

        $course->refresh();
        $this->assertEquals('Kubernetes Security Mastery (Updated)', $course->title);
        $this->assertEquals('https://images.unsplash.com/photo-1550751827-4bd374c3f58b', $course->thumbnail);
        $this->assertEquals(85, $course->passing_grade);
    }

    public function test_password_reset_flow_complete(): void
    {
        $user = User::where('email', 'student@enterpriselms.com')->first();
        $this->assertNotNull($user);

        // 1. Visit forgot password page
        $forgotPage = $this->get('/forgot-password');
        $forgotPage->assertStatus(200);
        $forgotPage->assertSee('Reset Password');
        $forgotPage->assertSee('Send Recovery Link');

        // 2. Submit forgot password form
        $submitEmail = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);
        $submitEmail->assertSessionHas('status');
        $submitEmail->assertSessionHas('dev_reset_url');

        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();
        $this->assertNotNull($tokenRecord);

        // 3. Generate raw token and update token record for verification
        $rawToken = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update([
                'token' => \Illuminate\Support\Facades\Hash::make($rawToken),
                'created_at' => now(),
            ]);

        // 4. View reset password page
        $resetPage = $this->get('/reset-password/' . $rawToken . '?email=' . urlencode($user->email));
        $resetPage->assertStatus(200);
        $resetPage->assertSee('Set New Password');
        $resetPage->assertSee('Update Password');

        // 5. Submit new password
        $resetResponse = $this->post('/reset-password', [
            'token' => $rawToken,
            'email' => $user->email,
            'password' => 'NewSecretPass999!',
            'password_confirmation' => 'NewSecretPass999!',
        ]);
        $resetResponse->assertRedirect('/login');
        $resetResponse->assertSessionHas('status');

        // Verify password was updated
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewSecretPass999!', $user->password));

        // Verify token was cleaned up
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_dynamic_homepage_sections_and_cms_admin_flow(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();
        $this->assertNotNull($admin);

        // 1. Seed sample sections
        $seedResponse = $this->actingAs($admin)->post('/admin/cms/seed-samples');
        $seedResponse->assertRedirect();
        $seedResponse->assertSessionHas('success');

        // 2. Check homepage renders all 4 screenshot sample sections
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Explore Categories');
        $homeResponse->assertSee('Web Development');
        $homeResponse->assertSee('Meet Our Mentors');
        $homeResponse->assertSee('Darren Treadway');
        $homeResponse->assertSee('Upcoming Live Events');
        $homeResponse->assertSee("Web3 Builder's Meetup");
        $homeResponse->assertSee('Frequently Asked Questions');
        $homeResponse->assertSee('Still Need Help?');

        // 3. Check Admin CMS manager index page
        $adminCmsResponse = $this->actingAs($admin)->get('/admin/cms');
        $adminCmsResponse->assertStatus(200);
        $adminCmsResponse->assertSee('Homepage Dynamic Section Stack');
        $adminCmsResponse->assertSee('CATEGORIES');
        $adminCmsResponse->assertSee('MENTORS');
        $adminCmsResponse->assertSee('EVENTS');
        $adminCmsResponse->assertSee('FAQ');

        // 4. Update a section via admin
        $faqSection = \App\Models\CmsSection::where('section_type', 'FAQ')->first();
        $this->assertNotNull($faqSection);

        $updateSectionResponse = $this->actingAs($admin)->put('/admin/cms/sections/' . $faqSection->id, [
            'order_index' => 6,
            'title' => 'Frequently Asked Questions (Admin Updated)',
            'subtitle' => 'Custom updated subtitle for enterprise learners.',
            'support_title' => 'Need 24/7 Enterprise Assistance?',
            'support_email' => 'support@enterpriselms.com',
        ]);
        $updateSectionResponse->assertRedirect();
        $updateSectionResponse->assertSessionHas('success');

        $faqSection->refresh();
        $this->assertEquals('Frequently Asked Questions (Admin Updated)', $faqSection->content_json['title']);
        $this->assertEquals('support@enterpriselms.com', $faqSection->content_json['support_email']);
    }
}
