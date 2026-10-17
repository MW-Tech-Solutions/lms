<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SiteConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolAdminController extends Controller
{
    /**
     * List all institutional schools and their subscription & enrollment metrics.
     */
    public function index(): View
    {
        $schools = Tenant::with(['admin', 'users'])
            ->withCount(['users as students_count' => function ($q) {
                $q->where('user_type', 'STUDENT');
            }])
            ->withCount(['users as teachers_count' => function ($q) {
                $q->whereHas('roleAssignments.role', function ($rq) {
                    $rq->whereIn('name', ['TEACHER', 'INSTRUCTOR', 'TEACHER_NON_EDITING']);
                });
            }])
            ->paginate(15);

        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Store new institutional school subscription.
     */
    public function store(Request $request, SiteConfigService $siteConfig): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tenants,slug'],
            'contact_email' => ['required', 'email'],
            'contact_name' => ['required', 'string', 'max:255'],
            'school_code' => ['nullable', 'string', 'max:64', 'unique:tenants,school_code'],
            'custom_domain' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Check if slug already taken
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(10, 99);
        }

        // Auto-generate Corporate Employee ID if not supplied (e.g. ATC-EMP-22361)
        $schoolCode = !empty($validated['school_code'])
            ? strtoupper(trim($validated['school_code']))
            : $siteConfig->generateCorporateId();

        // Ensure unique school_code
        while (Tenant::where('school_code', $schoolCode)->exists()) {
            $schoolCode = $siteConfig->generateCorporateId();
        }

        // Create or find admin user for this school
        $adminUser = User::where('email', $validated['contact_email'])->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => $validated['contact_name'],
                'email' => $validated['contact_email'],
                'password' => bcrypt('SchoolAdmin#' . rand(1000, 9999)),
                'user_type' => 'EMPLOYEE',
                'employee_id' => $schoolCode,
                'is_active' => true,
                'approval_status' => 'APPROVED',
            ]);

            \App\Models\UserProfile::create([
                'user_id' => $adminUser->id,
                'job_title' => 'School Principal / Institutional Admin',
                'timezone' => 'UTC',
                'locale' => 'en',
            ]);
        }

        // Assign Manager / School Admin CBAC role
        $role = Role::whereIn('name', ['CORP_MANAGER', 'MANAGER', 'SUPER_ADMIN'])->first();
        $context = \App\Models\Context::where('context_level', \App\Models\Context::LEVEL_SYSTEM)->first();
        if ($role && $context && !\App\Models\RoleAssignment::where('user_id', $adminUser->id)->where('role_id', $role->id)->exists()) {
            \App\Models\RoleAssignment::create([
                'user_id' => $adminUser->id,
                'role_id' => $role->id,
                'context_id' => $context->id,
            ]);
        }

        // Create School Tenant
        $school = Tenant::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'subdomain' => $slug,
            'school_code' => $schoolCode,
            'custom_domain' => $validated['custom_domain'] ?? null,
            'admin_user_id' => $adminUser->id,
            'is_active' => true,
            'subscription_status' => 'ACTIVE',
            'branding_json' => [
                'logo_text' => $validated['name'],
                'tagline' => 'Cambridge Partner Institution & Examination Centre',
                'school_code' => $schoolCode,
            ],
        ]);

        // Link admin user to tenant
        $adminUser->update(['tenant_id' => $school->id]);

        return redirect()->route('admin.schools.index')
            ->with('success', "Institutional School '{$school->name}' created successfully with Auto-Generated Code: {$schoolCode}");
    }

    /**
     * Show detailed school admin console.
     */
    public function show(Tenant $school): View
    {
        $school->load(['admin', 'users.profile', 'users.roleAssignments.role']);

        $students = $school->users->where('user_type', 'STUDENT');
        $teachers = $school->users->filter(function ($u) {
            return $u->isTeacher();
        });

        $inviteUrl = url('/register?school_code=' . urlencode($school->school_code ?? ''));
        $portalUrl = url('/schools/' . $school->slug);

        return view('admin.schools.show', compact('school', 'students', 'teachers', 'inviteUrl', 'portalUrl'));
    }

    /**
     * Update school details.
     */
    public function update(Request $request, Tenant $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'unique:tenants,slug,' . $school->id],
            'school_code' => ['required', 'string', 'max:64', 'unique:tenants,school_code,' . $school->id],
            'subscription_status' => ['required', 'in:ACTIVE,PAST_DUE,EXPIRED,TRIAL'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $school->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'school_code' => strtoupper(trim($validated['school_code'])),
            'subscription_status' => $validated['subscription_status'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "School '{$school->name}' updated successfully.");
    }
}
