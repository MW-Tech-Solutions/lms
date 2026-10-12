@extends('layouts.admin')

@section('title', 'CBAC User & Role Directory')

@section('content')
<div class="container-fluid" x-data="{ showAssignModal: false, selectedUserId: null, selectedUserName: '' }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_users_manager'] ?? 'Context-Based Access Control (CBAC) Directory' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_users_manager'] ?? 'Manage multi-tenant user personas, department hierarchies, and 5-tier context-aware role assignments.' }}</div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>User Personas &amp; CBAC Permissions</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>User Type</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Active Roles &amp; Contexts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $user->name }}</div>
                                <div class="small text-muted">{{ $user->email }}</div>
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
                            <td>{{ $user->department?->name ?? 'Corporate Staff' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roleAssignments as $assignment)
                                        <span class="badge bg-secondary font-mono small">
                                            {{ $assignment->role->name }} @ ({{ $assignment->context->context_level }})
                                        </span>
                                    @empty
                                        <span class="text-muted small">Default Learner</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        @click="
                                            selectedUserId = {{ $user->id }};
                                            selectedUserName = '{{ addslashes($user->name) }}';
                                            showAssignModal = true;
                                        ">
                                    <i class="bi bi-shield-plus me-1"></i> Assign Role
                                </button>
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
    </div>

    <!-- Role Assignment Modal -->
    <div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" :class="{ 'show': showAssignModal }" :style="showAssignModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" x-cloak @click.self="showAssignModal = false" @keydown.escape.window="showAssignModal = false">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-2">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Assign CBAC Role</h5>
                    <button type="button" class="btn-close" @click="showAssignModal = false" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.assign-role') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" :value="selectedUserId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">User</label>
                            <input type="text" class="form-control" :value="selectedUserName" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Target Role</label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Target Context Hierarchy</label>
                            <select name="context_id" class="form-select" required>
                                @foreach($contexts as $ctx)
                                    <option value="{{ $ctx->id }}">
                                        Level: {{ $ctx->context_level }} &bull; Path: {{ $ctx->path }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Permissions inherit upward along the materialized path to SYSTEM root.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="showAssignModal = false">Cancel</button>
                        <button type="submit" class="btn btn-accent px-4">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
