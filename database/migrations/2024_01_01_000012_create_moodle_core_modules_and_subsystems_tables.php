<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ALTER EXISTING TABLES FOR ADVANCED MOODLE CAPABILITIES
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'is_blind_marking')) {
                $table->boolean('is_blind_marking')->default(false)->after('max_points');
            }
            if (!Schema::hasColumn('assignments', 'marking_workflow_enabled')) {
                $table->boolean('marking_workflow_enabled')->default(true)->after('is_blind_marking');
            }
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_submissions', 'workflow_state')) {
                $table->string('workflow_state', 32)->default('RELEASED')->after('status');
            }
            if (!Schema::hasColumn('assignment_submissions', 'rubric_scores_json')) {
                $table->json('rubric_scores_json')->nullable()->after('workflow_state');
            }
            if (!Schema::hasColumn('assignment_submissions', 'inline_annotations_json')) {
                $table->json('inline_annotations_json')->nullable()->after('rubric_scores_json');
            }
        });

        Schema::table('grade_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('grade_categories', 'calculation_formula')) {
                $table->string('calculation_formula', 255)->nullable()->after('drop_lowest_count');
            }
        });

        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'safe_browser_enforced')) {
                $table->boolean('safe_browser_enforced')->default(false)->after('anti_cheat_flags');
            }
            if (!Schema::hasColumn('quizzes', 'adaptive_mode')) {
                $table->boolean('adaptive_mode')->default(false)->after('safe_browser_enforced');
            }
        });

        // 2. GROUPS & GROUPINGS SUBSYSTEM (Separate Groups vs Visible Groups)
        if (!Schema::hasTable('course_groups')) {
            Schema::create('course_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 128);
                $table->enum('group_mode', ['SEPARATE', 'VISIBLE'])->default('SEPARATE');
                $table->text('description')->nullable();
                $table->string('enrollment_key', 64)->nullable();
                $table->timestamps();

                $table->index(['course_id', 'group_mode']);
            });
        }

        if (!Schema::hasTable('course_group_members')) {
            Schema::create('course_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('course_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['group_id', 'user_id']);
            });
        }

        // 3. BADGES & COMPETENCIES SUBSYSTEM
        if (!Schema::hasTable('course_badges')) {
            Schema::create('course_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
                $table->string('name', 128);
                $table->text('description');
                $table->string('badge_image_url')->nullable();
                $table->string('issuer_name', 128)->default('Enterprise Academy');
                $table->enum('criteria_type', ['COURSE_COMPLETION', 'ACTIVITY_COMPLETION', 'GRADE_THRESHOLD', 'MANUAL']);
                $table->json('criteria_meta_json')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('badge_id')->constrained('course_badges')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('issued_at')->useCurrent();
                $table->string('verification_token', 64)->unique();
                $table->timestamps();

                $table->unique(['badge_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('course_competencies')) {
            Schema::create('course_competencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('idnumber', 64)->index();
                $table->string('framework_name', 128);
                $table->text('description');
                $table->timestamps();
            });
        }

        // 4. FORUM MODULE (mod_forum)
        if (!Schema::hasTable('course_forums')) {
            Schema::create('course_forums', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->enum('type', ['STANDARD', 'SINGLE_SIMPLE', 'Q_AND_A', 'EACH_USER'])->default('STANDARD');
                $table->text('intro')->nullable();
                $table->boolean('grade_enabled')->default(false);
                $table->decimal('grade_max', 5, 2)->default(100.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('forum_discussions')) {
            Schema::create('forum_discussions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('forum_id')->constrained('course_forums')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('name', 191);
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_locked')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('forum_posts')) {
            Schema::create('forum_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discussion_id')->constrained('forum_discussions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->cascadeOnDelete();
                $table->string('subject', 191);
                $table->text('message');
                $table->decimal('rating', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        // 5. LESSON MODULE (mod_lesson: Branching trees)
        if (!Schema::hasTable('course_lessons')) {
            Schema::create('course_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->decimal('grade_max', 5, 2)->default(100.00);
                $table->boolean('allow_retakes')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lesson_pages')) {
            Schema::create('lesson_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('course_lessons')->cascadeOnDelete();
                $table->string('title', 191);
                $table->text('content_html');
                $table->enum('page_type', ['CONTENT', 'QUESTION'])->default('CONTENT');
                $table->string('qtype', 32)->nullable(); // MULTICHOICE, TRUEFALSE, ESSAY
                $table->json('jumps_json')->nullable(); // Array of {answer_text, response_feedback, jump_to_page_id}
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 6. FEEDBACK MODULE (mod_feedback: Non-graded survey evaluations)
        if (!Schema::hasTable('course_feedbacks')) {
            Schema::create('course_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->boolean('is_anonymous')->default(true);
                $table->boolean('multiple_submit')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feedback_items')) {
            Schema::create('feedback_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feedback_id')->constrained('course_feedbacks')->cascadeOnDelete();
                $table->string('name', 191);
                $table->enum('typ', ['LABEL', 'TEXT', 'TEXTAREA', 'MULTICHOICE', 'NUMERIC'])->default('MULTICHOICE');
                $table->text('presentation')->nullable();
                $table->boolean('is_required')->default(true);
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feedback_responses')) {
            Schema::create('feedback_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feedback_id')->constrained('course_feedbacks')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('answers_json');
                $table->timestamp('completed_at')->useCurrent();
                $table->timestamps();
            });
        }

        // 7. CHOICE MODULE (mod_choice: Single-question polling)
        if (!Schema::hasTable('course_choices')) {
            Schema::create('course_choices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro');
                $table->boolean('allow_multiple')->default(false);
                $table->boolean('show_results')->default(true);
                $table->json('options_json'); // array of options [{id: 1, text: 'Option A'}]
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('choice_answers')) {
            Schema::create('choice_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('choice_id')->constrained('course_choices')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->json('selected_options_json');
                $table->timestamps();

                $table->unique(['choice_id', 'user_id']);
            });
        }

        // 8. H5P INTERACTIVE CONTENT MODULE (mod_h5p)
        if (!Schema::hasTable('course_h5p_packages')) {
            Schema::create('course_h5p_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('title', 191);
                $table->string('package_type', 64)->default('INTERACTIVE_VIDEO'); // INTERACTIVE_VIDEO, BRANCHING_SCENARIO, FLASHCARDS
                $table->string('h5p_library', 128)->nullable();
                $table->json('parameters_json')->nullable();
                $table->decimal('grade_max', 5, 2)->default(100.00);
                $table->timestamps();
            });
        }

        // 9. WORKSHOP MODULE (mod_workshop: Multi-phase peer review & self-assessment)
        if (!Schema::hasTable('course_workshops')) {
            Schema::create('course_workshops', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->enum('phase', ['SETUP', 'SUBMISSION', 'ASSESSMENT', 'EVALUATION', 'CLOSED'])->default('SETUP');
                $table->decimal('grade_submission_max', 5, 2)->default(80.00);
                $table->decimal('grade_assessment_max', 5, 2)->default(20.00);
                $table->enum('grading_strategy', ['ACCUMULATIVE', 'COMMENTS', 'RUBRIC'])->default('RUBRIC');
                $table->json('rubric_criteria_json')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('workshop_submissions')) {
            Schema::create('workshop_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workshop_id')->constrained('course_workshops')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title', 191);
                $table->text('content')->nullable();
                $table->string('attachment_url')->nullable();
                $table->decimal('grade_final', 5, 2)->nullable();
                $table->timestamps();

                $table->unique(['workshop_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('workshop_assessments')) {
            Schema::create('workshop_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('workshop_submissions')->cascadeOnDelete();
                $table->foreignId('reviewer_user_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('grade_given', 5, 2)->nullable();
                $table->text('feedback_comments')->nullable();
                $table->decimal('assessment_accuracy_grade', 5, 2)->nullable();
                $table->timestamps();

                $table->unique(['submission_id', 'reviewer_user_id']);
            });
        }

        // 10. CHAT MODULE (mod_chat: Real-time course chat)
        if (!Schema::hasTable('course_chats')) {
            Schema::create('course_chats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->timestamp('chat_time')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_id')->constrained('course_chats')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->timestamps();
            });
        }

        // 11. GLOSSARY & WIKI MODULES (mod_glossary, mod_wiki)
        if (!Schema::hasTable('course_glossaries')) {
            Schema::create('course_glossaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->boolean('is_global')->default(false);
                $table->boolean('allow_comments')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('glossary_entries')) {
            Schema::create('glossary_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('glossary_id')->constrained('course_glossaries')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('concept', 191)->index();
                $table->text('definition');
                $table->boolean('is_approved')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('course_wikis')) {
            Schema::create('course_wikis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('name', 191);
                $table->text('intro')->nullable();
                $table->string('first_page_title', 191)->default('First Page');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wiki_pages')) {
            Schema::create('wiki_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wiki_id')->constrained('course_wikis')->cascadeOnDelete();
                $table->string('title', 191);
                $table->longText('content_markdown');
                $table->foreignId('last_edited_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['wiki_id', 'title']);
            });
        }

        // 12. RESOURCE MODULES (Page, Book, File, Folder, URL, Label)
        if (!Schema::hasTable('course_resources')) {
            Schema::create('course_resources', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('section_id')->nullable()->constrained('course_sections')->nullOnDelete();
                $table->enum('resource_type', ['PAGE', 'BOOK', 'FILE', 'FOLDER', 'URL', 'LABEL'])->default('PAGE');
                $table->string('title', 191);
                $table->text('intro')->nullable();
                $table->longText('content_html')->nullable();
                $table->string('external_url', 512)->nullable();
                $table->string('file_path')->nullable();
                $table->json('files_json')->nullable(); // For mod_file and mod_folder
                $table->json('chapters_json')->nullable(); // For mod_book (chapter title & body)
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['course_id', 'resource_type']);
            });
        }

        // 13. SUBSYSTEM COLUMNS ON CORE TABLES
        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                if (!Schema::hasColumn('quizzes', 'safe_browser_enforced')) {
                    $table->boolean('safe_browser_enforced')->default(false)->after('passing_percentage');
                }
                if (!Schema::hasColumn('quizzes', 'adaptive_mode')) {
                    $table->boolean('adaptive_mode')->default(false)->after('safe_browser_enforced');
                }
                if (!Schema::hasColumn('quizzes', 'penalty_rate')) {
                    $table->decimal('penalty_rate', 4, 2)->default(0.00)->after('adaptive_mode');
                }
            });
        }

        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'is_blind_marking')) {
                    $table->boolean('is_blind_marking')->default(false)->after('max_points');
                }
                if (!Schema::hasColumn('assignments', 'marking_workflow_enabled')) {
                    $table->boolean('marking_workflow_enabled')->default(false)->after('is_blind_marking');
                }
                if (!Schema::hasColumn('assignments', 'rubric_json')) {
                    $table->json('rubric_json')->nullable()->after('marking_workflow_enabled');
                }
            });
        }

        if (Schema::hasTable('grade_categories')) {
            Schema::table('grade_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('grade_categories', 'calculation_formula')) {
                    $table->string('calculation_formula', 255)->nullable()->after('drop_lowest_count');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_resources');
        Schema::dropIfExists('wiki_pages');
        Schema::dropIfExists('course_wikis');
        Schema::dropIfExists('glossary_entries');
        Schema::dropIfExists('course_glossaries');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('course_chats');
        Schema::dropIfExists('workshop_assessments');
        Schema::dropIfExists('workshop_submissions');
        Schema::dropIfExists('course_workshops');
        Schema::dropIfExists('course_h5p_packages');
        Schema::dropIfExists('choice_answers');
        Schema::dropIfExists('course_choices');
        Schema::dropIfExists('feedback_responses');
        Schema::dropIfExists('feedback_items');
        Schema::dropIfExists('course_feedbacks');
        Schema::dropIfExists('lesson_pages');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_discussions');
        Schema::dropIfExists('course_forums');
        Schema::dropIfExists('course_competencies');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('course_badges');
        Schema::dropIfExists('course_group_members');
        Schema::dropIfExists('course_groups');
    }
};
