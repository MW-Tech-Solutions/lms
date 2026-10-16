# Database Migrations Documentation — AllThingsCambridge LMS

## 1. Overview
The database architecture was safely extended on MariaDB/MySQL (`enterprise_lms` running on port 3308) to support the Cambridge International curriculum hierarchy, revision tracker, spaced repetition flashcards, multi-tier British Pound subscriptions, and parent-student relationship linking.

All existing tables, user records, course modules, SCORM telemetry, and compliance certifications have been strictly preserved.

---

## 2. Migration Execution History
| Migration File | Description | Execution Status |
| :--- | :--- | :--- |
| `2026_10_15_102235_create_cambridge_academic_and_revision_system_tables.php` | Creates 12 Cambridge LMS relational tables | Applied Successfully (Batch 2) |

---

## 3. Schema Definitions

### 3.1 `cambridge_exam_series`
Stores official Cambridge examination sessions across academic cycles.
- `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
- `year` (INT UNSIGNED, e.g. 2023–2026)
- `season` (ENUM: `F/M` [Feb/March], `M/J` [May/June], `O/N` [Oct/Nov])
- `is_active` (BOOLEAN, default: 1)
- `created_at`, `updated_at` (TIMESTAMP)
- **Unique Key**: `(`year`, `season`)`

### 3.2 `cambridge_paper_components`
Breakdown of syllabus components (papers).
- `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
- `course_id` (BIGINT UNSIGNED, Foreign Key -> `courses.id`, Cascades on delete)
- `code` (VARCHAR 20, e.g. `P1`, `P3`, `M1`, `S1`)
- `name` (VARCHAR 150, e.g. `Pure Mathematics 1`)
- `max_marks` (DECIMAL 5,2, e.g. 75.00)
- `duration_minutes` (INT UNSIGNED, default: 110)
- `sort_order` (INT UNSIGNED, default: 0)
- `created_at`, `updated_at` (TIMESTAMP)

### 3.3 `past_paper_attempts` (Esther Math Tracker)
Records every student attempt on past-paper papers with dynamic scoring.
- `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
- `user_id` (BIGINT UNSIGNED, Foreign Key -> `users.id`, Cascades on delete)
- `course_id` (BIGINT UNSIGNED, Foreign Key -> `courses.id`, Cascades on delete)
- `component_id` (BIGINT UNSIGNED, Foreign Key -> `cambridge_paper_components.id`, Cascades on delete)
- `year` (INT UNSIGNED)
- `season` (VARCHAR 10)
- `variant` (TINYINT UNSIGNED, default: 1)
- `score_obtained` (DECIMAL 5,2)
- `max_score` (DECIMAL 5,2)
- `percentage` (DECIMAL 5,2)
- `status_color` (ENUM: `GREEN`, `AMBER`, `RED`)
- `notes` (TEXT, Nullable)
- `attempt_number` (INT UNSIGNED, default: 1)
- `is_latest` (BOOLEAN, default: 1)
- `created_at`, `updated_at` (TIMESTAMP)
- **Indexes**: `(`user_id`, `course_id`)`, `(`year`, `season`, `component_id`)`

### 3.4 `learning_reflections`
3-state student reflection feedback on notes and media lectures.
- `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
- `user_id` (BIGINT UNSIGNED, Foreign Key -> `users.id`, Cascades on delete)
- `course_id` (BIGINT UNSIGNED, Foreign Key -> `courses.id`, Cascades on delete)
- `module_id` (BIGINT UNSIGNED, Foreign Key -> `course_modules.id`, Nullable)
- `reflection_choice` (ENUM: `UNDERSTOOD`, `NEED_RETURN`, `NOT_UNDERSTOOD`)
- `watch_percentage` (DECIMAL 5,2, default: 100.00)
- `is_flagged_for_teacher` (BOOLEAN, default: 0)
- `student_comment` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

### 3.5 Spaced Repetition Flashcard System
- `flashcard_decks`: Topic-level decks (`course_id`, `title`, `description`, `is_published`).
- `flashcards`: Cards in deck (`deck_id`, `question_front`, `answer_back`, `latex_front`, `latex_back`).
- `flashcard_reviews`: Review logs (`user_id`, `card_id`, `review_status`: `KNOWN`/`NOT_YET_KNOWN`, `interval_days`, `next_review_at`).

### 3.6 `subscription_plans` & `user_subscriptions`
Assessment level passes in British Pounds (`GBP` / `£`).
- `subscription_plans`: `category_id`, `name`, `duration_term` (`3_MONTHS`, `12_MONTHS`), `price_gbp`, `is_active`.
- `user_subscriptions`: `user_id`, `plan_id`, `category_id`, `status` (`ACTIVE`, `PAST_DUE`, `CANCELED`, `EXPIRED`), `gateway`, `gateway_subscription_id`, `starts_at`, `ends_at`.

### 3.7 `parent_invites` & `parent_students`
Zero-trust Guardian oversight links.
- `parent_invites`: `student_id`, `invite_code` (VARCHAR 16, unique), `is_claimed`, `expires_at` (48 hours from issuance).
- `parent_students`: `parent_id`, `student_id`, `relationship`, `can_view_tracker` (default: 1), `can_view_billing` (default: 1).

### 3.8 `user_xp_ledger`
Anti-exploit gamification log.
- `id`, `user_id`, `xp_amount`, `activity_type`, `idempotency_key` (VARCHAR 100, unique), `description`, `created_at`, `updated_at`.

### 3.9 `resource_requests`
Intake table for student requests for unuploaded materials.
- `id`, `user_id`, `level_name`, `subject`, `topic`, `resource_type`, `details`, `status` (`PENDING`, `IN_REVIEW`, `FULFILLED`, `REJECTED`).

---

## 4. Seeder Artifacts
`database/seeders/CambridgeAcademicDataSeeder.php` seeded:
1. 11 Cambridge examination series (2023–2026 F/M, M/J, O/N).
2. 4 components for Cambridge International A-Level Mathematics (9709).
3. 10 Subscription Plans in GBP (£) covering all 5 assessment levels in 3-Month and 12-Month options.
