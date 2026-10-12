<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Canonical Moodle CBAC Roles
    public const SUPER_ADMIN = 'SUPER_ADMIN';
    public const MANAGER = 'MANAGER';
    public const COURSE_CREATOR = 'COURSE_CREATOR';
    public const TEACHER = 'TEACHER';
    public const TEACHER_NON_EDITING = 'TEACHER_NON_EDITING';
    public const STUDENT = 'STUDENT';
    public const GUEST = 'GUEST';
    public const AUTHENTICATED_USER = 'AUTHENTICATED_USER';

    // Aliases & Enterprise Mappings
    public const CORP_MANAGER = 'CORP_MANAGER';
    public const INSTRUCTOR = 'INSTRUCTOR';
    public const TRAINER = 'TRAINER';
    public const LEARNER_EMPLOYEE = 'LEARNER_EMPLOYEE';
    public const LEARNER_STUDENT = 'LEARNER_STUDENT';

    protected $fillable = [
        'name',
        'is_system',
    ];

    public static function getMoodleRolesList(): array
    {
        return [
            self::SUPER_ADMIN => [
                'label' => 'Site Administrator',
                'context' => 'SYSTEM',
                'description' => 'Total control over server, plugins, security policies, and capabilities bypass.',
                'badge' => 'bg-danger text-white',
            ],
            self::MANAGER => [
                'label' => 'Manager',
                'context' => 'SYSTEM / CATEGORY',
                'description' => 'Delegated administrator: creates categories/courses, assigns roles, reviews reports.',
                'badge' => 'bg-primary text-white',
            ],
            self::COURSE_CREATOR => [
                'label' => 'Course Creator',
                'context' => 'SYSTEM / CATEGORY',
                'description' => 'Can create courses within designated categories and automatically becomes Editing Teacher.',
                'badge' => 'bg-info text-dark',
            ],
            self::TEACHER => [
                'label' => 'Teacher (Editing)',
                'context' => 'COURSE',
                'description' => 'Designs instruction, adds/edits activities/resources, manages gradebook and completions.',
                'badge' => 'bg-success text-white',
            ],
            self::TEACHER_NON_EDITING => [
                'label' => 'Non-editing Teacher',
                'context' => 'COURSE / ACTIVITY',
                'description' => 'Facilitates instruction: grades submissions, marks attendance, moderates forums without altering structure.',
                'badge' => 'bg-warning text-dark',
            ],
            self::STUDENT => [
                'label' => 'Student',
                'context' => 'COURSE / ACTIVITY',
                'description' => 'Consumes learning materials, attempts quizzes, submits assignments, views grades.',
                'badge' => 'bg-secondary text-white',
            ],
            self::GUEST => [
                'label' => 'Guest',
                'context' => 'SYSTEM / COURSE',
                'description' => 'Read-only access to open courses. Cannot post, attempt quizzes, or earn grades.',
                'badge' => 'bg-light text-dark border',
            ],
            self::AUTHENTICATED_USER => [
                'label' => 'Authenticated User',
                'context' => 'SYSTEM (Logged In)',
                'description' => 'Baseline logged-in profile: dashboard, personal profile, notification preferences.',
                'badge' => 'bg-dark text-white',
            ],
        ];
    }

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }
}
