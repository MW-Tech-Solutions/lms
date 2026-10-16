<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AttendanceSession;
use App\Models\Certificate;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\Context;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModule;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\GradeCategory;
use App\Models\GradeGrade;
use App\Models\GradeItem;
use App\Models\Permission;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\ScormPackage;
use App\Models\SiteSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\CourseGroup;
use App\Models\CourseBadge;
use App\Models\CourseCompetency;
use App\Models\CourseForum;
use App\Models\ForumDiscussion;
use App\Models\ForumPost;
use App\Models\CourseLesson;
use App\Models\LessonPage;
use App\Models\CourseFeedback;
use App\Models\FeedbackItem;
use App\Models\CourseChoice;
use App\Models\CourseH5p;
use App\Models\CourseWorkshop;
use App\Models\CourseChat;
use App\Models\CourseGlossary;
use App\Models\GlossaryEntry;
use App\Models\CourseWiki;
use App\Models\WikiPage;
use App\Models\CourseResource;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent duplicate execution if database is already seeded
        if (Tenant::where('subdomain', 'acme')->exists()) {
            $this->command?->info('Demonstration tenant and seed records already exist. Skipping duplicate seed.');
            return;
        }

        // =========================================================================
        // 1. TENANT CREATION
        // =========================================================================
        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'name' => 'Acme Global Enterprise',
            'subdomain' => 'acme',
            'custom_domain' => 'lms.acmeglobal.com',
            'branding_json' => [
                'primary_color' => '#0F172A',
                'accent_color' => '#2563EB',
                'logo_text' => 'ACME ENTERPRISE ACADEMY',
            ],
            'is_active' => true,
        ]);

        // =========================================================================
        // 2. ORGANIZATIONAL & CAMBRIDGE ACADEMIC DEPARTMENTS
        // =========================================================================
        $deptSec = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cybersecurity & Information Governance',
        ]);
        $deptHR = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Human Resources & Workforce Compliance',
        ]);
        $deptCloud = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cloud Infrastructure & DevOps',
        ]);

        // 5 Canonical Cambridge Academic Syllabus Levels
        $deptPrimary = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cambridge Primary Checkpoint (Years 1–6)',
        ]);
        $deptLowerSec = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cambridge Lower Secondary (Years 7–9)',
        ]);
        $deptIgcse = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cambridge IGCSE (Years 10–11)',
        ]);
        $deptAsLevel = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cambridge AS Level (Year 12)',
        ]);
        $deptALevel = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cambridge A Level (Year 13)',
        ]);

        // =========================================================================
        // 3. 5-TIER CONTEXT HIERARCHY (CBAC)
        // System -> Tenant -> Department -> Category -> Course -> Module
        // =========================================================================
        $ctxSystem = Context::create([
            'context_level' => Context::LEVEL_SYSTEM,
            'instance_id' => '0',
            'path' => '1',
        ]);

        $ctxTenant = Context::create([
            'context_level' => Context::LEVEL_TENANT,
            'instance_id' => (string) $tenant->id,
            'path' => '1/2',
        ]);

        $ctxDept = Context::create([
            'context_level' => Context::LEVEL_DEPARTMENT,
            'instance_id' => (string) $deptHR->id,
            'path' => '1/2/' . $deptHR->id,
        ]);

        $catCompliance = CourseCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'Corporate Regulatory Governance',
            'slug' => 'corporate-regulatory-governance',
        ]);

        $ctxCategory = Context::create([
            'context_level' => Context::LEVEL_CATEGORY,
            'instance_id' => (string) $catCompliance->id,
            'path' => '1/2/' . $deptHR->id . '/' . $catCompliance->id,
        ]);

        // =========================================================================
        // 4. ROLE & PERMISSION MATRIX (MOODLE CANONICAL CBAC HIERARCHY)
        // =========================================================================
        $roleSuper = Role::firstOrCreate(['name' => Role::SUPER_ADMIN], ['is_system' => true]);
        $roleManager = Role::firstOrCreate(['name' => Role::MANAGER], ['is_system' => true]);
        $roleCreator = Role::firstOrCreate(['name' => Role::COURSE_CREATOR], ['is_system' => true]);
        $roleTeacher = Role::firstOrCreate(['name' => Role::TEACHER], ['is_system' => true]);
        $roleTeacherNonEdit = Role::firstOrCreate(['name' => Role::TEACHER_NON_EDITING], ['is_system' => true]);
        $roleStudent = Role::firstOrCreate(['name' => Role::STUDENT], ['is_system' => true]);
        $roleGuest = Role::firstOrCreate(['name' => Role::GUEST], ['is_system' => true]);
        $roleAuthUser = Role::firstOrCreate(['name' => Role::AUTHENTICATED_USER], ['is_system' => true]);

        // Legacy / Alias Enterprise Roles
        $roleCorpManager = Role::firstOrCreate(['name' => Role::CORP_MANAGER], ['is_system' => true]);
        $roleInstructor = Role::firstOrCreate(['name' => Role::INSTRUCTOR], ['is_system' => true]);
        $roleTrainer = Role::firstOrCreate(['name' => Role::TRAINER], ['is_system' => true]);
        $roleEmp = Role::firstOrCreate(['name' => Role::LEARNER_EMPLOYEE], ['is_system' => true]);
        $roleLearnerStudent = Role::firstOrCreate(['name' => Role::LEARNER_STUDENT], ['is_system' => true]);

        $permissions = [
            'course:create', 'course:publish', 'course:view', 'course:edit', 'course:delete',
            'quiz:attempt', 'quiz:proctor', 'quiz:grade', 'quiz:create',
            'assignment:submit', 'assignment:grade', 'assignment:blind_grade',
            'grade:modify', 'grade:view', 'grade:calculate',
            'compliance:signoff', 'compliance:audit', 'audit:export',
            'scorm:upload', 'scorm:track',
            'attendance:record', 'attendance:override', 'attendance:self_mark',
            'forum:post', 'forum:moderate', 'forum:grade',
            'lesson:author', 'lesson:attempt',
            'feedback:respond', 'feedback:view_reports',
            'choice:vote', 'choice:manage',
            'h5p:author', 'h5p:attempt',
            'workshop:submit', 'workshop:assess', 'workshop:grade',
            'chat:participate', 'chat:moderate',
            'glossary:view', 'glossary:contribute',
            'wiki:view', 'wiki:edit',
            'resource:view', 'resource:manage',
            'cms:manage', 'settings:edit', 'user:assign_role',
        ];

        $permMap = [];
        foreach ($permissions as $p) {
            $permMap[$p] = Permission::firstOrCreate(['name' => $p]);
        }

        // 1. Site Administrator: bypass any capability restriction
        $roleSuper->permissions()->sync(array_values(array_map(fn ($p) => $p->id, $permMap)));

        // 2. Manager: delegated admin (System / Category)
        $roleManager->permissions()->sync([
            $permMap['course:create']->id,
            $permMap['course:publish']->id,
            $permMap['course:view']->id,
            $permMap['compliance:signoff']->id,
            $permMap['compliance:audit']->id,
            $permMap['audit:export']->id,
            $permMap['grade:modify']->id,
            $permMap['grade:view']->id,
            $permMap['attendance:override']->id,
            $permMap['user:assign_role']->id,
        ]);
        $roleCorpManager->permissions()->sync($roleManager->permissions->pluck('id')->toArray());

        // 3. Course Creator: create courses in categories
        $roleCreator->permissions()->sync([
            $permMap['course:create']->id,
            $permMap['course:publish']->id,
            $permMap['course:view']->id,
            $permMap['course:edit']->id,
        ]);

        // 4. Teacher (Editing): designs and delivers instruction
        $roleTeacher->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['course:edit']->id,
            $permMap['quiz:create']->id,
            $permMap['quiz:proctor']->id,
            $permMap['quiz:grade']->id,
            $permMap['assignment:grade']->id,
            $permMap['assignment:blind_grade']->id,
            $permMap['grade:modify']->id,
            $permMap['grade:view']->id,
            $permMap['grade:calculate']->id,
            $permMap['attendance:record']->id,
            $permMap['forum:moderate']->id,
            $permMap['forum:grade']->id,
            $permMap['lesson:author']->id,
            $permMap['feedback:view_reports']->id,
            $permMap['choice:manage']->id,
            $permMap['h5p:author']->id,
            $permMap['workshop:grade']->id,
            $permMap['chat:moderate']->id,
            $permMap['glossary:contribute']->id,
            $permMap['wiki:edit']->id,
            $permMap['resource:manage']->id,
            $permMap['scorm:upload']->id,
        ]);
        $roleInstructor->permissions()->sync($roleTeacher->permissions->pluck('id')->toArray());

        // 5. Non-editing Teacher: facilitates and evaluates without altering structure
        $roleTeacherNonEdit->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['quiz:proctor']->id,
            $permMap['quiz:grade']->id,
            $permMap['assignment:grade']->id,
            $permMap['grade:view']->id,
            $permMap['attendance:record']->id,
            $permMap['forum:moderate']->id,
            $permMap['chat:moderate']->id,
            $permMap['glossary:view']->id,
            $permMap['wiki:view']->id,
            $permMap['resource:view']->id,
        ]);
        $roleTrainer->permissions()->sync($roleTeacherNonEdit->permissions->pluck('id')->toArray());

        // 6. Student: consumes learning materials and takes assessments
        $roleStudent->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['quiz:attempt']->id,
            $permMap['assignment:submit']->id,
            $permMap['scorm:track']->id,
            $permMap['grade:view']->id,
            $permMap['attendance:self_mark']->id,
            $permMap['forum:post']->id,
            $permMap['lesson:attempt']->id,
            $permMap['feedback:respond']->id,
            $permMap['choice:vote']->id,
            $permMap['h5p:attempt']->id,
            $permMap['workshop:submit']->id,
            $permMap['workshop:assess']->id,
            $permMap['chat:participate']->id,
            $permMap['glossary:view']->id,
            $permMap['glossary:contribute']->id,
            $permMap['wiki:view']->id,
            $permMap['wiki:edit']->id,
            $permMap['resource:view']->id,
        ]);
        $roleEmp->permissions()->sync($roleStudent->permissions->pluck('id')->toArray());
        $roleLearnerStudent->permissions()->sync($roleStudent->permissions->pluck('id')->toArray());

        // 7. Guest: read-only access to open courses
        $roleGuest->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['resource:view']->id,
            $permMap['glossary:view']->id,
        ]);

        // 8. Authenticated User: baseline logged-in privileges
        $roleAuthUser->permissions()->sync([
            $permMap['course:view']->id,
        ]);

        // =========================================================================
        // 5. FIVE REQUIRED DEFAULT OPERATIONAL ACCOUNTS
        // =========================================================================
        $defaultPassword = Hash::make('password123');

        // 1. Super Administrator
        $userSuper = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Alexander Vance (Super Admin)',
            'email' => 'admin@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'HYBRID',
            'employee_id' => 'EMP-0001',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userSuper->id,
            'job_title' => 'Chief Executive & System Architect',
            'timezone' => 'UTC',
            'locale' => 'en',
            'bio' => 'Enterprise System Administrator with root operational authority.',
        ]);
        RoleAssignment::create([
            'user_id' => $userSuper->id,
            'role_id' => $roleSuper->id,
            'context_id' => $ctxSystem->id,
        ]);

        // 2. Corporate HR Manager
        $userHRManager = User::create([
            'tenant_id' => $tenant->id,
            'department_id' => $deptHR->id,
            'name' => 'Sarah Connor (Corporate HR Manager)',
            'email' => 'hr.manager@acmeglobal.com',
            'password' => $defaultPassword,
            'user_type' => 'EMPLOYEE',
            'employee_id' => 'EMP-1040',
            'is_active' => true,
        ]);
        $deptHR->update(['manager_user_id' => $userHRManager->id]);
        UserProfile::create([
            'user_id' => $userHRManager->id,
            'job_title' => 'Director of Corporate Compliance & People Operations',
            'timezone' => 'UTC',
            'locale' => 'en',
            'bio' => 'Manages workforce compliance tracks, audit sign-offs, and recertification cycles.',
        ]);
        RoleAssignment::create([
            'user_id' => $userHRManager->id,
            'role_id' => $roleManager->id,
            'context_id' => $ctxDept->id,
        ]);

        // 3. Academic Instructor
        $userInstructor = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Dr. Robert Oppenheimer (Academic Instructor)',
            'email' => 'instructor@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'HYBRID',
            'employee_id' => 'FAC-201',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userInstructor->id,
            'job_title' => 'Lead Cloud Security Faculty & Assessment Chair',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userInstructor->id,
            'role_id' => $roleInstructor->id,
            'context_id' => $ctxTenant->id,
        ]);

        // 4. Corporate Employee
        $userEmployee = User::create([
            'tenant_id' => $tenant->id,
            'department_id' => $deptHR->id,
            'name' => 'John Doe (Corporate Employee)',
            'email' => 'employee@acmeglobal.com',
            'password' => $defaultPassword,
            'user_type' => 'EMPLOYEE',
            'employee_id' => 'EMP-88014',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userEmployee->id,
            'job_title' => 'Enterprise Security Associate',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userEmployee->id,
            'role_id' => $roleEmp->id,
            'context_id' => $ctxDept->id,
        ]);

        // 5. Public Student (University Domain)
        $userStudent = User::create([
            'name' => 'Alice Scholarly (Public Student)',
            'email' => 'student@university.edu',
            'password' => $defaultPassword,
            'user_type' => 'STUDENT',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userStudent->id,
            'job_title' => 'Graduate Computer Science Researcher',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userStudent->id,
            'role_id' => $roleStudent->id,
            'context_id' => $ctxSystem->id,
        ]);

        // 5b. Public Student (Enterprise LMS Domain)
        $userStudent2 = User::create([
            'name' => 'Emma Watson (Public Student)',
            'email' => 'student@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'STUDENT',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userStudent2->id,
            'job_title' => 'Undergraduate Learner & Scholar',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userStudent2->id,
            'role_id' => $roleStudent->id,
            'context_id' => $ctxSystem->id,
        ]);
        // 6. Course Creator
        $userCreator = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Clara Oswald (Course Creator)',
            'email' => 'creator@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'HYBRID',
            'employee_id' => 'CRT-501',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userCreator->id,
            'job_title' => 'Curriculum Architect & Course Creator',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userCreator->id,
            'role_id' => $roleCreator->id,
            'context_id' => $ctxCategory->id,
        ]);

        // 7. Teacher (Editing) - Canonical Moodle Editing Instructor
        $userTeacherEdit = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Marcus Aurelius (Editing Teacher)',
            'email' => 'teacher.editing@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'HYBRID',
            'employee_id' => 'TCH-101',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userTeacherEdit->id,
            'job_title' => 'Senior Instructional Designer & Lead Evaluator',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userTeacherEdit->id,
            'role_id' => $roleTeacher->id,
            'context_id' => $ctxTenant->id,
        ]);

        // 8. Non-editing Teacher - Facilitator & Grader
        $userTeacherNonEdit = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Elena Rostova (Non-editing Teacher)',
            'email' => 'teacher.assistant@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'HYBRID',
            'employee_id' => 'TA-202',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userTeacherNonEdit->id,
            'job_title' => 'Teaching Assistant & Evaluation Moderator',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userTeacherNonEdit->id,
            'role_id' => $roleTeacherNonEdit->id,
            'context_id' => $ctxTenant->id,
        ]);

        // 9. Guest User
        $userGuest = User::create([
            'name' => 'Guest Visitor (Read-Only)',
            'email' => 'guest@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'STUDENT',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userGuest->id,
            'job_title' => 'Public Visitor',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userGuest->id,
            'role_id' => $roleGuest->id,
            'context_id' => $ctxSystem->id,
        ]);

        // 10. Authenticated User (Standard User Account)
        $userAuth = User::create([
            'name' => 'David Miller (Standard User)',
            'email' => 'user@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'EMPLOYEE',
            'employee_id' => 'USR-900',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userAuth->id,
            'job_title' => 'Authenticated Enterprise Learner',
            'timezone' => 'UTC',
            'locale' => 'en',
        RoleAssignment::create([
            'user_id' => $userAuth->id,
            'role_id' => $roleAuthUser->id,
            'context_id' => $ctxSystem->id,
        ]);

        // 11. Parent Guardian Account
        $userParent = User::create([
            'name' => 'Sarah Connor (Parent Guardian)',
            'email' => 'parent@enterpriselms.com',
            'password' => $defaultPassword,
            'user_type' => 'STUDENT',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $userParent->id,
            'job_title' => 'Registered Parent & Academic Sponsor',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);
        RoleAssignment::create([
            'user_id' => $userParent->id,
            'role_id' => $roleAuthUser->id,
            'context_id' => $ctxSystem->id,
        ]);
        \App\Models\ParentStudent::create([
            'parent_id' => $userParent->id,
            'student_id' => $userStudent2->id,
            'relationship' => 'Mother / Academic Guardian',
        ]);

        // =========================================================================
        // 6. ZERO-HARDCODING SITE SETTINGS
        // =========================================================================
        $siteSettings = [
            'brand_name' => 'Enterprise Academy',
            'brand_primary_color' => '#0F172A',
            'brand_accent_color' => '#2563EB',
            'brand_logo_url' => '',
            'site_title' => 'Enterprise Dual-Audience Learning Management System',
            'meta_description' => 'Automated corporate compliance recertifications and public accredited degree-grade certifications.',
            'nav_courses' => 'Course Catalog',
            'nav_compliance' => 'Corporate Compliance',
            'nav_pricing' => 'Pricing & Cohorts',
            'nav_my_learning' => 'My Learning',
            'hero_badge' => 'Dual-Audience Enterprise LMS',
            'hero_headline' => 'Next-Gen Workforce Compliance & Academic Excellence',
            'hero_subheadline' => 'Unified platform for corporate recertification audits and public accredited student cohorts. Zero friction, fully compliant.',
            'footer_copyright' => 'All rights reserved. WCAG 2.1 AA Compliant.',
            'link_terms' => 'Terms of Service',
            'link_privacy' => 'Privacy Policy',
            'link_support' => 'Auditor Verification',
            'label_login' => 'Sign In',
            'label_register' => 'Enroll Now',
            'label_logout' => 'Sign Out',
            'label_portal' => 'Management Hub',
            'label_admin_portal' => 'Admin & Audits',
            'label_switch_light' => 'Switch to Light Mode',
            'label_switch_dark' => 'Switch to Dark Mode',
            // Topbar Promo & Countdown Banner Settings (Dynamic from Admin)
            'topbar_enabled' => '1',
            'topbar_badge_text' => 'SPECIAL EVENT',
            'topbar_promo_text' => 'Get 50% Off Annual Enterprise Compliance & Accredited Certifications',
            'topbar_btn_text' => 'Claim Offer',
            'topbar_btn_url' => '/p/pricing',
            'topbar_countdown_days' => '04',
            'topbar_countdown_hours' => '18',
            'topbar_countdown_minutes' => '35',
            'topbar_countdown_seconds' => '30',
        ];

        foreach ($siteSettings as $k => $v) {
            SiteSetting::create([
                'tenant_id' => null,
                'setting_key' => $k,
                'setting_value' => $v,
                'data_type' => str_ends_with($k, '_color') ? 'COLOR' : 'STRING',
                'is_public' => true,
            ]);
        }

        // =========================================================================
        // 7. DEFAULT CMS HOMEPAGE WITH DETAILED STYLES & SECTIONS
        // (Hero, Metrics, Course Catalog, Contact Form, Footer)
        // =========================================================================
        $cmsHome = CmsPage::create([
            'tenant_id' => null,
            'slug' => 'home',
            'title' => 'Home',
            'meta_title' => 'Enterprise Dual-Audience LMS - Workforce Compliance & Academic Cohorts',
            'meta_description' => 'Unified platform for corporate recertification audits and public accredited student cohorts.',
            'is_published' => true,
        ]);

        // Section 1: HERO
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_HERO,
            'order_index' => 1,
            'content_json' => [
                'headline' => 'Next-Gen Workforce Compliance & Academic Excellence',
                'subheadline' => 'Unified platform for corporate recertification audits and public accredited student cohorts. Zero friction, fully compliant.',
                'badge_text' => 'Dual-Audience Enterprise LMS',
                'primary_cta_label' => 'Explore Curriculum',
                'secondary_cta_label' => 'Corporate Compliance Hub',
            ],
            'styles_json' => [
                'background' => 'radial-gradient(ellipse at 80% 20%, rgba(37, 99, 235, 0.08), transparent 70%)',
                'padding' => 'py-5',
            ],
        ]);

        // Section 2: CATEGORIES
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_CATEGORIES,
            'order_index' => 2,
            'content_json' => [
                'title' => 'Explore Categories',
                'subtitle' => 'Browse top categories and find the perfect course to grow your skills.',
                'view_all_text' => 'View All Categories',
                'view_all_url' => route('courses.index'),
                'categories' => [
                    ['name' => 'Web Development', 'lessons' => '48+ lessons', 'icon' => 'code-window', 'url' => route('courses.index')],
                    ['name' => 'Data Science', 'lessons' => '52+ lessons', 'icon' => 'data-network', 'url' => route('courses.index')],
                    ['name' => 'IT & Software', 'lessons' => '50+ lessons', 'icon' => 'it-software', 'url' => route('courses.index')],
                    ['name' => 'UI/UX Design', 'lessons' => '38+ lessons', 'icon' => 'ui-ux', 'url' => route('courses.index')],
                    ['name' => 'Marketing', 'lessons' => '36+ lessons', 'icon' => 'marketing', 'url' => route('courses.index')],
                    ['name' => 'Cybersecurity', 'lessons' => '47+ lessons', 'icon' => 'cybersecurity', 'url' => route('courses.index')],
                    ['name' => 'Finance & Accounting', 'lessons' => '25+ lessons', 'icon' => 'finance', 'url' => route('courses.index')],
                    ['name' => 'Health & Fitness', 'lessons' => '20+ lessons', 'icon' => 'health', 'url' => route('courses.index')],
                ],
                'highlights' => [
                    ['icon' => 'bi-globe2', 'text' => '500+ Expert-led categories'],
                    ['icon' => 'bi-star-fill text-warning', 'text' => '94% Career growth rate'],
                    ['icon' => 'bi-mortarboard-fill text-primary', 'text' => 'New category every month'],
                ],
            ],
        ]);

        // Section 3: COURSE CATALOG
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_CATALOG,
            'order_index' => 3,
            'content_json' => [
                'tagline' => 'Curated Programs',
                'title' => 'Featured Dual-Audience Tracks',
            ],
            'styles_json' => [
                'padding' => 'py-5',
            ],
        ]);

        // Section 4: MEET OUR MENTORS
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_MENTORS,
            'order_index' => 4,
            'content_json' => [
                'title' => 'Meet Our Mentors',
                'subtitle' => 'Learn from industry experts who bring real-world experience straight to your screen.',
                'mentors' => [
                    [
                        'name' => 'Darren Treadway',
                        'role' => 'Data Science Instructor',
                        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                        'students' => '28k+',
                        'courses' => '8',
                        'rating' => '98%',
                    ],
                    [
                        'name' => 'Tammi Allison',
                        'role' => 'UI/UX Design Mentor',
                        'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                        'students' => '19k+',
                        'courses' => '12',
                        'rating' => '96%',
                    ],
                    [
                        'name' => 'Micheal Cabot',
                        'role' => 'Full-Stack Web Instructor',
                        'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                        'students' => '24k+',
                        'courses' => '10',
                        'rating' => '97%',
                    ],
                    [
                        'name' => 'Sofia Bennett',
                        'role' => 'Marketing & Growth Coach',
                        'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
                        'students' => '31k+',
                        'courses' => '9',
                        'rating' => '95%',
                    ],
                ],
            ],
        ]);

        // Section 5: UPCOMING LIVE EVENTS
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_EVENTS,
            'order_index' => 5,
            'content_json' => [
                'title' => 'Upcoming Live Events',
                'subtitle' => 'Join mentors, industry experts, and global learners in real-time sessions.',
                'view_all_text' => 'View All Event',
                'view_all_url' => route('courses.index'),
                'featured_image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
                'events' => [
                    [
                        'title' => "Web3 Builder's Meetup",
                        'date' => 'Jul 02, 2026',
                        'location' => 'Singapore',
                        'price' => '$79',
                        'price_label' => '/ networking',
                        'reserve_url' => route('courses.index'),
                    ],
                    [
                        'title' => 'Data Science Bootcamp',
                        'date' => 'Jul 10, 2026',
                        'location' => 'Online (Zoom + Discord)',
                        'price' => 'Free',
                        'price_label' => '/ limited seats',
                        'reserve_url' => route('courses.index'),
                    ],
                    [
                        'title' => 'UI/UX Design Hackathon',
                        'date' => 'Jul 20, 2026',
                        'location' => 'Berlin & Online',
                        'price' => '$49',
                        'price_label' => '/ early bird',
                        'reserve_url' => route('courses.index'),
                    ],
                    [
                        'title' => 'No-Code Hackathon',
                        'date' => 'Jul 28, 2026',
                        'location' => 'Virtual + Global',
                        'price' => '$15',
                        'price_label' => '/ student discount',
                        'reserve_url' => route('courses.index'),
                    ],
                ],
            ],
        ]);

        // Section 6: FREQUENTLY ASKED QUESTIONS (FAQ)
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_FAQ,
            'order_index' => 6,
            'content_json' => [
                'title' => 'Frequently Asked Questions',
                'subtitle' => 'Everything you need to know about Lerntix. Find clarity in seconds.',
                'support_title' => 'Still Need Help?',
                'support_subtitle' => 'Our support team is available 24/7 to guide you through your learning journey.',
                'support_email' => 'hello@lerntix.com',
                'support_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
                'faqs' => [
                    [
                        'question' => 'What makes Lerntix different from other LMS platforms?',
                        'answer' => 'Lerntix combines AI-driven learning paths, hands-on labs, and real-time progress analytics. Unlike traditional LMS, we offer adaptive quizzes, industry-verified certificates, and a vibrant mentor community — all in one intuitive dashboard.',
                    ],
                    [
                        'question' => 'Are the certificates recognized by employers?',
                        'answer' => 'Yes! Every certificate issued contains a unique verification UUID and QR code that can be authenticated instantly by employers and university registries worldwide.',
                    ],
                    [
                        'question' => 'Can I try Lerntix before subscribing?',
                        'answer' => 'Absolutely. You can explore foundational modules and select workshops completely free before deciding on individual or enterprise corporate subscriptions.',
                    ],
                    [
                        'question' => 'Are the courses self-paced or live?',
                        'answer' => 'Both. We offer flexible self-paced video modules with interactive coding playgrounds alongside scheduled live masterclasses and instructor office hours.',
                    ],
                    [
                        'question' => 'Is there a mobile app for offline learning?',
                        'answer' => 'Yes. Our platform is completely responsive and supports offline lesson caching so you can study smoothly on smartphones, tablets, or laptops anywhere.',
                    ],
                    [
                        'question' => 'Can teams or businesses use Lerntix?',
                        'answer' => 'Yes. Our dedicated Corporate Console features CBAC role assignment, SCORM packages, employee completion matrices, and automated compliance recertification engines.',
                    ],
                ],
            ],
        ]);

        // Section 7: FASTER PERFORMANCE & INQUIRY FORM
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_DYNAMIC_FORM,
            'order_index' => 7,
            'content_json' => [
                'title' => 'Inquire for Enterprise Deployment',
                'subtitle' => 'Our enterprise solutions architects will design your custom CBAC permissions hierarchy and compliance track.',
                'submit_label' => 'Submit Enterprise Inquiry',
            ],
            'styles_json' => [
                'padding' => 'py-5',
            ],
        ]);

        // Section 8: PRICING & COHORTS
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_PRICING,
            'order_index' => 8,
            'content_json' => [
                'badge' => 'Flexible Licensing',
                'title' => 'Enterprise Compliance or Individual Cohorts',
                'subtitle' => 'Select the operational model tailored to your organizational scale.',
            ],
            'styles_json' => [
                'background' => 'bg-body-tertiary',
            ],
        ]);

        // Section 9: FOOTER
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_FOOTER,
            'order_index' => 9,
            'content_json' => [
                'about_text' => 'The next-generation enterprise Learning Management System engineering compliance tracks, academic cohorts, and anti-cheat assessment infrastructure.',
            ],
            'styles_json' => [
                'background' => 'bg-dark text-white',
            ],
        ]);

        // Additional Standard CMS Pages
        CmsPage::firstOrCreate(['slug' => 'pricing'], [
            'tenant_id' => null,
            'title' => 'Cohort Pricing & Enterprise Licensing',
            'meta_title' => 'Pricing Plans - Enterprise Compliance & Academic Cohorts',
            'meta_description' => 'Flexible pricing plans for enterprise compliance recertification and accredited student cohorts.',
            'is_published' => true,
        ]);

        CmsPage::firstOrCreate(['slug' => 'terms'], [
            'tenant_id' => null,
            'title' => 'Enterprise Terms of Service',
            'meta_title' => 'Terms of Service - Enterprise Academy',
            'meta_description' => 'Enterprise Terms of Service and CBAC operational governance.',
            'is_published' => true,
        ]);

        CmsPage::firstOrCreate(['slug' => 'privacy'], [
            'tenant_id' => null,
            'title' => 'Privacy Policy & Data Protection',
            'meta_title' => 'Privacy Policy - Enterprise Academy',
            'meta_description' => 'Privacy policy, telemetry controls, and GDPR compliance standards.',
            'is_published' => true,
        ]);

        CmsPage::firstOrCreate(['slug' => 'support'], [
            'tenant_id' => null,
            'title' => 'Auditor Verification & Support',
            'meta_title' => 'Auditor Verification & Support - Enterprise Academy',
            'meta_description' => 'Auditor verification hub, compliance support tickets, and direct solution architect escalations.',
            'is_published' => true,
        ]);

        // =========================================================================
        // 8. COURSE CREATION:
        // Course A: Internal Corporate Compliance (with 365-day recertification cycle)
        // Course B: External Public Paid Course ($49 USD)
        // =========================================================================

        // Course A: Internal Corporate Compliance
        $courseIso = Course::create([
            'tenant_id' => $tenant->id,
            'category_id' => $catCompliance->id,
            'title' => 'ISO/IEC 27001 Information Security Recertification 2026',
            'slug' => 'iso-27001-recertification-2026',
            'target_audience' => Course::AUDIENCE_CORPORATE,
            'format' => Course::FORMAT_SELF_PACED,
            'passing_grade' => 80,
            'recertification_days' => 365, // Recertification cycle of 365 days
            'price_cents' => 0,            // Internal / Free for employees
            'currency' => 'USD',
            'is_published' => true,
        ]);

        // Course Context Level
        $ctxCourse = Context::create([
            'context_level' => Context::LEVEL_COURSE,
            'instance_id' => (string) $courseIso->id,
            'path' => '1/2/' . $deptHR->id . '/' . $catCompliance->id . '/' . $courseIso->id,
        ]);

        // Course B: External Public Paid Course
        $catAcademic = CourseCategory::create([
            'tenant_id' => null,
            'name' => 'Computer Science & Cloud Engineering',
            'slug' => 'cloud-engineering',
        ]);

        $courseCloud = Course::create([
            'tenant_id' => null,
            'category_id' => $catAcademic->id,
            'title' => 'Full-Stack Enterprise Cloud Architecture & DevSecOps',
            'slug' => 'enterprise-cloud-architecture-devsecops',
            'target_audience' => Course::AUDIENCE_PUBLIC,
            'format' => Course::FORMAT_SELF_PACED,
            'passing_grade' => 70,
            'recertification_days' => null, // Lifetime academic certification
            'price_cents' => 4900,         // $49.00 USD paid course
            'currency' => 'USD',
            'is_published' => true,
        ]);

        // =========================================================================
        // 9. SCORM, ASSESSMENTS, GRADEBOOK & SECTIONS FOR COMPLIANCE COURSE
        // =========================================================================
        $scormPkg = ScormPackage::create([
            'tenant_id' => $tenant->id,
            'title' => 'Interactive Phishing & Threat Mitigation Simulation',
            'version' => '1.2',
            'entry_url' => '',
            'storage_path' => 'scorm/threat_sim_v1',
        ]);

        $bank = QuestionBank::create([
            'tenant_id' => $tenant->id,
            'name' => 'ISO 27001 Question Bank',
            'department_id' => $deptSec->id,
        ]);

        $q1 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_SINGLE_CHOICE,
            'question_text' => 'Under ISO/IEC 27001:2022, what is the mandatory frequency for reviewing the Statement of Applicability (SoA)?',
            'points' => 10,
        ]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Annually or upon significant organizational/technological change', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Every 5 years', 'is_correct' => false, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Only following an unauthorized breach', 'is_correct' => false, 'sort_order' => 3]);

        $q2 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_TRUE_FALSE,
            'question_text' => 'Multi-factor authentication (MFA) is strictly required for remote access under Annex A.9.',
            'points' => 10,
        ]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'True', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'False', 'is_correct' => false, 'sort_order' => 2]);

        $q3 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_MULTI_CHOICE,
            'question_text' => 'Which controls are categorized under Organizational controls in ISO 27001:2022?',
            'points' => 10,
        ]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Policies for information security', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Information security roles and responsibilities', 'is_correct' => true, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Physical perimeter barriers', 'is_correct' => false, 'sort_order' => 3]);

        $q4 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_SHORT_ANSWER,
            'question_text' => 'What does the CIA triad stand for in information security governance?',
            'points' => 10,
        ]);

        $q5 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_MATCHING,
            'question_text' => 'Match each ISO 27001 control clause to its primary focus domain.',
            'points' => 15,
        ]);
        QuestionOption::create(['question_id' => $q5->id, 'option_text' => 'Clause 5 -> Organizational Controls; Clause 6 -> People Controls; Clause 7 -> Physical Controls; Clause 8 -> Technological Controls', 'is_correct' => true, 'sort_order' => 1]);

        $q6 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_NUMERICAL,
            'question_text' => 'What is the minimum recommended RSA key length (in bits) for digital certificate root authorities through 2030?',
            'points' => 10,
        ]);

        $q7 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_ESSAY,
            'question_text' => 'Draft a concise executive escalation protocol for a suspected ransomware exfiltration incident.',
            'points' => 15,
        ]);

        $q8 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_DRAG_DROP,
            'question_text' => 'Arrange the following incident response phases in chronological sequence: Identification, Containment, Eradication, Recovery, Lessons Learned.',
            'points' => 10,
        ]);

        $q9 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_CALCULATED,
            'question_text' => 'Calculate allowed annual downtime in minutes for an enterprise service level agreement (SLA) target of 99.9% availability.',
            'points' => 10,
        ]);

        $quizIso = Quiz::create([
            'title' => 'ISO 27001 Annual Recertification Exam',
            'time_limit_minutes' => 30,
            'shuffle_questions' => true,
            'max_attempts' => 2,
            'passing_percentage' => 80,
            'safe_browser_enforced' => true,
            'adaptive_mode' => true,
            'penalty_rate' => 0.10,
            'anti_cheat_flags' => [
                'blur_detection' => true,
                'block_copy_paste' => true,
                'webcam_snapshots' => true,
                'safe_exam_browser' => true,
            ],
        ]);
        $quizIso->questions()->attach([$q1->id, $q2->id, $q3->id, $q4->id, $q5->id, $q6->id, $q7->id, $q8->id, $q9->id]);

        $assignment = Assignment::create([
            'title' => 'SOC 2 & ISO 27001 Mapping Defense Dossier',
            'instructions' => 'Submit a detailed technical report demonstrating encryption standards across cloud microservices.',
            'max_file_size_mb' => 25,
            'allowed_mime_types' => 'pdf,docx,zip',
            'max_points' => 100,
            'is_blind_marking' => true,
            'marking_workflow_enabled' => true,
            'rubric_json' => [
                ['criterion' => 'Executive Alignment', 'weight' => 25, 'description' => 'Maps controls to corporate risk appetite.'],
                ['criterion' => 'Cryptographic Controls Verification', 'weight' => 50, 'description' => 'Validates TLS 1.3 and AES-256 ciphers.'],
                ['criterion' => 'Remediation Roadmap', 'weight' => 25, 'description' => 'Specifies milestones and auditor signoff steps.'],
            ],
        ]);

        $attSession = AttendanceSession::create([
            'course_id' => $courseIso->id,
            'title' => 'San Francisco SOC Live Briefing',
            'session_date' => Carbon::now()->toDateString(),
            'qr_secret' => Str::random(32),
            'lat_boundary' => 37.774929,
            'lng_boundary' => -122.419416,
            'radius_meters' => 50,
        ]);

        // =========================================================================
        // MOODLE SUBSYSTEMS: GROUPS, BADGES & COMPETENCIES
        // =========================================================================
        CourseGroup::create([
            'course_id' => $courseIso->id,
            'name' => 'Cohort Alpha - Internal SecOps',
            'group_mode' => 'SEPARATE',
            'description' => 'Dedicated cohort partition for internal cybersecurity staff.',
        ]);
        CourseGroup::create([
            'course_id' => $courseIso->id,
            'name' => 'Cohort Beta - Third-Party Auditors',
            'group_mode' => 'VISIBLE',
            'description' => 'Cross-visible review stream for external accreditation inspectors.',
        ]);

        CourseBadge::create([
            'course_id' => $courseIso->id,
            'name' => 'Certified ISO 27001 Lead Auditor',
            'description' => 'Demonstrated mastery across risk management, cryptographic controls, and Annex A audit readiness.',
            'criteria_type' => 'COURSE_COMPLETION',
            'criteria_meta_json' => ['passing_grade' => 80, 'completion_required' => true],
            'is_active' => true,
        ]);

        CourseCompetency::create([
            'course_id' => $courseIso->id,
            'idnumber' => 'COMP-SEC-01',
            'framework_name' => 'NIST CSF & ISO 27001',
            'description' => 'Information Security Risk Mitigation Framework: ability to evaluate residual risk and architect defensive countermeasures.',
        ]);
        CourseCompetency::create([
            'course_id' => $courseIso->id,
            'idnumber' => 'COMP-SEC-02',
            'framework_name' => 'FIPS 140-3',
            'description' => 'Cryptographic Key Lifecycle Governance: mastery of HSM key rotations, PKI hierarchies, and certificate revocation.',
        ]);

        // =========================================================================
        // MOODLE CORE INTERACTIVE & TRACKING MODULES
        // =========================================================================
        $forum = CourseForum::create([
            'course_id' => $courseIso->id,
            'name' => 'ISO 27001 Implementation & Audit Discussion Forum',
            'type' => 'STANDARD',
            'intro' => 'Collaborate with instructors and peers on applying updated control requirements.',
            'grade_enabled' => true,
            'grade_max' => 100,
        ]);
        $disc = ForumDiscussion::create([
            'forum_id' => $forum->id,
            'user_id' => $userSuper->id,
            'name' => 'Annex A.5 Organizational Controls Discussion',
            'is_pinned' => true,
        ]);
        ForumPost::create([
            'discussion_id' => $disc->id,
            'user_id' => $userInstructor->id,
            'subject' => 'Re: Threat Intelligence Clause A.5.7',
            'message' => 'We integrate automated STIX/TAXII feeds into SIEM to satisfy proactive telemetry criteria.',
        ]);

        $lesson = CourseLesson::create([
            'course_id' => $courseIso->id,
            'name' => 'Adaptive Branching Incident Response Simulation',
            'intro' => 'A dynamic branching exercise where each triage decision redirects your mitigation route.',
            'grade_max' => 100,
            'allow_retakes' => true,
        ]);
        LessonPage::create([
            'lesson_id' => $lesson->id,
            'title' => 'Stage 1: Ransomware Perimeter Alert',
            'content_html' => 'A tier-1 endpoint alerts on unexpected volume encryption. Do you isolate network ports immediately or execute memory imaging first?',
            'page_type' => 'QUESTION',
            'qtype' => 'MULTICHOICE',
            'jumps_json' => [
                ['text' => 'Quarantine affected subnet via microsegmentation (Safe Path)', 'score' => 10],
                ['text' => 'Power cycle the affected server (Destroys volatile RAM evidence - Remedial Path)', 'score' => 0],
            ],
            'sort_order' => 1,
        ]);

        $feedback = CourseFeedback::create([
            'course_id' => $courseIso->id,
            'name' => 'Annual Course Quality & Auditor Feedback Survey',
            'intro' => 'Please provide non-graded evaluations of course materials, lab simulations, and proctoring experience.',
            'is_anonymous' => true,
            'multiple_submit' => false,
        ]);
        FeedbackItem::create([
            'feedback_id' => $feedback->id,
            'name' => 'Overall instructional depth & clarity',
            'typ' => 'NUMERIC',
            'presentation' => 'Rate from 1 to 5',
            'is_required' => true,
            'position' => 1,
        ]);

        $choice = CourseChoice::create([
            'course_id' => $courseIso->id,
            'name' => 'Select Your Supervised Proctored Exam Slot',
            'intro' => 'Reserve your virtual proctoring slot for the final certification examination.',
            'options_json' => [
                'Monday 09:00 UTC (European Track)',
                'Wednesday 14:00 UTC (Americas Track)',
                'Friday 02:00 UTC (Asia-Pacific Track)',
            ],
            'allow_multiple' => false,
            'show_results' => true,
        ]);

        $h5p = CourseH5p::create([
            'course_id' => $courseIso->id,
            'title' => 'Interactive Phishing & Social Engineering Checkpoint (H5P)',
            'package_type' => 'INTERACTIVE_VIDEO',
            'parameters_json' => ['video_source' => 'sample.mp4', 'checkpoints' => []],
            'grade_max' => 100.0,
        ]);

        $workshop = CourseWorkshop::create([
            'course_id' => $courseIso->id,
            'name' => 'Peer Review: Incident Response Runbook Evaluation',
            'intro' => 'Multi-phase peer evaluation: Grade 1 for your submission quality (80%), Grade 2 for grading accuracy assessing peer submissions (20%).',
            'phase' => 'SUBMISSION',
            'grade_submission_max' => 80.0,
            'grade_assessment_max' => 20.0,
        ]);

        $chat = CourseChat::create([
            'course_id' => $courseIso->id,
            'name' => 'Live SOC Office Hours & Real-time Consultation',
            'intro' => 'Direct text consultation channel with certified enterprise lead auditors.',
        ]);
        \App\Models\ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $userInstructor->id,
            'message' => 'Welcome to the live ISMS consultation stream. Post your questions here.',
        ]);

        $glossary = CourseGlossary::create([
            'course_id' => $courseIso->id,
            'name' => 'Information Security & Compliance Lexicon',
            'intro' => 'Definitive searchable terminology guide for ISO/IEC 27001:2022 and SOC 2.',
            'is_global' => false,
            'allow_comments' => true,
        ]);
        GlossaryEntry::create([
            'glossary_id' => $glossary->id,
            'user_id' => $userSuper->id,
            'concept' => 'Statement of Applicability (SoA)',
            'definition' => 'Mandatory document identifying which of the ISO 27001 Annex A controls are applicable, with justifications for inclusion or exclusion.',
            'is_approved' => true,
        ]);
        GlossaryEntry::create([
            'glossary_id' => $glossary->id,
            'user_id' => $userSuper->id,
            'concept' => 'Zero Trust Architecture (ZTA)',
            'definition' => 'A cybersecurity paradigm focused on resource protection where trust is never granted implicitly, requiring continuous verification.',
            'is_approved' => true,
        ]);

        $wiki = CourseWiki::create([
            'course_id' => $courseIso->id,
            'name' => 'Collaborative Cyber Defense Playbook Wiki',
            'intro' => 'Co-authored operational playbook maintained and referenced by certified audit cohorts.',
            'first_page_title' => 'First Response Standard Operating Procedures',
        ]);
        WikiPage::create([
            'wiki_id' => $wiki->id,
            'title' => 'First Response Standard Operating Procedures',
            'content_markdown' => "## Emergency Protocol\n1. Confirm anomaly via SIEM correlation.\n2. Preserve volatile forensic state in RAM.\n3. Escalate to Chief Information Security Officer.",
            'last_edited_by_user_id' => $userInstructor->id,
        ]);


        // =========================================================================
        // MOODLE CONTENT RESOURCES (Page, Book, File, Folder, URL, Label)
        // =========================================================================
        $resPage = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'PAGE',
            'title' => 'Executive Governance Policy & Scope',
            'content_html' => '<div class="prose"><h4>Scope of Information Security Management</h4><p>This program covers cloud microservices, continuous integration pipelines, and customer data repositories under strict ISO 27001 surveillance.</p></div>',
        ]);
        $resBook = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'BOOK',
            'title' => 'ISO 27001 Implementation Handbook (Chapters 1-6)',
            'content_html' => '<h4>Chapter 1: Organizational Context & Stakeholder Requirements</h4><p>Details internal and external issues affecting organizational security posture.</p>',
        ]);
        $resFile = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'FILE',
            'title' => 'Auditor_Evidence_Collection_Checklist_2026.pdf',
            'file_path' => 'resources/Auditor_Evidence_Collection_Checklist_2026.pdf',
            'content_html' => '<p>Official downloadable reference checklist for internal and third-party compliance reviews.</p>',
        ]);
        $resFolder = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'FOLDER',
            'title' => 'Regulatory SOP Templates & Defense Artifacts',
            'content_html' => '<p>Categorized directory containing policy templates, threat register spreadsheets, and sample signoff forms.</p>',
        ]);
        $resUrl = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'URL',
            'title' => 'ISO/IEC Standards Directory (Official Reference)',
            'external_url' => 'https://www.iso.org/isoiec-27001-information-security.html',
            'content_html' => '<p>Direct web bridge to official international standard documentation.</p>',
        ]);
        $resLabel = CourseResource::create([
            'course_id' => $courseIso->id,
            'resource_type' => 'LABEL',
            'title' => 'Important Milestone Notice',
            'content_html' => '<div class="alert alert-primary mb-0"><i class="bi bi-info-circle-fill me-2"></i><strong>Audit Milestone:</strong> Complete the Adaptive Simulation and Survey before initiating final proctored exam attempt.</div>',
        ]);

        // Curriculum Sections & Modules (Linking All Moodle Activities & Resources)
        $sec1 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 1: Information Security Management Systems (ISMS)',
            'sort_order' => 0,
        ]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_LABEL, 'instance_id' => $resLabel->id, 'sort_order' => 0, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_PAGE, 'instance_id' => $resPage->id, 'sort_order' => 1, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_BOOK, 'instance_id' => $resBook->id, 'sort_order' => 2, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_LESSON, 'instance_id' => $lesson->id, 'sort_order' => 3, 'is_mandatory' => true]);

        $sec2 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 2: SCORM Telemetry, H5P & Collaborative Interaction',
            'sort_order' => 1,
        ]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_SCORM, 'instance_id' => $scormPkg->id, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_H5P, 'instance_id' => $h5p->id, 'sort_order' => 1, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_FORUM, 'instance_id' => $forum->id, 'sort_order' => 2, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_WORKSHOP, 'instance_id' => $workshop->id, 'sort_order' => 3, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_GLOSSARY, 'instance_id' => $glossary->id, 'sort_order' => 4, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_WIKI, 'instance_id' => $wiki->id, 'sort_order' => 5, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_CHAT, 'instance_id' => $chat->id, 'sort_order' => 6, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_ATTENDANCE, 'instance_id' => $attSession->id, 'sort_order' => 7, 'is_mandatory' => false]);

        $sec3 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 3: Proctored Assessment, Feedback & Audit Submission',
            'sort_order' => 2,
        ]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_CHOICE, 'instance_id' => $choice->id, 'sort_order' => 0, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_FILE, 'instance_id' => $resFile->id, 'sort_order' => 1, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_ASSIGNMENT, 'instance_id' => $assignment->id, 'sort_order' => 2, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_QUIZ, 'instance_id' => $quizIso->id, 'sort_order' => 3, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_FEEDBACK, 'instance_id' => $feedback->id, 'sort_order' => 4, 'is_mandatory' => false]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_URL, 'instance_id' => $resUrl->id, 'sort_order' => 5, 'is_mandatory' => false]);

        // Cloud Course Sections
        $secCloud = CourseSection::create([
            'course_id' => $courseCloud->id,
            'title' => 'Section 1: Cloud-Native Microservices & Kubernetes Security',
            'sort_order' => 0,
        ]);
        CourseModule::create(['section_id' => $secCloud->id, 'module_type' => CourseModule::TYPE_PAGE, 'instance_id' => $resPage->id, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $secCloud->id, 'module_type' => CourseModule::TYPE_QUIZ, 'instance_id' => $quizIso->id, 'sort_order' => 1, 'is_mandatory' => true]);

        // Weighted Gradebook Subsystem (Weighted scoring + Calculation Formula)
        $gradeCatExam = GradeCategory::create([
            'course_id' => $courseIso->id,
            'name' => 'Proctored Examinations',
            'weight_percentage' => 40.0,
            'drop_lowest_count' => 0,
            'calculation_formula' => '= [QUIZ_SCORE] * 1.0',
        ]);
        $gradeCatPractical = GradeCategory::create([
            'course_id' => $courseIso->id,
            'name' => 'Audit Submissions & Defense Dossiers',
            'weight_percentage' => 60.0,
            'drop_lowest_count' => 0,
            'calculation_formula' => '= [ASSIGNMENT_SCORE] * 1.0',
        ]);
        $gradeItemQuiz = GradeItem::create([
            'course_id' => $courseIso->id,
            'category_id' => $gradeCatExam->id,
            'item_type' => GradeItem::TYPE_QUIZ,
            'item_id' => $quizIso->id,
            'max_score' => 100.0,
        ]);
        $gradeItemAssign = GradeItem::create([
            'course_id' => $courseIso->id,
            'category_id' => $gradeCatPractical->id,
            'item_type' => GradeItem::TYPE_ASSIGNMENT,
            'item_id' => $assignment->id,
            'max_score' => 100.0,
        ]);


        // =========================================================================
        // 10. ENROLLMENTS & SAMPLE CERTIFICATES
        // =========================================================================
        // Employee enrolled & completed ISO 27001 (Valid for 365 days)
        Enrollment::create([
            'user_id' => $userEmployee->id,
            'course_id' => $courseIso->id,
            'status' => Enrollment::STATUS_COMPLETED,
            'enrolled_at' => Carbon::now()->subMonths(1),
            'completed_at' => Carbon::now()->subMonths(1),
            'expires_at' => Carbon::now()->subMonths(1)->addDays(365),
        ]);

        GradeGrade::create([
            'grade_item_id' => $gradeItemQuiz->id,
            'user_id' => $userEmployee->id,
            'final_score' => 95.0,
            'is_locked' => true,
        ]);
        GradeGrade::create([
            'grade_item_id' => $gradeItemAssign->id,
            'user_id' => $userEmployee->id,
            'final_score' => 90.0,
            'is_locked' => true,
        ]);

        Certificate::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userEmployee->id,
            'course_id' => $courseIso->id,
            'issued_at' => Carbon::now()->subMonths(1),
            'expires_at' => Carbon::now()->subMonths(1)->addDays(365),
            'pdf_url' => null,
        ]);

        // Student active in Cloud Course
        Enrollment::create([
            'user_id' => $userStudent->id,
            'course_id' => $courseCloud->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => Carbon::now()->subDays(3),
            'expires_at' => null,
        ]);

        Enrollment::create([
            'user_id' => $userStudent2->id,
            'course_id' => $courseCloud->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => Carbon::now()->subDays(3),
            'expires_at' => null,
        ]);

        Enrollment::create([
            'user_id' => $userStudent2->id,
            'course_id' => $courseIso->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => Carbon::now()->subDays(1),
            'expires_at' => null,
        ]);
    }
}
