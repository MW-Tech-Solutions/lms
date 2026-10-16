<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\EventReservationApprovedMail;
use App\Mail\ExamNotificationSubscribedMail;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\EventReservation;
use App\Models\Faq;
use App\Models\LiveEvent;
use App\Models\NewsletterSubscriber;
use App\Models\PastPaperAttempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewEnhancementsTest extends TestCase
{
    private User $admin;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@cambridge.local'],
            [
                'name' => 'Admin QA',
                'password' => bcrypt('password123'),
                'user_type' => 'EMPLOYEE',
                'approval_status' => 'APPROVED',
            ]
        );

        $adminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN'], ['is_system' => true]);
        RoleAssignment::firstOrCreate([
            'user_id' => $this->admin->id,
            'role_id' => $adminRole->id,
            'context_id' => 1,
        ]);

        $this->student = User::firstOrCreate(
            ['email' => 'student_qa@cambridge.local'],
            [
                'name' => 'Student QA',
                'password' => bcrypt('password123'),
                'user_type' => 'STUDENT',
                'approval_status' => 'APPROVED',
            ]
        );
    }

    public function test_homepage_renders_category_cards_and_removes_admin_updated_text(): void
    {
        // Seed an FAQ
        Faq::create([
            'question' => 'How does the past paper grading work?',
            'answer' => 'Our system provides automated rubrics and step-by-step mark schemes.',
            'category' => 'ACADEMIC',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify categories section
        $response->assertSee('Cambridge Primary Checkpoint');
        $response->assertSee('Past Paper Packs');

        // Verify "(Admin Updated" is completely gone
        $response->assertDontSee('(Admin Updated');
    }

    public function test_teachers_admin_page_supports_pagination_and_status_filtering(): void
    {
        $uniquePending = 'teacher_pending_' . uniqid() . '@cambridge.local';
        $uniqueApproved = 'teacher_approved_' . uniqid() . '@cambridge.local';

        // Create teachers with different statuses
        $teacherPending = User::create([
            'name' => 'Teacher Pending Test',
            'email' => $uniquePending,
            'password' => bcrypt('password'),
            'user_type' => 'EMPLOYEE',
            'approval_status' => 'PENDING',
        ]);

        $teacherRole = Role::firstOrCreate(['name' => 'TEACHER'], ['is_system' => true]);
        RoleAssignment::firstOrCreate([
            'user_id' => $teacherPending->id,
            'role_id' => $teacherRole->id,
            'context_id' => 1,
        ]);

        $teacherApproved = User::create([
            'name' => 'Teacher Approved Test',
            'email' => $uniqueApproved,
            'password' => bcrypt('password'),
            'user_type' => 'EMPLOYEE',
            'approval_status' => 'APPROVED',
        ]);
        RoleAssignment::firstOrCreate([
            'user_id' => $teacherApproved->id,
            'role_id' => $teacherRole->id,
            'context_id' => 1,
        ]);

        // Filter by PENDING
        $response = $this->actingAs($this->admin)->get(route('admin.teachers.index', ['status' => 'PENDING']));
        $response->assertStatus(200);
        $response->assertSee('Teacher Pending Test');

        // Status update PATCH
        $patchResponse = $this->actingAs($this->admin)->patch(route('admin.teachers.status', $teacherPending->id), [
            'approval_status' => 'APPROVED',
        ]);
        $patchResponse->assertRedirect();
        $this->assertEquals('APPROVED', $teacherPending->fresh()->approval_status);
    }

    public function test_live_events_management_and_spot_reservation_with_email(): void
    {
        Mail::fake();

        $event = LiveEvent::create([
            'title' => 'Masterclass on Mechanics 9709',
            'slug' => 'masterclass-mechanics-' . uniqid(),
            'speaker_name' => 'Dr. Aris Thorne',
            'speaker_role' => 'Principal Cambridge Examiner',
            'event_date' => now()->addDays(5),
            'start_time' => '17:00:00',
            'end_time' => '18:30:00',
            'capacity' => 50,
            'is_active' => true,
            'fee_cents' => 0,
        ]);

        // Reserve a spot
        $reserveResponse = $this->post(route('events.reserve', $event->id), [
            'name' => 'Cambridge Applicant',
            'email' => 'applicant_' . uniqid() . '@test.com',
            'phone' => '+44 7700 900077',
            'notes' => 'Looking forward to the mechanics session',
        ]);

        $reserveResponse->assertRedirect();

        $reservation = EventReservation::where('event_id', $event->id)->firstOrFail();

        // Admin approves reservation
        $approveResponse = $this->actingAs($this->admin)->patch(route('admin.events.reservations.status', $reservation->id), [
            'status' => 'CONFIRMED',
        ]);
        $approveResponse->assertRedirect();
        $this->assertEquals('CONFIRMED', $reservation->fresh()->status);

        // Verify confirmation email dispatched
        Mail::assertSent(EventReservationApprovedMail::class, function ($mail) use ($reservation) {
            return $mail->hasTo($reservation->email);
        });
    }

    public function test_faq_admin_crud(): void
    {
        $faqResponse = $this->actingAs($this->admin)->post(route('admin.faqs.store'), [
            'question' => 'Are mark schemes updated each season?',
            'answer' => 'Yes, specimen and series papers are updated after each Cambridge release cycle.',
            'category' => 'ACADEMIC',
            'sort_order' => 10,
            'is_published' => true,
        ]);

        $faqResponse->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'question' => 'Are mark schemes updated each season?',
        ]);
    }

    public function test_esther_scores_storage_history_and_printable_report(): void
    {
        $this->actingAs($this->student);

        // Post score
        $response = $this->post(route('scores.store'), [
            'level' => 'igcse',
            'exam_year' => 2024,
            'season' => 'M/J',
            'variant' => 2,
            'component_name' => 'Extended Mathematics (0580) Paper 4',
            'score_obtained' => 110,
            'max_score' => 130,
            'notes' => 'Strong performance on calculus and vectors',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('past_paper_attempts', [
            'user_id' => $this->student->id,
            'score_obtained' => 110,
            'max_score' => 130,
        ]);

        // Check history page
        $historyResponse = $this->get(route('scores.history'));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('Extended Mathematics (0580) Paper 4');

        // Check printable diagnostic report
        $reportResponse = $this->get(route('scores.report'));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('Cambridge Academic Mastery Report');
        $reportResponse->assertSee('110 / 130');
    }

    public function test_subscriptions_admin_management_in_gbp(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Cambridge Premium Test Plan',
            'slug' => 'cambridge-premium-' . uniqid(),
            'price_pence' => 9900,
            'duration_term' => '12_MONTHS',
            'currency' => 'GBP',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.subscriptions.index'));
        $response->assertStatus(200);
        $response->assertSee('£99.00');
        $response->assertSee('Cambridge Premium Test Plan');
    }

    public function test_exam_notification_newsletter_subscription_with_email(): void
    {
        Mail::fake();

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'cambridge_candidate@example.org',
            'level_interest' => 'Cambridge IGCSE & A-Level',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'cambridge_candidate@example.org',
            'level_interest' => 'Cambridge IGCSE & A-Level',
        ]);

        Mail::assertSent(ExamNotificationSubscribedMail::class, function ($mail) {
            return $mail->hasTo('cambridge_candidate@example.org');
        });
    }

    public function test_level_admin_moderation_approves_and_rejects_materials(): void
    {
        $category = CourseCategory::firstOrCreate(
            ['slug' => 'cambridge-a-level-test'],
            ['name' => 'Cambridge A-Level Test']
        );

        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Advanced Mechanics 9709 P4',
            'slug' => 'advanced-mechanics-9709-p4-' . uniqid(),
            'description' => 'Test course material',
            'target_audience' => 'PUBLIC',
            'format' => 'SELF_PACED',
            'passing_grade' => 75,
            'price_cents' => 4500,
            'currency' => 'GBP',
            'is_published' => false,
        ]);

        // Level admin view
        $adminView = $this->actingAs($this->admin)->get(route('admin.levels.index', ['level_id' => $category->id]));
        $adminView->assertStatus(200);
        $adminView->assertSee('Advanced Mechanics 9709 P4');

        // Approve course
        $approve = $this->actingAs($this->admin)->patch(route('admin.levels.materials.approve', $course->id));
        $approve->assertRedirect();
        $this->assertTrue($course->fresh()->is_published);

        // Reject course
        $reject = $this->actingAs($this->admin)->patch(route('admin.levels.materials.reject', $course->id));
        $reject->assertRedirect();
        $this->assertFalse($course->fresh()->is_published);
    }

    public function test_freemium_content_visibility_for_unpaid_users(): void
    {
        $category = CourseCategory::firstOrCreate(
            ['slug' => 'cambridge-igcse-test'],
            ['name' => 'Cambridge IGCSE Test']
        );

        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'IGCSE Extended Mathematics 0580',
            'slug' => 'igcse-extended-math-0580-' . uniqid(),
            'description' => 'Comprehensive IGCSE program',
            'public_preview_content' => 'Free Teaser: Previewing Question 1 on Factorising Polynomials.',
            'target_audience' => 'PUBLIC',
            'format' => 'SELF_PACED',
            'passing_grade' => 70,
            'price_cents' => 3500,
            'currency' => 'GBP',
            'is_published' => true,
        ]);

        // Public visitor view
        $response = $this->get(route('courses.show', $course->slug));
        $response->assertStatus(200);

        // Preview content must be visible
        $response->assertSee('Free Public Preview');
        $response->assertSee('Free Teaser: Previewing Question 1 on Factorising Polynomials.');
        $response->assertSee('Locked Behind Cambridge Membership');
    }

    public function test_teacher_course_scoping_and_admin_deletion_approval(): void
    {
        $teacher = User::firstOrCreate(
            ['email' => 'teacher_qa@cambridge.local'],
            [
                'name' => 'Dr. Cambridge Teacher',
                'password' => bcrypt('password123'),
                'user_type' => 'EMPLOYEE',
                'approval_status' => 'APPROVED',
            ]
        );
        $teacher->update(['approval_status' => 'APPROVED']);
        $teacherRole = Role::firstOrCreate(['name' => 'TEACHER'], ['is_system' => true]);
        RoleAssignment::firstOrCreate([
            'user_id' => $teacher->id,
            'role_id' => $teacherRole->id,
            'context_id' => 1,
        ]);

        $category = CourseCategory::firstOrCreate(
            ['slug' => 'cambridge-o-level-test'],
            ['name' => 'Cambridge O Level Test']
        );

        \App\Models\TeacherLevelAssignment::firstOrCreate([
            'user_id' => $teacher->id,
            'category_id' => $category->id,
        ]);

        // 1. Teacher creates course under Cambridge level
        $createResponse = $this->actingAs($teacher)->post(route('admin.courses.store'), [
            'category_id' => $category->id,
            'title' => 'O Level Pure Chemistry 5070',
            'slug' => 'o-level-pure-chem-5070-' . uniqid(),
            'description' => 'Organic and Physical Chemistry Notes',
            'target_audience' => 'PUBLIC',
            'format' => 'SELF_PACED',
            'passing_grade' => 70,
            'price_cents' => 3000,
            'currency' => 'GBP',
            'is_published' => 1,
            'material_notes' => 'Unit 1: Stoichiometry and the Mole Concept. Full worked calculations.',
        ]);
        $createResponse->assertRedirect(route('admin.courses.index'));

        $course = Course::where('title', 'O Level Pure Chemistry 5070')->first();
        $this->assertNotNull($course);
        $this->assertEquals($teacher->id, $course->instructor_id);
        $this->assertEquals('Unit 1: Stoichiometry and the Mole Concept. Full worked calculations.', $course->material_notes);

        // 2. Teacher views courses index - sees his course
        $teacherIndex = $this->actingAs($teacher)->get(route('admin.courses.index'));
        $teacherIndex->assertStatus(200);
        $teacherIndex->assertSee('O Level Pure Chemistry 5070');
        $teacherIndex->assertSee('My Courses &amp; Materials', false);

        // 3. Teacher cannot directly delete approved course without admin approval
        $directDelete = $this->actingAs($teacher)->delete(route('admin.courses.destroy', $course->id));
        $directDelete->assertRedirect();
        $this->assertNotNull(Course::find($course->id)); // still exists

        // 4. Teacher requests deletion with reason
        $requestDelete = $this->actingAs($teacher)->post(route('admin.courses.request-delete', $course->id), [
            'reason' => 'Syllabus updated for 2027 edition.',
        ]);
        $requestDelete->assertRedirect();
        $course->refresh();
        $this->assertEquals(Course::DELETION_REQUESTED, $course->deletion_status);
        $this->assertEquals('Syllabus updated for 2027 edition.', $course->deletion_reason);

        // 5. Admin approves deletion request
        $approveDelete = $this->actingAs($this->admin)->post(route('admin.courses.approve-delete', $course->id));
        $approveDelete->assertRedirect();
        $this->assertNull(Course::find($course->id)); // purged

        // 6. Strict RBAC: Teacher cannot access admin settings or users
        $teacherSettings = $this->actingAs($teacher)->get('/admin/settings');
        $teacherSettings->assertStatus(403);

        $teacherUsers = $this->actingAs($teacher)->get('/admin/users');
        $teacherUsers->assertStatus(403);
    }

    public function test_system_currency_setting_and_parent_student_relationship_flow(): void
    {
        // 1. Admin updates system currency to EUR
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'settings' => [
                'system_currency' => 'EUR',
            ],
        ]);
        $response->assertRedirect();

        $settings = app(\App\Services\SiteConfigService::class);
        $this->assertEquals('EUR', $settings->get('system_currency'));

        // 2. Student dashboard displays Family & Parent Connection
        $student = User::factory()->create(['user_type' => 'EMPLOYEE']);
        $studentDash = $this->actingAs($student)->get(route('my-learning'));
        $studentDash->assertStatus(200);
        $studentDash->assertSee('Family &amp; Guardian Connection', false);

        // 3. Parent views dashboard with live student status
        $parent = User::factory()->create(['user_type' => 'EMPLOYEE']);
        \App\Models\ParentStudent::create([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'Parent/Guardian',
        ]);

        $parentDash = $this->actingAs($parent)->get(route('parent.dashboard'));
        $parentDash->assertStatus(200);
        $parentDash->assertSee($student->name);
        $parentDash->assertSee('Mastered');

        // 4. Clean up test parent link
        \App\Models\ParentStudent::where('parent_id', $parent->id)->delete();
    }

    public function test_quiz_question_bank_scoping_and_random_pooling(): void
    {
        $teacher = User::factory()->create([
            'user_type' => 'EMPLOYEE',
            'approval_status' => 'APPROVED',
        ]);
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['description' => 'Instructor']);
        RoleAssignment::create([
            'user_id' => $teacher->id,
            'role_id' => $teacherRole->id,
            'context_id' => 1,
        ]);

        $category = CourseCategory::firstOrCreate(['name' => 'Cambridge IGCSE Test'], ['slug' => 'cambridge-igcse-test']);

        $teacherCourse = Course::create([
            'category_id' => $category->id,
            'instructor_id' => $teacher->id,
            'title' => 'IGCSE Physics 0625 by Teacher',
            'slug' => 'igcse-physics-0625-teacher-' . uniqid(),
            'description' => 'Comprehensive syllabus',
            'is_published' => true,
        ]);

        // 1. Question Bank Index
        $bankResponse = $this->actingAs($teacher)->get(route('admin.quizzes.questions.index'));
        $bankResponse->assertStatus(200);
        $bankResponse->assertSee('Question Bank Repository');
        $bankResponse->assertSee('IGCSE Physics 0625 by Teacher');

        $qText = 'What is terminal velocity of a falling parachute? ' . uniqid();
        $chapterName = 'Chapter 1: Kinematics ' . uniqid();
        $createQResponse = $this->actingAs($teacher)->post(route('admin.quizzes.questions.store'), [
            'course_id' => $teacherCourse->id,
            'chapter' => $chapterName,
            'type' => 'SINGLE_CHOICE',
            'question_text' => $qText,
            'points' => 2,
            'options' => [
                'Constant speed when drag equals weight',
                'Accelerating speed at 9.8 m/s²',
                'Zero speed at maximum altitude',
                'Infinite velocity',
            ],
            'correct_option' => 0,
        ]);
        $createQResponse->assertRedirect();

        $question = Question::where('question_text', $qText)->first();
        $this->assertNotNull($question);
        $this->assertEquals($teacherCourse->id, $question->course_id);
        $this->assertEquals($chapterName, $question->chapter);
        $this->assertEquals(4, $question->options()->count());
        $this->assertTrue($question->options()->where('is_correct', true)->first()->option_text === 'Constant speed when drag equals weight');

        // 3. Download CSV template
        $templateResponse = $this->actingAs($teacher)->get(route('admin.quizzes.questions.template'));
        $templateResponse->assertStatus(200);
        $templateResponse->assertHeader('Content-Disposition', 'attachment; filename="question_bank_template.csv"');

        // 4. Create Quiz with Random Pooling from Teacher Course
        $quizTitle = 'Kinematics Speed Assessment ' . uniqid();
        $quizResponse = $this->actingAs($teacher)->post(route('admin.quizzes.store'), [
            'title' => $quizTitle,
            'course_id' => $teacherCourse->id,
            'time_limit_minutes' => 25,
            'max_attempts' => 2,
            'passing_percentage' => 75,
            'question_mode' => 'random',
            'fetch_count' => 1,
            'fetch_chapter' => $chapterName,
            'blur_detection' => 1,
            'block_copy_paste' => 1,
            'webcam_snapshots' => 1,
        ]);
        $quizResponse->assertRedirect();

        $quiz = Quiz::where('title', $quizTitle)->first();
        $this->assertNotNull($quiz);
        $this->assertEquals(1, $quiz->questions()->count());
        $this->assertEquals($question->id, $quiz->questions()->first()->id);

        // 5. Course Code check
        $this->assertStringContainsString('T' . $teacher->id, $teacherCourse->course_code);
    }

    public function test_public_mentors_directory_and_detail_page_by_level(): void
    {
        $teacher = User::factory()->create([
            'name' => 'Prof. Richard Feynman',
            'approval_status' => 'APPROVED',
            'user_type' => 'EMPLOYEE',
        ]);
        $profile = \App\Models\UserProfile::create([
            'user_id' => $teacher->id,
            'job_title' => 'Cambridge Quantum Mechanics Lead',
            'bio' => 'World-renowned physics lecturer and curriculum developer.',
        ]);

        $category = CourseCategory::firstOrCreate(['name' => 'Cambridge A Level Test'], ['slug' => 'cambridge-a-level-test']);
        $course = Course::create([
            'category_id' => $category->id,
            'instructor_id' => $teacher->id,
            'title' => 'Advanced Wave Particle Duality',
            'slug' => 'advanced-wave-particle-' . uniqid(),
            'is_published' => true,
        ]);

        // 1. Mentors public directory
        $mentorsIndex = $this->get(route('mentors.index'));
        $mentorsIndex->assertStatus(200);
        $mentorsIndex->assertSee('Meet Our Mentors &amp; Examiners', false);
        $mentorsIndex->assertSee('Prof. Richard Feynman');

        // 2. Mentor detail page with courses grouped by level
        $mentorShow = $this->get(route('mentors.show', $teacher->id));
        $mentorShow->assertStatus(200);
        $mentorShow->assertSee('Prof. Richard Feynman');
        $mentorShow->assertSee('Cambridge Quantum Mechanics Lead');
        $mentorShow->assertSee('Cambridge A Level Test');
        $mentorShow->assertSee('Advanced Wave Particle Duality');
        $mentorShow->assertSee($course->course_code);
    }
}

