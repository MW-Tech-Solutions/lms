<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Context;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['is_system' => true]);
        $context = Context::first() ?? Context::create(['name' => 'System Global', 'context_type' => 'SYSTEM']);

        $sampleTeachers = [
            [
                'name' => 'Darren Treadway',
                'email' => 'darren.treadway@allthingscambridge.co.uk',
                'job_title' => 'Lead Cambridge Pure Math Examiner',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'approval_status' => 'APPROVED',
                'bio' => '15+ years marking Pure Mathematics 1-3 & Mechanics past papers.',
            ],
            [
                'name' => 'Tammi Allison',
                'email' => 'tammi.allison@allthingscambridge.co.uk',
                'job_title' => 'Senior Chemistry & Biology Specialist',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'approval_status' => 'APPROVED',
                'bio' => 'Cambridge International AS & A Level Chemistry syllabus author.',
            ],
            [
                'name' => 'Dr. Arthur Pendelton',
                'email' => 'arthur.pendelton@cambridge-apply.org',
                'job_title' => 'A-Level Physics Teacher Applicant',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                'approval_status' => 'PENDING',
                'bio' => 'PhD in Theoretical Physics; applied for Year 12-13 syllabus authoring.',
            ],
            [
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.jenkins@teach-uk.com',
                'job_title' => 'Primary Checkpoint Instructor Candidate',
                'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
                'approval_status' => 'UNAPPROVED',
                'bio' => 'Application submitted without Cambridge teacher certification documents.',
            ],
            [
                'name' => 'James Vance',
                'email' => 'james.vance@bogusmail.co.uk',
                'job_title' => 'Unverified Tutor Applicant',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=600&q=80',
                'approval_status' => 'REJECTED',
                'bio' => 'Rejected due to failure in identity and qualification verification.',
            ],
        ];

        foreach ($sampleTeachers as $t) {
            $user = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'tenant_id' => $tenant?->id,
                    'name' => $t['name'],
                    'password' => Hash::make('Password123!'),
                    'user_type' => 'EMPLOYEE',
                    'is_active' => $t['approval_status'] === 'APPROVED',
                    'approval_status' => $t['approval_status'],
                ]
            );

            $user->approval_status = $t['approval_status'];
            $user->save();

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'job_title' => $t['job_title'],
                    'avatar_url' => $t['avatar'],
                    'bio' => $t['bio'],
                ]
            );

            RoleAssignment::firstOrCreate([
                'user_id' => $user->id,
                'role_id' => $teacherRole->id,
                'context_id' => $context->id,
            ]);
        }
    }
}
