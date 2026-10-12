<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseModule extends Model
{
    // 1. Core Assessment Modules
    public const TYPE_QUIZ = 'QUIZ';             // mod_quiz
    public const TYPE_ASSIGNMENT = 'ASSIGNMENT'; // mod_assign
    public const TYPE_LESSON = 'LESSON';         // mod_lesson (adaptive branching)
    public const TYPE_FEEDBACK = 'FEEDBACK';     // mod_feedback (surveys)
    public const TYPE_CHOICE = 'CHOICE';         // mod_choice (single question poll)

    // 2. Tracking, Interaction & Collaboration Modules
    public const TYPE_ATTENDANCE = 'ATTENDANCE'; // mod_attendance
    public const TYPE_FORUM = 'FORUM';           // mod_forum (4 modes)
    public const TYPE_H5P = 'H5P';               // mod_h5p (interactive rich content)
    public const TYPE_SCORM = 'SCORM';           // mod_scorm (1.2 & 2004)
    public const TYPE_WORKSHOP = 'WORKSHOP';     // mod_workshop (peer review)
    public const TYPE_CHAT = 'CHAT';             // mod_chat (real-time chat)
    public const TYPE_GLOSSARY = 'GLOSSARY';     // mod_glossary (encyclopedia)
    public const TYPE_WIKI = 'WIKI';             // mod_wiki (collaborative docs)
    public const TYPE_LIVE_SESSION = 'LIVE_SESSION';

    // 3. Content Delivery & Static Resource Modules
    public const TYPE_PAGE = 'PAGE';             // mod_page
    public const TYPE_BOOK = 'BOOK';             // mod_book (chapter TOC)
    public const TYPE_FILE = 'FILE';             // mod_file
    public const TYPE_FOLDER = 'FOLDER';         // mod_folder
    public const TYPE_URL = 'URL';               // mod_url
    public const TYPE_LABEL = 'LABEL';           // mod_label (text & media area)
    public const TYPE_VIDEO = 'VIDEO';

    protected $fillable = [
        'section_id',
        'module_type',
        'instance_id',
        'sort_order',
        'is_mandatory',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_mandatory' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
}
