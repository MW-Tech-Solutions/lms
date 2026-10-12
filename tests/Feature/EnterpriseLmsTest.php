<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\ScormPackage;
use App\Models\User;
use App\Services\ComplianceRecertificationEngine;
use App\Services\GradebookCalculationService;
use App\Services\PermissionService;
use Tests\TestCase;

class EnterpriseLmsTest extends TestCase
{
    public function test_homepage_renders_dynamic_cms_components_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Next-Gen Workforce Compliance');
    }

    public function test_course_catalog_and_details_page_render(): void
    {
        $response = $this->get('/courses');
        $response->assertStatus(200);

        $course = Course::first();
        $detailResponse = $this->get('/courses/' . $course->slug);
        $detailResponse->assertStatus(200);
    }

    public function test_cbac_super_admin_bypasses_all_contexts(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();
        $this->assertNotNull($admin);

        /** @var PermissionService $permService */
        $permService = app(PermissionService::class);
        $this->assertTrue($permService->hasPermission($admin, 'settings:edit'));
        $this->assertTrue($permService->hasPermission($admin, 'course:create'));
    }

    public function test_compliance_recertification_engine_executes_cleanly(): void
    {
        /** @var ComplianceRecertificationEngine $engine */
        $engine = app(ComplianceRecertificationEngine::class);
        $results = $engine->checkAndProcessRecertifications();

        $this->assertIsArray($results);
        $this->assertArrayHasKey('expired_count', $results);
        $this->assertArrayHasKey('warning_count', $results);
    }

    public function test_gradebook_weighted_calculation_service(): void
    {
        $employee = User::where('email', 'employee@acmeglobal.com')->first();
        $course = Course::where('slug', 'iso-27001-recertification-2026')->first();

        $this->assertNotNull($employee);
        $this->assertNotNull($course);

        /** @var GradebookCalculationService $calc */
        $calc = app(GradebookCalculationService::class);
        $result = $calc->calculateCourseGrade($employee, $course);

        $this->assertGreaterThanOrEqual(80.0, $result['final_percentage']);
        $this->assertTrue($result['is_passing']);
        $this->assertContains($result['letter_grade'], ['A', 'A-']);
    }

    public function test_scorm_cmi_commit_persists_telemetry(): void
    {
        $employee = User::where('email', 'employee@acmeglobal.com')->first();
        $package = ScormPackage::first();

        $response = $this->actingAs($employee)->postJson("/api/scorm/commit/{$package->id}", [
            'cmi.core.lesson_status' => 'passed',
            'cmi.core.score.raw' => '96',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'passed', 'score' => '96']);
    }

    public function test_attendance_rolling_qr_verifies_within_geofence(): void
    {
        $employee = User::where('email', 'employee@acmeglobal.com')->first();
        $session = AttendanceSession::first();

        $slot = (int) floor(time() / 15);
        $validToken = $session->qr_secret . '_' . $slot;

        $response = $this->actingAs($employee)->postJson('/attendance/checkin', [
            'session_id' => $session->id,
            'token' => $validToken,
            'lat' => $session->lat_boundary,
            'lng' => $session->lng_boundary,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_dynamic_branding_and_color_template_selection_persists_and_audits(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'color_template_preset' => 'emerald',
            'settings' => [
                'brand_name' => 'Acme Global Academy',
                'brand_logo_url' => 'https://example.com/logo.svg',
                'brand_favicon_url' => 'https://example.com/favicon.ico',
            ],
        ]);

        $response->assertSessionHas('success');

        /** @var \App\Services\SiteConfigService $config */
        $config = app(\App\Services\SiteConfigService::class);
        $this->assertEquals('Acme Global Academy', $config->get('brand_name'));
        $this->assertEquals('emerald', $config->get('color_template_preset'));
        $this->assertEquals('#059669', $config->get('brand_accent_color'));
        $this->assertEquals('https://example.com/favicon.ico', $config->get('brand_favicon_url'));

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'SETTINGS_UPDATE',
        ]);
    }

    public function test_compliance_auditor_csv_export_streams_proper_headers(): void
    {
        $admin = User::where('email', 'admin@enterpriselms.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.compliance.export-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('workforce_compliance_audit_export', (string) $response->headers->get('Content-Disposition'));

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'COMPLIANCE_CSV_EXPORTED',
        ]);
    }
}
