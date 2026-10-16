@extends('layouts.admin')

@section('title', 'Contact Messages & Support Inquiries')

@section('content')
<div class="container-fluid px-4 py-4" x-data="{ 
    liveSearch: '{{ addslashes($search ?? '') }}',
    replyModalOpen: false,
    currentMessage: @js($selectedMessage)
}">

    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Engagement &amp; Support</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-chat-dots-fill text-info me-2"></i> Contact Inquiries &amp; Email Support Desk
            </h1>
            <p class="text-secondary small mb-0">
                Review inquiries sent from the contact portal, manage inquiry statuses, and send direct email replies in an interactive chat-style conversation view.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('contact') }}" target="_blank" class="tj-btn-outline py-2 px-3 small shadow-sm text-decoration-none">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Public Contact Page
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--lms-surface-card);">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                
                <!-- Status Pills -->
                <ul class="nav nav-pills gap-1.5 flex-nowrap overflow-x-auto pb-1 pb-md-0" style="scrollbar-width: thin;">
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $statusFilter === 'ALL' ? 'active' : '' }}" 
                           href="{{ route('admin.contact.index', ['status' => 'ALL', 'search' => $search]) }}">
                            All Messages <span class="badge bg-light text-dark ms-1">{{ $counts['all'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $statusFilter === 'PENDING' ? 'active' : '' }}" 
                           href="{{ route('admin.contact.index', ['status' => 'PENDING', 'search' => $search]) }}">
                            <i class="bi bi-clock-history me-1"></i> Pending <span class="badge bg-danger-subtle text-danger ms-1">{{ $counts['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $statusFilter === 'RESPONDED' ? 'active' : '' }}" 
                           href="{{ route('admin.contact.index', ['status' => 'RESPONDED', 'search' => $search]) }}">
                            <i class="bi bi-check2-all me-1"></i> Responded <span class="badge bg-success-subtle text-success ms-1">{{ $counts['responded'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $statusFilter === 'ARCHIVED' ? 'active' : '' }}" 
                           href="{{ route('admin.contact.index', ['status' => 'ARCHIVED', 'search' => $search]) }}">
                            <i class="bi bi-archive me-1"></i> Archived <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $counts['archived'] }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Live Search Form -->
                <form method="GET" action="{{ route('admin.contact.index') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" x-model="liveSearch" class="form-control border-start-0" placeholder="Search sender, email, subject...">
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                    @if(!empty($search) || $statusFilter !== 'ALL')
                        <a href="{{ route('admin.contact.index') }}" class="btn btn-sm btn-link text-muted p-0 ms-1 text-decoration-none">Clear</a>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <!-- Main Inquiries Layout: 2 Columns (List & Chat Detail) -->
    <div class="row g-4">
        <!-- Messages Master List -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
                <div class="card-header border-0 py-3 px-3 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
                    <span class="fw-bold small text-uppercase font-mono text-muted">Inbox ({{ $messages->total() }} Inquiries)</span>
                    <span class="badge bg-light text-dark border">{{ $counts['pending'] }} Pending</span>
                </div>

                <div class="list-group list-group-flush" style="max-height: 650px; overflow-y: auto;">
                    @forelse($messages as $msg)
                        <a href="{{ route('admin.contact.index', ['selected' => $msg->id, 'status' => $statusFilter, 'search' => $search]) }}" 
                           class="list-group-item list-group-item-action p-3 border-bottom transition-all {{ ($selectedMessage?->id === $msg->id) ? 'bg-primary-subtle border-primary-subtle' : '' }}"
                           x-show="!liveSearch || '{{ strtolower(addslashes($msg->name . ' ' . $msg->email . ' ' . ($msg->subject ?? '') . ' ' . $msg->status)) }}'.includes(liveSearch.toLowerCase().trim())">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                    {{ $msg->name }}
                                </div>
                                <span class="badge rounded-pill px-2 py-0.5 small {{ $msg->status === 'PENDING' ? 'bg-danger text-white' : ($msg->status === 'RESPONDED' ? 'bg-success text-white' : 'bg-secondary text-white') }}" style="font-size: 0.65rem;">
                                    {{ $msg->status }}
                                </span>
                            </div>
                            <div class="small fw-semibold text-body-secondary text-truncate mb-1">
                                {{ $msg->subject ?? 'No Subject' }}
                            </div>
                            <div class="small text-secondary text-truncate mb-2" style="font-size: 0.76rem;">
                                {{ $msg->message }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted font-mono" style="font-size: 0.7rem;">
                                <span>{{ $msg->email }}</span>
                                <span>{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            No contact inquiries found matching filter.
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="p-2 border-top bg-light">
                        {{ $messages->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail & Chat Conversation Panel -->
        <div class="col-lg-7">
            @if($selectedMessage)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 d-flex flex-column" style="background: var(--lms-surface-card);">
                    
                    <!-- Chat Header -->
                    <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" 
                                 style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                                {{ strtoupper(substr($selectedMessage->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $selectedMessage->name }}</h5>
                                <div class="text-secondary small font-mono">
                                    <a href="mailto:{{ $selectedMessage->email }}" class="text-decoration-none text-muted">
                                        <i class="bi bi-envelope me-1"></i>{{ $selectedMessage->email }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <!-- Status Modifier Dropdown -->
                            <form action="{{ route('admin.contact.status', $selectedMessage->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="font-size: 0.78rem;">
                                    <option value="PENDING" {{ $selectedMessage->status === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="RESPONDED" {{ $selectedMessage->status === 'RESPONDED' ? 'selected' : '' }}>Responded</option>
                                    <option value="ARCHIVED" {{ $selectedMessage->status === 'ARCHIVED' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </form>

                            <!-- Delete Inquiry -->
                            <form action="{{ route('admin.contact.destroy', $selectedMessage->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Inquiry">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Conversation Body (Chat Thread) -->
                    <div class="card-body p-4 d-flex flex-column gap-4 flex-grow-1" style="background: #FAFAFA; min-height: 380px;">
                        
                        <!-- Inquiry Header Meta -->
                        <div class="p-3 bg-white rounded-3 border shadow-xs">
                            <div class="small text-muted font-mono mb-1">
                                Subject: <strong class="text-dark">{{ $selectedMessage->subject ?: 'General Academic Inquiry' }}</strong>
                            </div>
                            <div class="small text-secondary" style="font-size: 0.72rem;">
                                Received: {{ $selectedMessage->created_at->format('M d, Y @ H:i') }} ({{ $selectedMessage->created_at->diffForHumans() }})
                            </div>
                        </div>

                        <!-- 1. Incoming Message Bubble (User) -->
                        <div class="d-flex gap-3 align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                 style="width: 36px; height: 36px; background: #64748B;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="p-3 rounded-4 bg-white border shadow-xs" style="max-width: 85%;">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                    <span class="fw-bold small text-dark">{{ $selectedMessage->name }}</span>
                                    <span class="text-muted font-mono" style="font-size: 0.68rem;">{{ $selectedMessage->created_at->format('H:i A') }}</span>
                                </div>
                                <p class="mb-0 text-dark" style="line-height: 1.7; font-size: 0.92rem; white-space: pre-wrap;">{{ $selectedMessage->message }}</p>
                            </div>
                        </div>

                        <!-- 2. Admin Outgoing Reply Bubble (Sent via Email) -->
                        @if($selectedMessage->admin_reply)
                            <div class="d-flex gap-3 align-items-start justify-content-end">
                                <div class="p-3 rounded-4 shadow-xs border" style="max-width: 85%; background: #EFF6FF; border-color: #BFDBFE !important;">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 small" style="font-size: 0.65rem;">
                                            EMAIL SENT TO {{ $selectedMessage->email }}
                                        </span>
                                        <span class="text-muted font-mono" style="font-size: 0.68rem;">
                                            {{ $selectedMessage->replied_at ? $selectedMessage->replied_at->format('M d, H:i A') : 'Sent' }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-dark" style="line-height: 1.7; font-size: 0.92rem; white-space: pre-wrap;">{{ $selectedMessage->admin_reply }}</p>
                                    <div class="small text-muted font-mono text-end" style="font-size: 0.7rem;">
                                        Replied by: <strong>{{ $selectedMessage->responder?->name ?? 'Administrator' }}</strong>
                                    </div>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                     style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border rounded-3 p-3 text-center my-auto">
                                <i class="bi bi-reply text-muted fs-3 d-block mb-1"></i>
                                <span class="small text-muted">No response dispatched yet. Use the chat response box below to compose an email reply to this user.</span>
                            </div>
                        @endif

                    </div>

                    <!-- Chat Message Input & Email Dispatcher -->
                    <div class="card-footer border-top bg-white p-3">
                        <form method="POST" action="{{ route('admin.contact.reply', $selectedMessage->id) }}">
                            @csrf
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        <i class="bi bi-reply-fill text-primary me-1"></i>
                                        Reply to {{ $selectedMessage->name }} (dispatches email to {{ $selectedMessage->email }})
                                    </label>
                                    <span class="badge bg-light text-muted border font-mono" style="font-size: 0.65rem;">SMTP Email Gateway</span>
                                </div>
                                <textarea name="admin_reply" rows="3" class="form-control" required placeholder="Type your response here... It will appear in this chat thread and be dispatched immediately to the user's email inbox.">{{ old('admin_reply', $selectedMessage->admin_reply) }}</textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted" style="font-size: 0.72rem;">User will receive email from support@allthingscambridge.com</span>
                                <button type="submit" class="tj-btn-primary px-4 py-2 small shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <i class="bi bi-send-fill"></i>
                                    <span>{{ $selectedMessage->admin_reply ? 'Send Updated Email' : 'Send Email Reply' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-chat-left-dots display-3 opacity-25 mb-3 text-primary"></i>
                    <h5 class="fw-bold text-dark">No Inquiry Selected</h5>
                    <p class="small text-secondary" style="max-width: 360px;">
                        Select a message from the inbox on the left to read full feedback details and dispatch an email response.
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
