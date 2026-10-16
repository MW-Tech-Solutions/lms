<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CambridgeExamSeries;
use App\Models\CambridgePaperComponent;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\LearningReflection;
use App\Models\ParentInvite;
use App\Models\ParentStudent;
use App\Models\PastPaperAttempt;
use App\Models\ResourceRequest;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Tests\TestCase;

class CambridgeFeaturesTest extends TestCase
{
    protected User $student;
    protected User $parent;
    protected Course $course;
    protected CourseCategory $aLevelCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::firstOrCreate(
            ['email' => 'student.cambridge@test.com'],
            ['name' => 'Alice Cambridge', 'password' => bcrypt('password123')]
        );

        $this->parent = User::firstOrCreate(
            ['email' => 'parent.cambridge@test.com'],
            ['name' => 'Guardian Cambridge', 'password' => bcrypt('password123')]
        );

        $this->aLevelCategory = CourseCategory::where('slug', 'a-level')->firstOrFail();
        $this->course = Course::where('category_id', $this->aLevelCategory->id)->firstOrFail();
    }

    /**
     * Section 8: Esther Math Past-Paper Tracker Matrix Scoring & Thresholds
     */
     public function test_esther_tracker_matrix_renders_and_computes_dynamic_thresholds(): void
     {
         $component = CambridgePaperComponent::firstOrFail();

         // 1. Submit high score -> Green threshold (>= 75%)
         $greenResponse = $this->actingAs($this->student)->postJson('/api/tracker/record', [
             'course_id' => $this->course->id,
             'year' => 2025,
             'season' => 'O/N',
             'component_id' => $component->id,
             'score_obtained' => 60,
             'max_score' => 75,
             'variant' => 2,
         ]);

         $greenResponse->assertStatus(200);
         $greenResponse->assertJson([
             'success' => true,
             'percentage' => 80.0,
             'status_color' => PastPaperAttempt::STATUS_GREEN,
             'attempt' => [
                 'percentage' => 80.0,
                 'status_color' => PastPaperAttempt::STATUS_GREEN,
             ]
         ]);

         // 2. Retake submission with Amber score (50% - 74.99%) -> Preserves history and increments attempt_number
         $amberResponse = $this->actingAs($this->student)->postJson('/api/tracker/record', [
             'course_id' => $this->course->id,
             'year' => 2025,
             'season' => 'O/N',
             'component_id' => $component->id,
             'score_obtained' => 45,
             'max_score' => 75,
             'variant' => 2,
         ]);

         $amberResponse->assertStatus(200);
         $amberResponse->assertJson([
             'success' => true,
             'percentage' => 60.0,
             'status_color' => PastPaperAttempt::STATUS_AMBER,
             'attempt' => [
                 'percentage' => 60.0,
                 'status_color' => PastPaperAttempt::STATUS_AMBER,
             ]
         ]);

         // 3. Red score (< 50%)
         $redResponse = $this->actingAs($this->student)->postJson('/api/tracker/record', [
             'course_id' => $this->course->id,
             'year' => 2025,
             'season' => 'O/N',
             'component_id' => $component->id,
             'score_obtained' => 30,
             'max_score' => 75,
             'variant' => 2,
         ]);

         $redResponse->assertStatus(200);
         $redResponse->assertJson([
             'success' => true,
             'percentage' => 40.0,
             'status_color' => PastPaperAttempt::STATUS_RED,
             'attempt' => [
                 'percentage' => 40.0,
                 'status_color' => PastPaperAttempt::STATUS_RED,
             ]
         ]);

         // 4. Tracker UI page renders student historical attempts and matrix
         $trackerPage = $this->actingAs($this->student)->get('/tracker/' . $this->course->slug);
         $trackerPage->assertStatus(200);
         $trackerPage->assertSee('ESTHER MATH MATRIX');
         $trackerPage->assertSee('Pure Mathematics 1');
     }

     /**
      * Section 8: Tracker validation rejects invalid / negative scores
      */
     public function test_tracker_rejects_negative_or_exceeded_scores(): void
     {
         $component = CambridgePaperComponent::firstOrFail();

         $invalidResponse = $this->actingAs($this->student)->postJson('/api/tracker/record', [
             'course_id' => $this->course->id,
             'year' => 2024,
             'season' => 'M/J',
             'component_id' => $component->id,
             'score_obtained' => 95,
             'max_score' => 75, // score > max
         ]);

         $invalidResponse->assertStatus(422);
     }

     /**
      * Section 7: 3-State Learning Reflections and 90% Media watch time rule
      */
     public function test_learning_reflections_enforce_media_watch_threshold(): void
     {
         // Reset previous ledger for clean assertion
         \Illuminate\Support\Facades\DB::table('user_xp_ledger')
             ->where('user_id', $this->student->id)
             ->delete();

         // 1. Rejects reflection if media progress is under 90%
         $rejectResponse = $this->actingAs($this->student)->postJson('/api/reflections', [
             'course_id' => $this->course->id,
             'reflection_choice' => LearningReflection::CHOICE_UNDERSTOOD,
             'resource_type' => 'video',
             'watch_percentage' => 75.5,
         ]);

         $rejectResponse->assertStatus(422);
         $rejectResponse->assertJson(['success' => false]);

         // 2. Accepts reflection when watch time >= 90% and awards 50 XP
         $acceptResponse = $this->actingAs($this->student)->postJson('/api/reflections', [
             'course_id' => $this->course->id,
             'reflection_choice' => LearningReflection::CHOICE_UNDERSTOOD,
             'resource_type' => 'video',
             'watch_percentage' => 95.0,
         ]);

         $acceptResponse->assertStatus(200);
         $acceptResponse->assertJson([
             'success' => true,
             'reflection' => [
                 'reflection_choice' => LearningReflection::CHOICE_UNDERSTOOD,
             ],
             'xp_awarded' => 50,
         ]);

         // 3. Duplicate submission updates reflection and awards 0 duplicate XP (anti-exploit)
         $duplicateResponse = $this->actingAs($this->student)->postJson('/api/reflections', [
             'course_id' => $this->course->id,
             'reflection_choice' => LearningReflection::CHOICE_NEED_RETURN,
             'resource_type' => 'video',
             'watch_percentage' => 95.0,
         ]);

         $duplicateResponse->assertStatus(200);
         $duplicateResponse->assertJson([
             'success' => true,
             'xp_awarded' => 0,
         ]);
     }

     /**
      * Section 11: Parent-Child Link, Alphanumeric 48-Hour Invite Codes
      */
     public function test_parent_invite_code_generation_and_student_linking(): void
     {
         // 1. Student generates 6-character CMP-XXXXXX code
         $inviteResponse = $this->actingAs($this->student)->postJson('/parent/invite/generate');
         $inviteResponse->assertStatus(200);
         $inviteResponse->assertJsonStructure([
             'success',
             'invite_code',
             'expires_at',
         ]);

         $inviteCode = $inviteResponse->json('invite_code');
         $this->assertStringStartsWith('CMP-', $inviteCode);
         $this->assertEquals(10, strlen($inviteCode)); // CMP- + 6 alphanumeric = 10 chars

         // 2. Parent claims invite code
         $linkResponse = $this->actingAs($this->parent)->post('/parent/link-student', [
             'invite_code' => $inviteCode,
         ]);
         $linkResponse->assertRedirect(route('parent.dashboard'));
         $linkResponse->assertSessionHas('success');

         // Verify database relationship
         $this->assertDatabaseHas('parent_students', [
             'parent_id' => $this->parent->id,
             'student_id' => $this->student->id,
         ]);

         // 3. Parent views child progress dashboard
         $childDashboard = $this->actingAs($this->parent)->get('/parent/child/' . $this->student->id . '/progress');
         $childDashboard->assertStatus(200);
         $childDashboard->assertSee($this->student->name);
         $childDashboard->assertSee('Esther Math Past-Paper Attempts');
     }

     /**
      * Section 10: Subscriptions & Assessment Level Passes in GBP (£)
     */
     public function test_cambridge_subscription_plans_in_gbp_and_checkout(): void
     {
         // 1. Verify pricing page renders 5 levels in GBP
         $pricingResponse = $this->get('/p/pricing');
         $pricingResponse->assertStatus(200);
         $pricingResponse->assertSee('Choose Your Cambridge Assessment Level Pass');
         $pricingResponse->assertSee('£');

         // 2. Student checks out 3-Month Plan for A Level
         $aLevelPlan = SubscriptionPlan::where('category_id', $this->aLevelCategory->id)
             ->where('duration_term', '3_MONTHS')
             ->firstOrFail();

         $checkoutResponse = $this->actingAs($this->student)->post('/subscription/checkout/' . $aLevelPlan->id);
         $checkoutResponse->assertRedirect(route('courses.index', ['category' => $this->aLevelCategory->slug]));
         $checkoutResponse->assertSessionHas('success');

         // 3. Entitlement confirmed in database
         $this->assertDatabaseHas('user_subscriptions', [
             'user_id' => $this->student->id,
             'plan_id' => $aLevelPlan->id,
             'category_id' => $this->aLevelCategory->id,
             'status' => UserSubscription::STATUS_ACTIVE,
         ]);
     }

     /**
      * Section 6: Cambridge Resource Request Form Submission
      */
     public function test_cambridge_resource_request_workflow(): void
     {
         $response = $this->actingAs($this->student)->post('/resource-request', [
             'level_name' => 'Cambridge International A Level (Year 13)',
             'subject' => 'Mathematics (9709)',
             'topic' => 'Pure Mathematics 1 - Integration and Trigonometry',
             'resource_type' => 'Topical Past Paper Pack',
             'details' => 'Please upload worked solutions for Paper 1 Pure Mathematics 2024 Oct/Nov.',
         ]);

         $response->assertRedirect();
         $response->assertSessionHas('success');

         $this->assertDatabaseHas('resource_requests', [
             'user_id' => $this->student->id,
             'subject' => 'Mathematics (9709)',
             'status' => ResourceRequest::STATUS_PENDING,
         ]);
     }
}
