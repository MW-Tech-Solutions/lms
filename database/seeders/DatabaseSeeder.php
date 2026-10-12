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
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
        // 2. ORGANIZATIONAL DEPARTMENTS
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
        // 4. ROLE & PERMISSION MATRIX
        // =========================================================================
        $roleSuper = Role::create(['name' => Role::SUPER_ADMIN, 'is_system' => true]);
        $roleManager = Role::create(['name' => Role::CORP_MANAGER, 'is_system' => true]);
        $roleInstructor = Role::create(['name' => Role::INSTRUCTOR, 'is_system' => true]);
        $roleTrainer = Role::create(['name' => Role::TRAINER, 'is_system' => true]);
        $roleEmp = Role::create(['name' => Role::LEARNER_EMPLOYEE, 'is_system' => true]);
        $roleStudent = Role::create(['name' => Role::LEARNER_STUDENT, 'is_system' => true]);

        $permissions = [
            'course:create', 'course:publish', 'course:view',
            'quiz:attempt', 'quiz:proctor', 'quiz:grade',
            'grade:modify', 'grade:view',
            'compliance:signoff', 'compliance:audit', 'audit:export',
            'scorm:upload', 'scorm:track',
            'attendance:record', 'attendance:override',
            'cms:manage', 'settings:edit', 'user:assign_role',
        ];

        $permMap = [];
        foreach ($permissions as $p) {
            $permMap[$p] = Permission::create(['name' => $p]);
        }

        // Grant Permissions: Super Admin (All)
        $roleSuper->permissions()->sync(array_values(array_map(fn ($p) => $p->id, $permMap)));

        // Corporate HR Manager
        $roleManager->permissions()->sync([
            $permMap['compliance:signoff']->id,
            $permMap['compliance:audit']->id,
            $permMap['audit:export']->id,
            $permMap['grade:modify']->id,
            $permMap['attendance:override']->id,
            $permMap['course:view']->id,
        ]);

        // Academic Instructor
        $roleInstructor->permissions()->sync([
            $permMap['course:create']->id,
            $permMap['course:publish']->id,
            $permMap['course:view']->id,
            $permMap['quiz:proctor']->id,
            $permMap['quiz:grade']->id,
            $permMap['grade:modify']->id,
            $permMap['grade:view']->id,
            $permMap['scorm:upload']->id,
            $permMap['attendance:record']->id,
        ]);

        // Corporate Employee
        $roleEmp->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['quiz:attempt']->id,
            $permMap['scorm:track']->id,
            $permMap['grade:view']->id,
        ]);

        // Public Academic Student
        $roleStudent->permissions()->sync([
            $permMap['course:view']->id,
            $permMap['quiz:attempt']->id,
            $permMap['scorm:track']->id,
            $permMap['grade:view']->id,
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

        // Section 2: METRICS
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_METRICS,
            'order_index' => 2,
            'content_json' => [
                'metrics' => [
                    ['value' => '99.9%', 'label' => 'Audit SLA Compliance', 'icon' => 'bi-shield-check', 'color' => 'text-success'],
                    ['value' => '45,000+', 'label' => 'Certified Professionals', 'icon' => 'bi-people', 'color' => 'text-primary'],
                    ['value' => '100%', 'label' => 'SCORM 1.2 / 2004 Support', 'icon' => 'bi-box-seam', 'color' => 'text-info'],
                    ['value' => '0.00ms', 'label' => 'Sub-millisecond Anti-Cheat Sync', 'icon' => 'bi-lightning-charge', 'color' => 'text-warning'],
                ],
            ],
            'styles_json' => [
                'background' => 'bg-body-tertiary',
                'border' => 'border-top border-bottom',
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

        // Section 4: PRICING & COHORTS
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_PRICING,
            'order_index' => 4,
            'content_json' => [
                'badge' => 'Flexible Licensing',
                'title' => 'Enterprise Compliance or Individual Cohorts',
                'subtitle' => 'Select the operational model tailored to your organizational scale.',
            ],
            'styles_json' => [
                'background' => 'bg-body-tertiary',
            ],
        ]);

        // Section 5: CONTACT FORM
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_DYNAMIC_FORM,
            'order_index' => 5,
            'content_json' => [
                'title' => 'Inquire for Enterprise Deployment',
                'subtitle' => 'Our enterprise solutions architects will design your custom CBAC permissions hierarchy and compliance track.',
                'submit_label' => 'Submit Enterprise Inquiry',
            ],
            'styles_json' => [
                'padding' => 'py-5',
            ],
        ]);

        // Section 6: FOOTER
        CmsSection::create([
            'page_id' => $cmsHome->id,
            'section_type' => CmsSection::TYPE_FOOTER,
            'order_index' => 6,
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
            'points' => 25,
        ]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Annually or upon significant organizational/technological change', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Every 5 years', 'is_correct' => false, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Only following an unauthorized breach', 'is_correct' => false, 'sort_order' => 3]);

        $q2 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_TRUE_FALSE,
            'question_text' => 'Multi-factor authentication (MFA) is strictly required for remote access under Annex A.9.',
            'points' => 25,
        ]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'True', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'False', 'is_correct' => false, 'sort_order' => 2]);

        $q3 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_MULTI_CHOICE,
            'question_text' => 'Which controls are categorized under Organizational controls in ISO 27001:2022?',
            'points' => 25,
        ]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Policies for information security', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Information security roles and responsibilities', 'is_correct' => true, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Physical perimeter barriers', 'is_correct' => false, 'sort_order' => 3]);

        $q4 = Question::create([
            'bank_id' => $bank->id,
            'type' => Question::TYPE_SHORT_ANSWER,
            'question_text' => 'What does the CIA triad stand for in information security governance?',
            'points' => 25,
        ]);

        $quizIso = Quiz::create([
            'title' => 'ISO 27001 Annual Recertification Exam',
            'time_limit_minutes' => 30,
            'shuffle_questions' => false,
            'max_attempts' => 2,
            'passing_percentage' => 80,
            'anti_cheat_flags' => [
                'blur_detection' => true,
                'block_copy_paste' => true,
                'webcam_snapshots' => true,
            ],
        ]);
        $quizIso->questions()->attach([$q1->id, $q2->id, $q3->id, $q4->id]);

        $assignment = Assignment::create([
            'title' => 'SOC 2 & ISO 27001 Mapping Defense Dossier',
            'instructions' => 'Submit a detailed technical report demonstrating encryption standards across cloud microservices.',
            'max_file_size_mb' => 20,
            'allowed_mime_types' => 'pdf,docx,zip',
            'max_points' => 100,
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

        // Curriculum Sections & Modules
        $sec1 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 1: Information Security Management Systems (ISMS)',
            'sort_order' => 0,
        ]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_LESSON, 'instance_id' => 1, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec1->id, 'module_type' => CourseModule::TYPE_VIDEO, 'instance_id' => 1, 'sort_order' => 1, 'is_mandatory' => true]);

        $sec2 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 2: SCORM Telemetry & Phishing Simulation',
            'sort_order' => 1,
        ]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_SCORM, 'instance_id' => $scormPkg->id, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec2->id, 'module_type' => CourseModule::TYPE_ATTENDANCE, 'instance_id' => $attSession->id, 'sort_order' => 1, 'is_mandatory' => false]);

        $sec3 = CourseSection::create([
            'course_id' => $courseIso->id,
            'title' => 'Section 3: Proctored Assessment & Audit Submission',
            'sort_order' => 2,
        ]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_QUIZ, 'instance_id' => $quizIso->id, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $sec3->id, 'module_type' => CourseModule::TYPE_ASSIGNMENT, 'instance_id' => $assignment->id, 'sort_order' => 1, 'is_mandatory' => true]);

        // Cloud Course Sections
        $secCloud = CourseSection::create([
            'course_id' => $courseCloud->id,
            'title' => 'Section 1: Cloud-Native Microservices & Kubernetes Security',
            'sort_order' => 0,
        ]);
        CourseModule::create(['section_id' => $secCloud->id, 'module_type' => CourseModule::TYPE_LESSON, 'instance_id' => 1, 'sort_order' => 0, 'is_mandatory' => true]);
        CourseModule::create(['section_id' => $secCloud->id, 'module_type' => CourseModule::TYPE_VIDEO, 'instance_id' => 1, 'sort_order' => 1, 'is_mandatory' => true]);

        // Weighted Gradebook
        $gradeCatExam = GradeCategory::create([
            'course_id' => $courseIso->id,
            'name' => 'Proctored Examinations',
            'weight_percentage' => 50.0,
            'drop_lowest_count' => 0,
        ]);
        $gradeCatPractical = GradeCategory::create([
            'course_id' => $courseIso->id,
            'name' => 'Audit Submissions',
            'weight_percentage' => 50.0,
            'drop_lowest_count' => 0,
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
