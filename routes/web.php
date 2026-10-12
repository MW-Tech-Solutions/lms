<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceAdminController;
use App\Http\Controllers\Admin\CmsAdminController;
use App\Http\Controllers\Admin\ComplianceAdminController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\GradebookAdminController;
use App\Http\Controllers\Admin\QuizAdminController;
use App\Http\Controllers\Admin\ScormAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScormController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public CMS, Catalog & Credential Verification Routes
|--------------------------------------------------------------------------
*/
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
});
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
    Route::post('/assignments/submit', [LearningController::class, 'submitAssignment'])->name('assignments.submit');

    Route::get('/quiz/{quiz}/take', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    Route::get('/attendance/{course_id}', [AttendanceController::class, 'showRollingQr'])->name('attendance.qr');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
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
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/compliance', [ComplianceAdminController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/export-csv', [ComplianceAdminController::class, 'exportCsv'])->name('compliance.export-csv');
    Route::post('/compliance/trigger-scan', [ComplianceAdminController::class, 'triggerScan'])->name('compliance.trigger-scan');
    Route::post('/compliance/signoff/{id}', [ComplianceAdminController::class, 'signoff'])->name('compliance.signoff');

    Route::get('/courses', [CourseAdminController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseAdminController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseAdminController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseAdminController::class, 'destroy'])->name('courses.destroy');

    Route::get('/scorm', [ScormAdminController::class, 'index'])->name('scorm.index');
    Route::post('/scorm/upload', [ScormAdminController::class, 'upload'])->name('scorm.upload');

    Route::get('/quizzes', [QuizAdminController::class, 'index'])->name('quizzes.index');
    Route::post('/quizzes', [QuizAdminController::class, 'store'])->name('quizzes.store');
    Route::put('/quizzes/{quiz}', [QuizAdminController::class, 'update'])->name('quizzes.update');
    Route::post('/quizzes/{quiz}/toggle', [QuizAdminController::class, 'toggleStatus'])->name('quizzes.toggle');

    Route::get('/gradebook', [GradebookAdminController::class, 'index'])->name('gradebook.index');
    Route::post('/gradebook/override', [GradebookAdminController::class, 'override'])->name('gradebook.override');

    Route::get('/attendance', [AttendanceAdminController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceAdminController::class, 'store'])->name('attendance.store');

    Route::get('/cms', [CmsAdminController::class, 'index'])->name('cms.index');

    Route::get('/settings', [SettingsAdminController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsAdminController::class, 'update'])->name('settings.update');

    Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
    Route::post('/users', [UserAdminController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserAdminController::class, 'update'])->name('users.update');
    Route::post('/users/assign-role', [UserAdminController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/remove-role', [UserAdminController::class, 'removeRole'])->name('users.remove-role');
    Route::post('/users/roles', [UserAdminController::class, 'storeRole'])->name('users.store-role');
});
