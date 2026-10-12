<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('lms_theme') === 'dark',
          sidebarOpen: window.innerWidth >= 992,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('lms_theme', this.darkMode ? 'dark' : 'light');
              document.documentElement.setAttribute('data-bs-theme', this.darkMode ? 'dark' : 'light');
          }
      }"
      x-init="document.documentElement.setAttribute('data-bs-theme', darkMode ? 'dark' : 'light');"
      :data-bs-theme="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Hub') - {{ $site_settings['brand_name'] ?? 'Enterprise Academy' }}</title>

    <!-- Favicon from Dynamic Brand Settings -->
    <link rel="icon" type="image/x-icon" href="{{ !empty($site_settings['brand_favicon_url']) ? $site_settings['brand_favicon_url'] : asset('favicon.ico') }}">

    <!-- Google Fonts: Manrope (Body) & Urbanist (Headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Urbanist:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}">

    <!-- Dynamic Theme Variables from CMS (Overrides Entire System) -->
    <style>
        :root {
            --lms-primary: {{ $site_settings['brand_primary_color'] ?? '#0F1321' }};
            --lms-accent: {{ $site_settings['brand_accent_color'] ?? '#723EC3' }};
            --lms-accent-hover: {{ $site_settings['brand_accent_hover_color'] ?? '#5E32A6' }};
            --tj-color-theme-primary: {{ $site_settings['brand_accent_color'] ?? '#723EC3' }};
            --tj-color-theme-primary-hover: {{ $site_settings['brand_accent_hover_color'] ?? '#5E32A6' }};
            --tj-color-theme-primary-rgb: {{ $site_settings['brand_accent_rgb'] ?? '114, 62, 195' }};
            --lms-bg-secondary: {{ $site_settings['brand_secondary_color'] ?? '#F8F9FD' }};
        }
        [data-bs-theme="dark"] {
            --lms-bg-body: #0A0D16;
            --lms-bg-secondary: #0F1322 !important;
            --lms-surface-card: #151928;
            --lms-border: rgba(255, 255, 255, 0.08);
            --lms-text-primary: #F8FAFC;
            --lms-text-secondary: #CBD5E1;
            --lms-text-muted: #94A3B8;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body style="background-color: var(--lms-bg-body);">

    <!-- 1. EDUNEX ADMIN TOPBAR -->
    <header class="admin-navbar sticky-top px-3 px-xl-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light btn-sm rounded-circle p-2 border" @click="sidebarOpen = !sidebarOpen" type="button" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold m-0" href="{{ route('admin.dashboard') }}">
                <span class="badge rounded-3 p-2 shadow-sm" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1.1rem; color: #FFF;">
                    <i class="bi bi-building-gear"></i>
                </span>
                <div class="d-flex flex-column">
                    <span style="font-family: var(--lms-font-heading); font-size: 1.2rem; font-weight: 800; color: var(--lms-text-primary); letter-spacing: -0.5px; line-height: 1;">
                        {{ $site_settings['brand_name'] ?? 'Enterprise Academy' }}
                    </span>
                    <span class="text-muted" style="font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">
                        Enterprise Console &bull; CBAC Engine
                    </span>
                </div>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Public Site Link -->
            <a href="{{ url('/') }}" class="tj-btn-outline py-1.5 px-3 small d-none d-md-inline-flex" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>
                <span>View Public Site</span>
            </a>

            <!-- Dark / Light Theme Toggle -->
            <button class="btn btn-light btn-sm rounded-circle p-2 border" @click="toggleTheme()" type="button" title="Toggle Dark/Light Mode">
                <i :class="darkMode ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill text-primary'"></i>
            </button>

            <!-- Authenticated User Profile Pill -->
            @auth
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-pill border px-3 py-1.5 d-flex align-items-center gap-2 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" 
                             style="width: 28px; height: 28px; background: linear-gradient(135deg, var(--lms-accent), #2563EB); font-size: 0.75rem;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-bold small text-truncate" style="max-width: 130px; line-height: 1.1;">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted" style="font-size: 0.65rem;">
                                {{ Auth::user()->user_type ?? 'Admin' }}
                            </div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="background: var(--lms-surface-card); min-width: 220px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold small">{{ Auth::user()->name }}</div>
                            <div class="text-muted small font-mono" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                            <span class="badge badge-purple-soft rounded-pill mt-1" style="font-size: 0.65rem;">
                                {{ Auth::user()->employee_id ?? 'ROOT PRIVILEGED' }}
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded-2 mt-1 small" href="{{ route('my-learning') }}">
                                <i class="bi bi-person-workspace text-primary me-2"></i>My Learner View
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded-2 small" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-sliders text-secondary me-2"></i>System Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2 rounded-2 small">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out Console
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </header>

    <!-- 2. ADMIN LAYOUT WORKSPACE -->
    <!-- 2. ADMIN LAYOUT WORKSPACE -->
    <div class="d-flex" style="min-height: calc(100vh - 72px); position: relative;">
        <!-- Mobile Sidebar Backdrop -->
        <div class="modal-backdrop fade show d-lg-none" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak style="z-index: 1045;"></div>

        <!-- Role-Specific Dedicated Sidebar Navigation -->
        @php
            $user = Auth::user();
            $role = $user ? $user->primaryRole() : 'GUEST';
            $isSuperAdmin = in_array($role, ['SUPER_ADMIN'], true);
            $isManager = in_array($role, ['MANAGER', 'CORP_MANAGER'], true);
            $isCreator = in_array($role, ['COURSE_CREATOR'], true);
            $isTeacher = in_array($role, ['TEACHER', 'INSTRUCTOR'], true);
            $isTeacherNonEdit = in_array($role, ['TEACHER_NON_EDITING', 'TRAINER'], true);
        @endphp

        <aside class="admin-sidebar p-3" x-show="sidebarOpen" x-transition>
            
            <!-- Active Persona Badge Pill -->
            <div class="p-2.5 rounded-3 mb-3 border d-flex align-items-center gap-2" 
                 style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 32px; height: 32px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                    <i class="bi {{ $user ? $user->roleIcon() : 'bi-person-badge' }}"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="fw-bold small text-truncate" style="font-size: 0.82rem; color: var(--lms-text-primary);">
                        {{ $user ? $user->roleLabel() : 'System User' }}
                    </div>
                    <div class="text-muted text-truncate font-mono" style="font-size: 0.65rem;">
                        {{ $user ? $user->roleContext() : 'Session Active' }}
                    </div>
                </div>
            </div>

            {{-- =========================================================================
                 1. SUPER ADMINISTRATOR DEDICATED SIDEBAR (FULL SYSTEM CONTROL)
                 ========================================================================= --}}
            @if($isSuperAdmin)
                <div class="sidebar-heading text-nowrap">System &amp; Executive Core</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-speedometer2"></i></div>
                            <span class="text-nowrap">Executive Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.compliance*') ? 'active' : '' }}" href="{{ route('admin.compliance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-shield-check"></i></div>
                            <span class="text-nowrap">Compliance &amp; Audits</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('admin.compliance.export-csv') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                            <span class="text-nowrap">Audit Trail Export</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Academic Subsystems &amp; Engine</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-journal-richtext"></i></div>
                            <span class="text-nowrap">Course Curriculum</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.scorm*') ? 'active' : '' }}" href="{{ route('admin.scorm.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-box-seam"></i></div>
                            <span class="text-nowrap">SCORM 1.2/2004 Registry</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-patch-question"></i></div>
                            <span class="text-nowrap">Assessments &amp; Anti-Cheat</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-calculator"></i></div>
                            <span class="text-nowrap">Weighted Gradebook</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-qr-code-scan"></i></div>
                            <span class="text-nowrap">Rolling QR Attendance</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Platform &amp; Identity Administration</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-people"></i></div>
                            <span class="text-nowrap">CBAC Users &amp; Roles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.cms*') ? 'active' : '' }}" href="{{ route('admin.cms.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-layers"></i></div>
                            <span class="text-nowrap">Headless CMS Engine</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-sliders"></i></div>
                            <span class="text-nowrap">Site Dynamic Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ url('/run-setup.php') }}" target="_blank">
                            <div class="sidebar-icon-box"><i class="bi bi-tools"></i></div>
                            <span class="text-nowrap">Server Maintenance Console</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Live Environment Switcher</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-person-workspace"></i></div>
                            <span class="text-nowrap">Learner Experience View</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('courses.index') }}" target="_blank">
                            <div class="sidebar-icon-box"><i class="bi bi-grid"></i></div>
                            <span class="text-nowrap">Public Course Catalog</span>
                        </a>
                    </li>
                </ul>

            {{-- =========================================================================
                 2. CORPORATE / HR MANAGER DEDICATED SIDEBAR
                 ========================================================================= --}}
            @elseif($isManager)
                <div class="sidebar-heading text-nowrap">Workforce Management</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-speedometer2"></i></div>
                            <span class="text-nowrap">Management Hub</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.compliance*') ? 'active' : '' }}" href="{{ route('admin.compliance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-shield-check"></i></div>
                            <span class="text-nowrap">Workforce Compliance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('admin.compliance.export-csv') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                            <span class="text-nowrap">Export Compliance CSV</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Department &amp; Staff</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-people"></i></div>
                            <span class="text-nowrap">Department Members</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-qr-code-scan"></i></div>
                            <span class="text-nowrap">Roll-Call Attendance</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Curriculum &amp; Analytics</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-journal-richtext"></i></div>
                            <span class="text-nowrap">Training Tracks</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.scorm*') ? 'active' : '' }}" href="{{ route('admin.scorm.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-box-seam"></i></div>
                            <span class="text-nowrap">SCORM Telemetry</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-calculator"></i></div>
                            <span class="text-nowrap">Gradebook &amp; Audits</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Workforce Learner View</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-collection-play"></i></div>
                            <span class="text-nowrap">My Learning Portal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('certificates.my') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-award"></i></div>
                            <span class="text-nowrap">My Certificates</span>
                        </a>
                    </li>
                </ul>

            {{-- =========================================================================
                 3. COURSE CREATOR DEDICATED SIDEBAR
                 ========================================================================= --}}
            @elseif($isCreator)
                <div class="sidebar-heading text-nowrap">Authoring Studio</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-speedometer2"></i></div>
                            <span class="text-nowrap">Creator Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-journal-richtext"></i></div>
                            <span class="text-nowrap">Curriculum &amp; Lessons</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.scorm*') ? 'active' : '' }}" href="{{ route('admin.scorm.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-box-seam"></i></div>
                            <span class="text-nowrap">SCORM Package Uploads</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Pedagogy &amp; Assessments</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-patch-question"></i></div>
                            <span class="text-nowrap">Question Banks &amp; Quizzes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-calculator"></i></div>
                            <span class="text-nowrap">Gradebook Scales</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Site Publishing</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.cms*') ? 'active' : '' }}" href="{{ route('admin.cms.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-layers"></i></div>
                            <span class="text-nowrap">Course Landing Pages</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Testing &amp; Previews</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-person-workspace"></i></div>
                            <span class="text-nowrap">Learner View Preview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('courses.index') }}" target="_blank">
                            <div class="sidebar-icon-box"><i class="bi bi-grid"></i></div>
                            <span class="text-nowrap">Catalog Preview</span>
                        </a>
                    </li>
                </ul>

            {{-- =========================================================================
                 4. TEACHER (EDITING) / INSTRUCTOR DEDICATED SIDEBAR
                 ========================================================================= --}}
            @elseif($isTeacher)
                <div class="sidebar-heading text-nowrap">Faculty Workspace</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-speedometer2"></i></div>
                            <span class="text-nowrap">Faculty Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-journal-richtext"></i></div>
                            <span class="text-nowrap">My Courses &amp; Modules</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.scorm*') ? 'active' : '' }}" href="{{ route('admin.scorm.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-box-seam"></i></div>
                            <span class="text-nowrap">Course SCORM Packages</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Grading &amp; Proctoring</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-patch-question"></i></div>
                            <span class="text-nowrap">Assessments &amp; Anti-Cheat</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-calculator"></i></div>
                            <span class="text-nowrap">Gradebook &amp; Submissions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-qr-code-scan"></i></div>
                            <span class="text-nowrap">Rolling QR Attendance</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Classroom Cohorts</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-people"></i></div>
                            <span class="text-nowrap">Cohort Student Roster</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-person-workspace"></i></div>
                            <span class="text-nowrap">Personal Learning Portal</span>
                        </a>
                    </li>
                </ul>

            {{-- =========================================================================
                 5. NON-EDITING TEACHER / TRAINER DEDICATED SIDEBAR
                 ========================================================================= --}}
            @elseif($isTeacherNonEdit)
                <div class="sidebar-heading text-nowrap">Facilitator Desk</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-speedometer2"></i></div>
                            <span class="text-nowrap">Facilitator Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-journal-richtext"></i></div>
                            <span class="text-nowrap">Assigned Course Cohorts</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading text-nowrap">Evaluations &amp; Proctoring</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-patch-question"></i></div>
                            <span class="text-nowrap">Student Quiz Attempts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-calculator"></i></div>
                            <span class="text-nowrap">Gradebook Evaluation</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-qr-code-scan"></i></div>
                            <span class="text-nowrap">Attendance Verification</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-person-workspace"></i></div>
                            <span class="text-nowrap">My Learning Portal</span>
                        </a>
                    </li>
                </ul>

            {{-- =========================================================================
                 6. STUDENT / CORPORATE LEARNER / GUEST SIDEBAR
                 ========================================================================= --}}
            @else
                <div class="sidebar-heading text-nowrap">Learner Desk</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('my-learning') ? 'active' : '' }}" href="{{ route('my-learning') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-collection-play"></i></div>
                            <span class="text-nowrap">My Learning Portal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('certificates.my') ? 'active' : '' }}" href="{{ route('certificates.my') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-award"></i></div>
                            <span class="text-nowrap">My Certificates</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('courses.index') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-grid"></i></div>
                            <span class="text-nowrap">Browse All Courses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="{{ url('/') }}">
                            <div class="sidebar-icon-box"><i class="bi bi-house"></i></div>
                            <span class="text-nowrap">Public Academy Home</span>
                        </a>
                    </li>
                </ul>
            @endif

            <!-- Sidebar Bottom Status Card -->
            <div class="mt-auto pt-3 border-top">
                <div class="p-3 rounded-4" style="background: var(--lms-bg-secondary);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-success bg-opacity-25 text-success small">
                            <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Engine Healthy
                        </span>
                        <span class="text-muted small font-mono" style="font-size: 0.68rem;">v3.2</span>
                    </div>
                    <div class="small text-muted" style="font-size: 0.72rem; line-height: 1.3;">
                        Automated Recertification Loop: <strong class="text-success">ACTIVE</strong>
                    </div>
                </div>
            </div>

        </aside>

        <!-- Main Workspace Area -->
        <main class="flex-grow-1 p-3 p-md-4" style="background-color: var(--lms-bg-body); min-height: calc(100vh - 72px); overflow-x: hidden;">
            @yield('content')
        </main>
    </div>

    <!-- Floating Flash Toast Notification (Auto-Dismiss with Timer) -->
    @if(session('success') || session('status') || session('error'))
    <div class="floating-toast-container" 
         x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4500)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-3 scale-95"
         x-cloak>
        <div class="floating-toast shadow-lg">
            <div class="p-3 d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    @if(session('error'))
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 32px; height: 32px; background: #EF4444;">
                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 0.9rem;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold small text-danger" style="font-size: 0.8rem;">Action Alert</div>
                            <div class="small text-body text-truncate" style="font-size: 0.78rem;">{{ session('error') }}</div>
                        </div>
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 32px; height: 32px; background: #10B981;">
                            <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold small text-success" style="font-size: 0.8rem;">Notification</div>
                            <div class="small text-body text-truncate" style="font-size: 0.78rem;">{{ session('success') ?? session('status') }}</div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-link text-secondary p-0 border-0 flex-shrink-0" @click="show = false" title="Dismiss">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="floating-toast-progress" style="background: {{ session('error') ? '#EF4444' : '#10B981' }};"></div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
