<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'department_id',
        'name',
        'email',
        'password',
        'user_type',
        'employee_id',
        'two_factor_secret',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isEmployee(): bool
    {
        return in_array($this->user_type, ['EMPLOYEE', 'HYBRID'], true);
    }

    public function isStudent(): bool
    {
        return in_array($this->user_type, ['STUDENT', 'HYBRID'], true);
    }

    public function hasRole(string|array $roleNames): bool
    {
        $roleNames = (array) $roleNames;
        return $this->roleAssignments()
            ->whereHas('role', fn ($q) => $q->whereIn('name', $roleNames))
            ->exists();
    }

    public function primaryRole(): string
    {
        $priorityOrder = [
            Role::SUPER_ADMIN,
            Role::MANAGER,
            Role::CORP_MANAGER,
            Role::COURSE_CREATOR,
            Role::TEACHER,
            Role::INSTRUCTOR,
            Role::TEACHER_NON_EDITING,
            Role::TRAINER,
            Role::LEARNER_EMPLOYEE,
            Role::LEARNER_STUDENT,
            Role::STUDENT,
            Role::GUEST,
            Role::AUTHENTICATED_USER,
        ];

        $roles = $this->roleAssignments->pluck('role.name')->filter()->values()->all();
        if (empty($roles)) {
            $roles = $this->roleAssignments()->with('role')->get()->pluck('role.name')->filter()->values()->all();
        }

        foreach ($priorityOrder as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return $this->user_type === 'EMPLOYEE' ? Role::LEARNER_EMPLOYEE : Role::STUDENT;
    }

    public function roleLabel(): string
    {
        $role = $this->primaryRole();
        $map = [
            Role::SUPER_ADMIN => 'Site Administrator',
            Role::MANAGER => 'Corporate HR Manager',
            Role::CORP_MANAGER => 'Corporate Manager',
            Role::COURSE_CREATOR => 'Course Creator',
            Role::TEACHER => 'Teacher (Editing)',
            Role::INSTRUCTOR => 'Academic Instructor',
            Role::TEACHER_NON_EDITING => 'Non-editing Teacher',
            Role::TRAINER => 'Workforce Trainer',
            Role::LEARNER_EMPLOYEE => 'Corporate Employee',
            Role::LEARNER_STUDENT => 'Academic Student',
            Role::STUDENT => 'Academic Student',
            Role::GUEST => 'Guest Visitor',
            Role::AUTHENTICATED_USER => 'Authenticated User',
        ];

        return $map[$role] ?? 'Learning Participant';
    }

    public function roleContext(): string
    {
        $role = $this->primaryRole();
        $map = [
            Role::SUPER_ADMIN => 'System Root CBAC Context',
            Role::MANAGER => 'Department & Category Context',
            Role::CORP_MANAGER => 'Corporate Compliance Context',
            Role::COURSE_CREATOR => 'Category & Course Context',
            Role::TEACHER => 'Course Authoring Context',
            Role::INSTRUCTOR => 'Faculty Instruction Context',
            Role::TEACHER_NON_EDITING => 'Course Activity Context',
            Role::TRAINER => 'Activity Facilitation Context',
            Role::LEARNER_EMPLOYEE => 'Corporate Learner Context',
            Role::LEARNER_STUDENT => 'Academic Scholar Context',
            Role::STUDENT => 'Student Cohort Context',
            Role::GUEST => 'Public Read-Only Context',
            Role::AUTHENTICATED_USER => 'General User Context',
        ];

        return $map[$role] ?? 'Standard User Context';
    }

    public function roleIcon(): string
    {
        $role = $this->primaryRole();
        $map = [
            Role::SUPER_ADMIN => 'bi-shield-shaded',
            Role::MANAGER => 'bi-briefcase-fill',
            Role::CORP_MANAGER => 'bi-briefcase-fill',
            Role::COURSE_CREATOR => 'bi-folder-plus',
            Role::TEACHER => 'bi-pencil-square',
            Role::INSTRUCTOR => 'bi-journal-code',
            Role::TEACHER_NON_EDITING => 'bi-person-check-fill',
            Role::TRAINER => 'bi-person-check-fill',
            Role::LEARNER_EMPLOYEE => 'bi-person-badge',
            Role::LEARNER_STUDENT => 'bi-mortarboard-fill',
            Role::STUDENT => 'bi-mortarboard-fill',
            Role::GUEST => 'bi-incognito',
            Role::AUTHENTICATED_USER => 'bi-person',
        ];

        return $map[$role] ?? 'bi-person-badge';
    }
}
