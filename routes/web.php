<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceAdminController;
use App\Http\Controllers\Admin\CmsAdminController;
use App\Http\Controllers\Admin\ComplianceAdminController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\EventAdminController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\GradebookAdminController;
use App\Http\Controllers\Admin\LevelAdminController;
use App\Http\Controllers\Admin\QuizAdminController;
use App\Http\Controllers\Admin\ScormAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
use App\Http\Controllers\Admin\SubscriptionAdminController;
use App\Http\Controllers\Admin\TeacherAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScoreEntryController;
use App\Http\Controllers\ScormController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public CMS, Catalog & Credential Verification Routes
|--------------------------------------------------------------------------
*/
// Dynamic Favicon Route (Prevents 404s when requested directly by browsers)
Route::get('/favicon.ico', function () {
    $siteConfig = app(\App\Services\SiteConfigService::class);
    $favUrl = $siteConfig->get('brand_favicon_url');
    if (!empty($favUrl)) {
        $parsed = parse_url($favUrl, PHP_URL_PATH);
        $localPath = public_path(ltrim((string) $parsed, '/\\'));
        if (file_exists($localPath) && is_readable($localPath) && filesize($localPath) > 0) {
            $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
            $mime = match($ext) {
                'ico' => 'image/x-icon',
                'svg' => 'image/svg+xml',
                'jpg', 'jpeg' => 'image/jpeg',
                default => 'image/png',
            };
            return response()->file($localPath, ['Content-Type' => $mime, 'Cache-Control' => 'public, max-age=86400']);
        }
    }
    if (file_exists(public_path('favicon.ico')) && filesize(public_path('favicon.ico')) > 0) {
        return response()->file(public_path('favicon.ico'), ['Content-Type' => 'image/x-icon', 'Cache-Control' => 'public, max-age=86400']);
    }
    if (file_exists(public_path('uploads/branding/favicon_32x32.png'))) {
        return response()->file(public_path('uploads/branding/favicon_32x32.png'), ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400']);
    }
    if (file_exists(public_path('images/site_logo.png'))) {
        return response()->file(public_path('images/site_logo.png'), ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400']);
    }
    abort(404);
});

Route::get('/', [CmsController::class, 'show'])->name('home');
Route::get('/p/{slug}', [CmsController::class, 'show'])->name('cms.show');
Route::post('/cms/form-submit', [CmsController::class, 'handleFormSubmit'])->name('cms.form-submit');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/api/courses/search', [CourseController::class, 'searchApi'])->name('courses.search.api');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/corporate-compliance', function () {
    $complianceTracks = Course::where('target_audience', 'CORPORATE')
        ->orWhereNotNull('recertification_days')
        ->take(6)
        ->get();
    return view('corporate.compliance', compact('complianceTracks'));
})->name('corporate.compliance');

Route::get('/verify/{uuid}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::get('/verify/{uuid}/pdf', [CertificateController::class, 'pdf'])->name('certificates.pdf');
Route::get('/verify/{uuid}/view', [CertificateController::class, 'viewCertificate'])->name('certificates.view');

// Public About & Contact Pages
Route::get('/about', [\App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/contact', [\App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\PageController::class, 'submitContact'])->name('contact.submit');

// Live Cambridge Events & Seat Reservations (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::post('/events/{event}/reserve', [EventController::class, 'reserve'])->name('events.reserve');

// Cambridge Real-time Score Entry & Printable Diagnostics
Route::post('/cambridge/scores/store', [ScoreEntryController::class, 'store'])->name('scores.store');
Route::get('/cambridge/scores/history', [ScoreEntryController::class, 'history'])->name('scores.history');
Route::get('/cambridge/scores/report', [ScoreEntryController::class, 'generateReport'])->name('scores.report');

// Faculty & Mentors Directory (Public)
Route::get('/mentors', [MentorController::class, 'index'])->name('mentors.index');
Route::get('/mentors/{id}', [MentorController::class, 'show'])->name('mentors.show');

// Exam Notification & Newsletter Subscriptions
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Dedicated Public/Institutional School Portal Route (e.g. /schools/mundramodelschool)
Route::get('/schools/{slug}', [\App\Http\Controllers\SchoolController::class, 'show'])->name('schools.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Two-Factor Authentication Challenge
    Route::get('/auth/2fa-challenge', [\App\Http\Controllers\TwoFactorAuthController::class, 'showChallenge'])->name('auth.2fa.challenge');
    Route::post('/auth/2fa-challenge', [\App\Http\Controllers\TwoFactorAuthController::class, 'verifyChallenge'])->name('auth.2fa.verify');
    Route::post('/auth/2fa-resend', [\App\Http\Controllers\TwoFactorAuthController::class, 'resendEmailOtp'])->name('auth.2fa.resend');
});
Route::get('/profile/security', [\App\Http\Controllers\TwoFactorAuthController::class, 'showSettings'])->middleware('auth')->name('profile.security');
Route::post('/profile/2fa', [\App\Http\Controllers\TwoFactorAuthController::class, 'updateSettings'])->middleware('auth')->name('profile.2fa.update');
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Learner & Workforce Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/my-learning', [LearningController::class, 'myLearning'])->name('my-learning');
    Route::get('/my-certificates', [CertificateController::class, 'myCertificates'])->name('certificates.my');
    Route::get('/learn/{course}', [LearningController::class, 'player'])->name('learning.player');
    Route::post('/learn/module-complete', [LearningController::class, 'markModuleComplete'])->name('learning.module-complete');
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('enrollment.store');
    Route::get('/courses/{course}/enroll/success', [CourseController::class, 'enrollSuccess'])->name('courses.enroll.success');
    Route::post('/assignments/submit', [LearningController::class, 'submitAssignment'])->name('assignments.submit');

    Route::get('/quiz/{quiz}/take', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    Route::get('/attendance/{course_id}', [AttendanceController::class, 'showRollingQr'])->name('attendance.qr');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');

    // Esther Math Past-Paper Revision Tracker Matrix
    Route::get('/tracker/{courseSlug?}', [\App\Http\Controllers\EstherTrackerController::class, 'show'])->name('tracker.show');
    Route::post('/api/tracker/record', [\App\Http\Controllers\EstherTrackerController::class, 'recordScore'])->name('tracker.record');

    // 3-State Learning Reflections & 90% Media Progress
    Route::post('/api/reflections', [\App\Http\Controllers\ReflectionController::class, 'store'])->name('reflections.store');

    // Subscriptions & Assessment Level Passes (£)
    Route::get('/p/pricing', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('pricing');
    Route::post('/subscription/checkout/{plan}', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('subscription.success');

    // Parent & Guardian Portal
    Route::get('/parent/dashboard', [\App\Http\Controllers\ParentPortalController::class, 'index'])->name('parent.dashboard');
    Route::post('/parent/invite/generate', [\App\Http\Controllers\ParentPortalController::class, 'generateInvite'])->name('parent.invite.generate');
    Route::post('/parent/link-student', [\App\Http\Controllers\ParentPortalController::class, 'linkStudent'])->name('parent.link.student');
    Route::get('/parent/child/{student}/progress', [\App\Http\Controllers\ParentPortalController::class, 'viewChildProgress'])->name('parent.child.progress');

    // Cambridge Resource Request Form
    Route::post('/resource-request', [\App\Http\Controllers\ResourceRequestController::class, 'store'])->name('resource-request.store');
});

/*
|--------------------------------------------------------------------------
| SCORM Runtime & Proctoring API Endpoints
|--------------------------------------------------------------------------
*/
Route::get('/scorm/launch/{package_id}', [ScormController::class, 'launch'])->name('scorm.launch');
Route::get('/api/scorm/init/{package_id}', [ScormController::class, 'initialize'])->name('api.scorm.init');
Route::post('/api/scorm/commit/{package_id}', [ScormController::class, 'commit'])->name('api.scorm.commit');

Route::post('/api/quiz/autosave', [QuizController::class, 'autosave'])->name('api.quiz.autosave');
Route::post('/api/anti-cheat/log', [QuizController::class, 'logAntiCheat'])->name('api.anti-cheat.log');

/*
|--------------------------------------------------------------------------
| Management & Administration Portal (CBAC Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:SUPER_ADMIN,MANAGER,CORP_MANAGER,COURSE_CREATOR,TEACHER,INSTRUCTOR,TEACHER_NON_EDITING,TRAINER'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Faculty & Course Operations
    Route::middleware('role:SUPER_ADMIN,MANAGER,COURSE_CREATOR,TEACHER,INSTRUCTOR')->group(function () {
        Route::get('/courses', [CourseAdminController::class, 'index'])->name('courses.index');
        Route::post('/courses', [CourseAdminController::class, 'store'])->name('courses.store');
        Route::put('/courses/{course}', [CourseAdminController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseAdminController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/request-delete', [CourseAdminController::class, 'requestDelete'])->name('courses.request-delete');
    });

    // Super Admin Course Moderation, Deletion & Pricing Approvals
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::post('/courses/{course}/approve', [CourseAdminController::class, 'approve'])->name('courses.approve');
        Route::post('/courses/{course}/reject', [CourseAdminController::class, 'reject'])->name('courses.reject');
        Route::post('/courses/{course}/approve-delete', [CourseAdminController::class, 'approveDelete'])->name('courses.approve-delete');
        Route::post('/courses/{course}/reject-delete', [CourseAdminController::class, 'rejectDelete'])->name('courses.reject-delete');
        Route::post('/courses/{course}/approve-pricing', [CourseAdminController::class, 'approvePricing'])->name('courses.approve-pricing');
        Route::post('/courses/{course}/reject-pricing', [CourseAdminController::class, 'rejectPricing'])->name('courses.reject-pricing');
    });

    // Assessments & Question Banks
    Route::middleware('role:SUPER_ADMIN,MANAGER,COURSE_CREATOR,TEACHER,INSTRUCTOR,TEACHER_NON_EDITING,TRAINER')->group(function () {
        Route::get('/quizzes', [QuizAdminController::class, 'index'])->name('quizzes.index');
        Route::post('/quizzes', [QuizAdminController::class, 'store'])->name('quizzes.store');
        Route::put('/quizzes/{quiz}', [QuizAdminController::class, 'update'])->name('quizzes.update');
        Route::post('/quizzes/{quiz}/toggle', [QuizAdminController::class, 'toggleStatus'])->name('quizzes.toggle');
        Route::get('/quizzes/questions', [QuizAdminController::class, 'questionsIndex'])->name('quizzes.questions.index');
        Route::post('/quizzes/questions', [QuizAdminController::class, 'questionStore'])->name('quizzes.questions.store');
        Route::delete('/quizzes/questions/{question}', [QuizAdminController::class, 'questionDestroy'])->name('quizzes.questions.destroy');
        Route::get('/quizzes/questions/template', [QuizAdminController::class, 'downloadQuestionTemplate'])->name('quizzes.questions.template');
        Route::post('/quizzes/questions/upload', [QuizAdminController::class, 'uploadQuestions'])->name('quizzes.questions.upload');

        Route::get('/gradebook', [GradebookAdminController::class, 'index'])->name('gradebook.index');
        Route::post('/gradebook/override', [GradebookAdminController::class, 'override'])->name('gradebook.override');

        Route::get('/attendance', [AttendanceAdminController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceAdminController::class, 'store'])->name('attendance.store');
    });

    // Super Administrator & Manager Exclusive Consoles
    Route::middleware('role:SUPER_ADMIN,MANAGER,CORP_MANAGER')->group(function () {
        Route::get('/compliance', [ComplianceAdminController::class, 'index'])->name('compliance.index');
        Route::get('/compliance/export-csv', [ComplianceAdminController::class, 'exportCsv'])->name('compliance.export-csv');
        Route::post('/compliance/trigger-scan', [ComplianceAdminController::class, 'triggerScan'])->name('compliance.trigger-scan');
        Route::post('/compliance/signoff/{id}', [ComplianceAdminController::class, 'signoff'])->name('compliance.signoff');

        Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
        Route::post('/users', [UserAdminController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserAdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserAdminController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-archive', [UserAdminController::class, 'toggleArchive'])->name('users.toggle-archive');
        Route::post('/users/assign-role', [UserAdminController::class, 'assignRole'])->name('users.assign-role');
        Route::post('/users/remove-role', [UserAdminController::class, 'removeRole'])->name('users.remove-role');
        Route::post('/users/roles', [UserAdminController::class, 'storeRole'])->name('users.store-role');

        // Institutional Schools & Cohort Hub
        Route::get('/schools', [\App\Http\Controllers\Admin\SchoolAdminController::class, 'index'])->name('schools.index');
        Route::post('/schools', [\App\Http\Controllers\Admin\SchoolAdminController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}', [\App\Http\Controllers\Admin\SchoolAdminController::class, 'show'])->name('schools.show');
        Route::put('/schools/{school}', [\App\Http\Controllers\Admin\SchoolAdminController::class, 'update'])->name('schools.update');
    });

    // Faculty Level Requests (Teacher Submits)
    Route::post('/teachers/request-level', [TeacherAdminController::class, 'requestLevel'])->name('teachers.request-level');

    // Root Site Administrator Exclusive Consoles
    Route::middleware('role:SUPER_ADMIN')->group(function () {
        Route::get('/scorm', [ScormAdminController::class, 'index'])->name('scorm.index');
        Route::post('/scorm/upload', [ScormAdminController::class, 'upload'])->name('scorm.upload');

        Route::get('/cms', [CmsAdminController::class, 'index'])->name('cms.index');
        Route::post('/cms/sections', [CmsAdminController::class, 'storeSection'])->name('cms.sections.store');
        Route::put('/cms/sections/{section}', [CmsAdminController::class, 'updateSection'])->name('cms.sections.update');
        Route::delete('/cms/sections/{section}', [CmsAdminController::class, 'destroySection'])->name('cms.sections.destroy');
        Route::post('/cms/seed-samples', [CmsAdminController::class, 'seedSamples'])->name('cms.seed-samples');

        Route::get('/settings', [SettingsAdminController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsAdminController::class, 'update'])->name('settings.update');

        Route::get('/teachers', [TeacherAdminController::class, 'index'])->name('teachers.index');
        Route::post('/teachers', [TeacherAdminController::class, 'store'])->name('teachers.store');
        Route::patch('/teachers/{user}/status', [TeacherAdminController::class, 'updateStatus'])->name('teachers.status');
        Route::post('/teachers/level-requests/{levelRequest}/approve', [TeacherAdminController::class, 'approveLevelRequest'])->name('teachers.approve-level-request');
        Route::post('/teachers/level-requests/{levelRequest}/reject', [TeacherAdminController::class, 'rejectLevelRequest'])->name('teachers.reject-level-request');

        Route::get('/events', [EventAdminController::class, 'index'])->name('events.index');
        Route::post('/events', [EventAdminController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventAdminController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventAdminController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/toggle', [EventAdminController::class, 'toggleActive'])->name('events.toggle');
        Route::patch('/events/reservations/{reservation}/status', [EventAdminController::class, 'updateReservationStatus'])->name('events.reservations.status');

        Route::get('/faqs', [FaqAdminController::class, 'index'])->name('faqs.index');
        Route::post('/faqs', [FaqAdminController::class, 'store'])->name('faqs.store');
        Route::put('/faqs/{faq}', [FaqAdminController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [FaqAdminController::class, 'destroy'])->name('faqs.destroy');

        Route::get('/subscriptions', [SubscriptionAdminController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions', [SubscriptionAdminController::class, 'store'])->name('subscriptions.store');
        Route::put('/subscriptions/{subscriptionPlan}', [SubscriptionAdminController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscriptionPlan}', [SubscriptionAdminController::class, 'destroy'])->name('subscriptions.destroy');

        Route::get('/levels', [LevelAdminController::class, 'index'])->name('levels.index');
        Route::post('/levels', [LevelAdminController::class, 'storeLevel'])->name('levels.store');
        Route::put('/levels/{level}', [LevelAdminController::class, 'updateLevel'])->name('levels.update');
        Route::delete('/levels/{level}', [LevelAdminController::class, 'destroyLevel'])->name('levels.destroy');
        Route::patch('/levels/materials/{course}/approve', [LevelAdminController::class, 'approveMaterial'])->name('levels.materials.approve');
        Route::patch('/levels/materials/{course}/reject', [LevelAdminController::class, 'rejectMaterial'])->name('levels.materials.reject');

        // Contact Messages & Inquiry Chat-style Responses
        Route::get('/contact-messages', [\App\Http\Controllers\Admin\ContactAdminController::class, 'index'])->name('contact.index');
        Route::post('/contact-messages/{contactMessage}/reply', [\App\Http\Controllers\Admin\ContactAdminController::class, 'reply'])->name('contact.reply');
        Route::patch('/contact-messages/{contactMessage}/status', [\App\Http\Controllers\Admin\ContactAdminController::class, 'updateStatus'])->name('contact.status');
        Route::delete('/contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactAdminController::class, 'destroy'])->name('contact.destroy');
    });
});
