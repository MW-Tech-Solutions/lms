@extends('layouts.admin')

@section('title', 'CBAC User & Role Directory')

@section('content')
<div class="container-fluid" 
     x-data="{ 
         showAddUserModal: false,
         showEditUserModal: false,
         showAssignModal: false,
         showRolesModal: false,
         
         // Edit User Data
         editUser: {
             id: null,
             name: '',
             email: '',
             user_type: 'EMPLOYEE',
             employee_id: '',
             department_id: '',
             is_active: true
         },
         
         // Assign Role Data
         selectedUserId: null,
         selectedUserName: '',
         userAssignments: [],
         
         openEditUser(user) {
             this.editUser = {
                 id: user.id,
                 name: user.name,
                 email: user.email,
                 user_type: user.user_type || 'EMPLOYEE',
                 employee_id: user.employee_id || '',
                 department_id: user.department_id || '',
                 is_active: Boolean(user.is_active)
             };
             this.showEditUserModal = true;
         },

         openAssignRole(userId, userName, assignments) {
             this.selectedUserId = userId;
             this.selectedUserName = userName;
             this.userAssignments = assignments || [];
             this.showAssignModal = true;
         }
     }">

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                    <i class="bi bi-people-fill text-purple me-1"></i> Identity & Access Directory
                </span>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small">
                    5-Tier CBAC Active
                </span>
            </div>
            <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                {{ $site_settings['title_users_manager'] ?? 'Context-Based Access Control (CBAC) Directory' }}
            </h2>
            <div class="text-secondary small">
                {{ $site_settings['subtitle_users_manager'] ?? 'Manage multi-tenant user personas, department hierarchies, and 5-tier context-aware role assignments.' }}
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Manage System Roles Modal Trigger -->
            <button class="tj-btn-outline py-2 px-3 small shadow-sm" type="button" @click="showRolesModal = true">
                <i class="bi bi-shield-shaded me-1"></i>
                <span>Manage System Roles</span>
            </button>

            <!-- Add New User Modal Trigger -->
            <button class="tj-btn-primary py-2 px-3 small shadow-sm" type="button" @click="showAddUserModal = true">
                <i class="bi bi-person-plus-fill me-1"></i>
                <span>Add New User</span>
            </button>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    <i class="bi bi-people-fill text-primary me-2"></i>User Personas &amp; CBAC Permissions
                </h5>
                <span class="text-muted small">Total Registered Identities: {{ $users->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">User Type</th>
                        <th class="py-3">Employee ID</th>
                        <th class="py-3">Department</th>
                        <th class="py-3">Active Roles &amp; Contexts</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" 
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, var(--lms-accent), #2563EB); font-size: 0.85rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="small text-muted font-mono" style="font-size: 0.78rem;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->user_type === 'EMPLOYEE')
                                    <span class="badge badge-compliance">EMPLOYEE</span>
                                @elseif($user->user_type === 'STUDENT')
                                    <span class="badge bg-info-subtle text-info">STUDENT</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary">HYBRID</span>
                                @endif
                            </td>
                            <td class="font-mono small">{{ $user->employee_id ?? 'N/A' }}</td>
                            <td>
                                <div class="small">
                                    <i class="bi bi-building text-muted me-1"></i>
                                    {{ $user->department?->name ?? 'Corporate Staff' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                    @forelse($user->roleAssignments as $assignment)
                                        <span class="badge bg-secondary-subtle text-secondary border font-mono small d-inline-flex align-items-center gap-1.5 py-1 px-2 rounded-pill">
                                            <span>{{ $assignment->role->name }} @ ({{ $assignment->context->context_level }})</span>
                                            <form action="{{ route('admin.users.remove-role') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Remove role {{ $assignment->role->name }} from {{ addslashes($user->name) }}?');">
                                                @csrf
                                                <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                                <button type="submit" class="border-0 bg-transparent p-0 text-danger" title="Remove Role Assignment">
                                                    <i class="bi bi-x-circle-fill" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </form>
                                        </span>
                                    @empty
                                        <span class="text-muted small">Default Learner</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Suspended</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex gap-1.5 justify-content-end">
                                    <!-- Edit User Button -->
                                    <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm"
                                            @click="openEditUser({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                user_type: '{{ $user->user_type }}',
                                                employee_id: '{{ addslashes($user->employee_id ?? '') }}',
                                                department_id: '{{ $user->department_id ?? '' }}',
                                                is_active: {{ $user->is_active ? 'true' : 'false' }}
                                            })"
                                            title="Edit Profile & Details">
                                        <i class="bi bi-pencil-square"></i>
                                        <span class="d-none d-md-inline">Edit</span>
                                    </button>

                                    <!-- Assign / Manage Role Button -->
                                    <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm"
                                            @click="openAssignRole(
                                                {{ $user->id }}, 
                                                '{{ addslashes($user->name) }}',
                                                {{ json_encode($user->roleAssignments->map(fn($a) => ['id' => $a->id, 'role_name' => $a->role->name, 'context_level' => $a->context->context_level])) }}
                                            )"
                                            title="Assign or Change Roles">
                                        <i class="bi bi-shield-plus"></i>
                                        <span>Roles</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No users registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>

    <!-- 1. ADD NEW USER MODAL -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showAddUserModal }" 
         :style="showAddUserModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showAddUserModal = false" 
         @keydown.escape.window="showAddUserModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent);">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Create New User Account</h5>
                    </div>
                    <button type="button" class="btn-close" @click="showAddUserModal = false"></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required placeholder="john.doe@organization.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required placeholder="Min. 6 characters" minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">User Type <span class="text-danger">*</span></label>
                                <select name="user_type" class="form-select" required>
                                    <option value="EMPLOYEE">EMPLOYEE (Corporate / Compliance)</option>
                                    <option value="STUDENT">STUDENT (Degree Scholar / Academic)</option>
                                    <option value="HYBRID">HYBRID (Faculty / Admin Dual Access)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Employee ID (Optional)</label>
                                <input type="text" name="employee_id" class="form-control font-mono" placeholder="EMP-2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Department Hierarchy</label>
                                <select name="department_id" class="form-select">
                                    <option value="">None (General Staff)</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 mt-4 pt-3 border-top">
                                <h6 class="fw-bold small text-uppercase text-secondary font-mono mb-2">Initial CBAC Role Assignment (Optional)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Initial Canonical Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="">No special role (Learner default)</option>
                                    @php
                                        $moodleMeta = \App\Models\Role::getMoodleRolesList();
                                    @endphp
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $moodleMeta[$role->name]['label'] ?? $role->name }} ({{ $role->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Context Level</label>
                                <select name="context_id" class="form-select">
                                    <option value="">Select context...</option>
                                    @foreach($contexts as $ctx)
                                        <option value="{{ $ctx->id }}">Level: {{ $ctx->context_level }} &bull; Path: {{ $ctx->path }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showAddUserModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">Create User Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. EDIT USER MODAL -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showEditUserModal }" 
         :style="showEditUserModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showEditUserModal = false" 
         @keydown.escape.window="showEditUserModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Edit User: <span x-text="editUser.name"></span></h5>
                    </div>
                    <button type="button" class="btn-close" @click="showEditUserModal = false"></button>
                </div>
                <form :action="'{{ url('/admin/users') }}/' + editUser.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required x-model="editUser.name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required x-model="editUser.email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">User Type <span class="text-danger">*</span></label>
                                <select name="user_type" class="form-select" required x-model="editUser.user_type">
                                    <option value="EMPLOYEE">EMPLOYEE (Corporate / Compliance)</option>
                                    <option value="STUDENT">STUDENT (Degree Scholar / Academic)</option>
                                    <option value="HYBRID">HYBRID (Faculty / Admin Dual Access)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control font-mono" x-model="editUser.employee_id">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Department Hierarchy</label>
                                <select name="department_id" class="form-select" x-model="editUser.department_id">
                                    <option value="">None (General Staff)</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Reset Password (Optional)</label>
                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" value="1" id="editUserActive" x-model="editUser.is_active">
                                    <label class="form-check-label fw-bold" for="editUserActive">
                                        Account Active &amp; Operational
                                    </label>
                                    <div class="text-muted small">Suspended users cannot authenticate or access course materials.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showEditUserModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">Save User Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. ASSIGN / CHANGE ROLES MODAL -->
    <div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showAssignModal }" 
         :style="showAssignModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showAssignModal = false" 
         @keydown.escape.window="showAssignModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(5, 150, 105, 0.12); color: #059669;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">
                            Manage Roles: <span x-text="selectedUserName"></span>
                        </h5>
                    </div>
                    <button type="button" class="btn-close" @click="showAssignModal = false"></button>
                </div>

                <!-- Existing Assigned Roles List -->
                <div class="modal-body py-3">
                    <div class="mb-4 p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        <div class="fw-bold small text-uppercase font-mono text-secondary mb-2">
                            <i class="bi bi-list-check me-1"></i> Current Role Assignments
                        </div>
                        <template x-if="userAssignments.length === 0">
                            <div class="text-muted small">No explicit CBAC roles assigned. User operates under default learner permissions.</div>
                        </template>
                        <div class="d-flex flex-wrap gap-2">
                            <template x-for="item in userAssignments" :key="item.id">
                                <div class="badge bg-white text-dark border p-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                                    <div>
                                        <strong x-text="item.role_name"></strong>
                                        <span class="text-muted font-mono" style="font-size: 0.68rem;" x-text="'@ ' + item.context_level"></span>
                                    </div>
                                    <form action="{{ route('admin.users.remove-role') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Revoke this role?');">
                                        @csrf
                                        <input type="hidden" name="assignment_id" :value="item.id">
                                        <button type="submit" class="border-0 bg-transparent text-danger p-0" title="Revoke Role">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Assign New Role Form -->
                    <form action="{{ route('admin.users.assign-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" :value="selectedUserId">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="fw-bold small text-uppercase text-secondary font-mono mb-1">
                                    <i class="bi bi-plus-circle me-1"></i> Assign New Canonical Role
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Select Role</label>
                                <select name="role_id" class="form-select" required>
                                    @php
                                        $moodleMeta = \App\Models\Role::getMoodleRolesList();
                                    @endphp
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $moodleMeta[$role->name]['label'] ?? $role->name }} ({{ $role->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Target Context Hierarchy</label>
                                <select name="context_id" class="form-select" required>
                                    @foreach($contexts as $ctx)
                                        <option value="{{ $ctx->id }}">
                                            Level: {{ $ctx->context_level }} &bull; Path: {{ $ctx->path }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small" style="font-size: 0.72rem;">Permissions cascade along the materialized path.</div>
                            </div>
                            <div class="col-12 mt-3 text-end">
                                <button type="button" class="btn btn-secondary me-2" @click="showAssignModal = false">Close</button>
                                <button type="submit" class="tj-btn-primary px-4 py-2">Add Role Assignment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MANAGE SYSTEM ROLES & CBAC REGISTRY MODAL -->
    <div class="modal fade" id="manageRolesModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showRolesModal }" 
         :style="showRolesModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showRolesModal = false" 
         @keydown.escape.window="showRolesModal = false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent);">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Canonical Roles &amp; CBAC Registry</h5>
                    </div>
                    <button type="button" class="btn-close" @click="showRolesModal = false"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-secondary small mb-3">
                        Moodle-compatible Context-Based Access Control (CBAC) allows users to hold specific roles within designated system contexts (System, Tenant, Department, Category, Course, Activity).
                    </p>

                    <!-- Roles List -->
                    <div class="list-group list-group-flush mb-4">
                        @php
                            $moodleMeta = \App\Models\Role::getMoodleRolesList();
                        @endphp
                        @foreach($roles as $role)
                            @php
                                $meta = $moodleMeta[$role->name] ?? null;
                            @endphp
                            <div class="list-group-item p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between" style="background: var(--lms-surface-card);">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold text-dark">{{ $meta['label'] ?? $role->name }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary font-mono small">{{ $role->name }}</span>
                                        @if($role->is_system)
                                            <span class="badge bg-primary-subtle text-primary small">SYSTEM CORE</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success small">CUSTOM</span>
                                        @endif
                                    </div>
                                    <div class="text-secondary small">
                                        {{ $meta['desc'] ?? 'Custom institutional role configured in CBAC registry.' }}
                                    </div>
                                    <div class="text-muted font-mono mt-1" style="font-size: 0.72rem;">
                                        Default Scope: {{ $meta['context'] ?? 'System / Course' }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border font-mono px-3 py-2 rounded-pill flex-shrink-0">
                                    {{ $role->role_assignments_count ?? 0 }} Users
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Register Custom Role Form -->
                    <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        <h6 class="fw-bold small text-uppercase font-mono text-secondary mb-2">Register New Custom Role</h6>
                        <form action="{{ route('admin.users.store-role') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="name" class="form-control" required placeholder="e.g. COMPLIANCE_OFFICER" style="text-transform: uppercase;">
                            <button type="submit" class="tj-btn-primary px-3 py-1.5 text-nowrap">Register Role</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" @click="showRolesModal = false">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
