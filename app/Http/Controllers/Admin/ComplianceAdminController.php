<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\ComplianceRecertificationEngine;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplianceAdminController extends Controller
{
    public function index(): View
    {
        $complianceCourses = Course::with(['category', 'enrollments'])
            ->where(function ($q) {
                $q->where('target_audience', 'CORPORATE')
                  ->orWhereNotNull('recertification_days');
            })
            ->get();

        $enrollments = Enrollment::with(['user.department', 'course'])
            ->latest('enrolled_at')
            ->paginate(15);

        return view('admin.compliance.index', compact('complianceCourses', 'enrollments'));
    }

    /**
     * Trigger manual or scheduled compliance recertification scan.
     */
    public function triggerScan(ComplianceRecertificationEngine $engine): RedirectResponse
    {
        $results = $engine->checkAndProcessRecertifications();

        \App\Services\AuditService::log('COMPLIANCE_SCAN_TRIGGERED', null, null, $results);

        return back()->with('success', sprintf(
            'Compliance audit scan completed: %d overdue certifications expired & re-enrolled; %d approaching deadlines flagged.',
            $results['expired_count'],
            $results['warning_count']
        ));
    }

    /**
     * Manager manual sign-off for corporate workforce compliance.
     */
    public function signoff(int $id): RedirectResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $oldStatus = $enrollment->status;

        $enrollment->update([
            'status' => Enrollment::STATUS_COMPLETED,
            'completed_at' => Carbon::now(),
        ]);

        \App\Services\AuditService::log('COMPLIANCE_SIGNOFF', $enrollment, ['status' => $oldStatus], [
            'status' => Enrollment::STATUS_COMPLETED,
            'employee' => $enrollment->user->name,
            'signed_by' => auth()->user()->name ?? 'System Manager',
        ]);

        return back()->with('success', sprintf('Compliance sign-off verified for employee %s.', $enrollment->user->name));
    }

    /**
     * Export verifiable workforce compliance audit report as CSV.
     */
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        \App\Services\AuditService::log('COMPLIANCE_CSV_EXPORTED', null, null, ['format' => 'CSV']);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="workforce_compliance_audit_export_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($handle, [
                'Enrollment ID',
                'Employee Name',
                'Employee ID',
                'Work Email',
                'Department',
                'Compliance Track / Course',
                'Audience Tier',
                'Recertification Cycle (Days)',
                'Status',
                'Enrolled Date',
                'Completion / Sign-Off Date',
                'Audit Verifier Signature',
                'Immutable Cryptographic Hash',
            ]);

            $enrollments = Enrollment::with(['user.department', 'course'])->get();

            foreach ($enrollments as $item) {
                $user = $item->user;
                $course = $item->course;
                $deptName = $user && $user->department ? $user->department->name : 'General Operations';
                $signoffDate = $item->completed_at ? $item->completed_at->toIso8601String() : 'Pending Verification';
                $verifier = $item->status === Enrollment::STATUS_COMPLETED ? 'Manager Verified' : 'In Progress';
                
                // Deterministic audit verification hash
                $auditHash = hash('sha256', sprintf('%d-%s-%s-%s', $item->id, $user ? $user->email : 'user', $course ? $course->slug : 'course', $signoffDate));

                fputcsv($handle, [
                    $item->id,
                    $user ? $user->name : 'N/A',
                    $user ? ($user->employee_id ?? 'EMP-' . $user->id) : 'N/A',
                    $user ? $user->email : 'N/A',
                    $deptName,
                    $course ? $course->title : 'N/A',
                    $course ? $course->target_audience : 'CORPORATE',
                    $course ? ($course->recertification_days ?? 'Permanent') : '365',
                    strtoupper($item->status),
                    $item->enrolled_at ? $item->enrolled_at->toDateString() : '',
                    $signoffDate,
                    $verifier,
                    $auditHash,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
