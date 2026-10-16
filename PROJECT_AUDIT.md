# PROJECT AUDIT: AllThingsCambridge LMS Platform
**Date:** October 15, 2026  
**Document Version:** 1.0 (Formal Comprehensive Workspace Audit)  
**Author:** Principal Full-Stack Software Engineer & Solutions Architect  
**Workspace:** `c:\xampp\htdocs\LMS`

---

## 1. Executive Architecture Summary

The current workspace is a **Laravel 11 application** running on PHP 8.2+ with a MySQL / MariaDB relational database (`enterprise_lms` on port `3308`), styled using modern responsive Blade templates with Vanilla CSS, Bootstrap 5.3 components, and Alpine.js.

The platform was originally scaffolded as an Enterprise Regulatory Compliance & SCORM LMS and has been progressively transformed toward the **AllThingsCambridge** international education ecosystem specified by Kendat Integrated Services and the AllThingsCambridge blueprints.

### Key Technology Stack:
- **Framework:** Laravel 11.x (PHP 8.2+)
- **Database:** MySQL / MariaDB (port 3308, database: `enterprise_lms`)
- **Frontend Stack:** Laravel Blade, Alpine.js, Bootstrap 5.3, Bootstrap Icons, KaTeX (Mathematical LaTeX equations), Chart.js
- **Payment Infrastructure:** Stripe Checkout & Webhook Ready, PayPal support, currency in GBP (`£`)
- **Authentication:** Session-based auth with CBAC (Context-Based Access Control) and multi-role capabilities.

---

## 2. Existing Implemented Features (Retained & Verified)

1. **Authentication & Session Security:**
   - Standard registration (`/register`), login (`/login`), and logout (`/logout`).
   - Role assignments via `roles`, `permissions`, `role_assignments`, and `role_permissions` tables.
   - User profile management.

2. **Core CMS Engine:**
   - Dynamic page management (`cms_pages`), dynamic sections (`cms_sections`) with hero, feature grid, testimonials, CTA, statistics, dynamic form, and silo selection modules.
   - Live admin interface at `/admin/cms`.

3. **Academic Structure & Cataloging:**
   - `course_categories` table storing the 5 official Cambridge assessment levels:
     1. Cambridge Primary Checkpoint (Years 1–6)
     2. Cambridge Lower Secondary (Years 7–9)
     3. Cambridge IGCSE (Years 10–11)
     4. Cambridge AS Level (Year 12)
     5. Cambridge A Level (Year 13)
     plus Cambridge subject codes (Mathematics 9709, Physics 9702, Chemistry 9701/0620, Biology 9700/0610).
   - Dynamic catalog at `/courses` organized by category sections with level quick-filters and search.

4. **Curriculum & Modular Engine:**
   - `courses`, `course_sections`, `course_modules`, `course_resources`, and `course_lessons` tables.
   - Full SCORM package player and tracking engine (`/scorm/launch/{package_id}`).
   - Interactive Learner Player (`/learn/{course}`).

5. **Assessments & Quizzes:**
   - `quizzes`, `questions`, `question_options`, `question_banks`, `quiz_attempts`, and `quiz_attempt_answers`.
   - Real-time autosave (`/api/quiz/autosave`) and proctoring anti-cheat monitoring (`/api/anti-cheat/log`).
   - Interactive formula rendering via KaTeX.

6. **Gradebook, Certificates & Verification:**
   - Weighted grade calculations across assessment categories.
   - Cryptographically verifiable certificates with QR verification (`/verify/{uuid}`).

---

## 3. Partially Implemented Features (Requiring Upgrade)

1. **Academic Hierarchy (Assessment Level → Subject → Syllabus → Chapter → Topic):**
   - Current tables link `courses` to `course_categories`, `course_sections`, and `course_modules`.
   - Needs explicit normalization for Examination Series (Feb/March, May/June, Oct/Nov), Paper Variants, and components (Paper 1 Pure, Paper 3 Pure, Mechanics, Statistics) across the 5 assessment levels.

2. **Esther Math Past-Paper Revision Matrix Tracker:**
   - Currently demonstrated on the homepage and past papers catalog.
   - Needs dedicated student database models (`past_paper_attempts`, `tracker_cells`) to persist score percentages, mastery thresholds (Green >=75%, Amber 50-74.99%, Red <50%), and retake trajectory graphing.

3. **Learning Reflections & Media Progress:**
   - 3-state reflection workflow:
     1. *Read/watched and understood*
     2. *Read/watched but need to return later* (revision queue)
     3. *Read/watched but did not understand* (teacher notification queue)
   - 90% watch-time / completion threshold for video/audio.
   - Needs backend persistence table and API.

4. **Flashcards & Spaced Repetition:**
   - Front-end cards exist; needs student deck tracking (`flashcard_decks`, `flashcard_reviews`) where a deck is completed only when >=90% cards are classified as "Known".

5. **Subscription & Paywall Entitlement Engine:**
   - Model `orders` exists, but needs level-isolated subscription records (`subscriptions`, `subscription_plans`) with 3-month and 12-month terms in GBP (£) ensuring subscription to one assessment level does not grant access to another.

6. **Parent & Guardian Portal:**
   - Needs student-generated 6-character 48-hour invite code mechanism (`parent_invites`), parent-student relationship table (`parent_students`), and dedicated parent dashboard.

7. **Teacher, Tutor & School Multi-Tenancy:**
   - Role framework exists (`Role::ROLE_INSTRUCTOR`); needs explicit teacher assignment, marking workflow, student feedback, and school tenant isolation.

---

## 4. Missing Features to Implement

1. **Student Revision Queue & Gamification Engine:**
   - XP ledger table with anti-exploit duplicate prevention.
   - Daily study streak calculations using the student's configured timezone.
   - Exam countdown timer (official Cambridge session date countdown or custom student exam countdown).

2. **Resource Request Form Workflow:**
   - Database table `resource_requests` tracking missing educational resources requested by students.
   - Admin management screen and email alert trigger.

3. **Content Review & Publishing Pipeline:**
   - Multi-state workflow: Draft → Submitted for Review → Approved / Returned for Changes → Published.
   - Audit trail capturing author, reviewer, timestamps, version, and status.

4. **Secure File Delivery & PDF Watermarking:**
   - Watermarking student email and IP address on premium notes and worked solutions.

---

## 5. Security & Privacy Audit

- **Authentication & CBAC:** Server-side role enforcement is active. Client input must never be trusted for role escalation.
- **SQL Injection & CSRF:** Fully protected by Eloquent ORM and Laravel `@csrf` middleware.
- **Entitlement Isolation:** Ensure API routes reject unentitled access to premium past-paper packs.
- **Tenant Isolation:** Schools must never be able to query or inspect students outside their institution.

---

## 6. Implementation Phasing & Next Steps

- **Phase 1:** Academic Structure & Hierarchy Normalization (Assessment Levels, Examination Series, Components).
- **Phase 2:** Esther Math Past-Paper Revision Matrix & Attempt History Persistence.
- **Phase 3:** 3-State Learning Reflections, 90% Media Thresholds, and Spaced-Repetition Flashcards.
- **Phase 4:** Subscriptions & Paywall Entitlements (3-Month / 12-Month in GBP, Stripe/PayPal Webhooks).
- **Phase 5:** Parent Portal & Student Invite Codes (CMP-XXXXXX).
- **Phase 6:** Teacher Review Pipeline, Gamification (XP & Streaks), and Exam Countdown.
- **Phase 7:** End-to-End Testing & Verification.
