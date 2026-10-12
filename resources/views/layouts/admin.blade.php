<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('lms_theme') === 'dark',
          sidebarOpen: true,
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
    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar p-3" x-show="sidebarOpen" x-transition>
            
            <div class="sidebar-heading text-nowrap">{{ $site_settings['sidebar_overview'] ?? 'Operational Overview' }}</div>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <div class="sidebar-icon-box" style="background: #F2ECFB; color: #723EC3;">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <span class="text-nowrap">Executive Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.compliance*') ? 'active' : '' }}" href="{{ route('admin.compliance.index') }}">
                        <div class="sidebar-icon-box" style="background: #E6F9F1; color: #059669;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <span class="text-nowrap">Compliance &amp; Audits</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading text-nowrap">{{ $site_settings['sidebar_academics'] ?? 'Curriculum & Telemetry' }}</div>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                        <div class="sidebar-icon-box" style="background: #E5F5FD; color: #0284C7;">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <span class="text-nowrap">Course Curriculum</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.scorm*') ? 'active' : '' }}" href="{{ route('admin.scorm.index') }}">
                        <div class="sidebar-icon-box" style="background: #FFF2E5; color: #EA580C;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <span class="text-nowrap">SCORM Registry</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes.index') }}">
                        <div class="sidebar-icon-box" style="background: #FFF8E5; color: #D97706;">
                            <i class="bi bi-patch-question"></i>
                        </div>
                        <span class="text-nowrap">Assessments &amp; Anti-Cheat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.gradebook*') ? 'active' : '' }}" href="{{ route('admin.gradebook.index') }}">
                        <div class="sidebar-icon-box" style="background: #EDE9FE; color: #6366F1;">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <span class="text-nowrap">Weighted Gradebook</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                        <div class="sidebar-icon-box" style="background: #FCE7F3; color: #DB2777;">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <span class="text-nowrap">Rolling QR Attendance</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading text-nowrap">{{ $site_settings['sidebar_system'] ?? 'Platform Administration' }}</div>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.cms*') ? 'active' : '' }}" href="{{ route('admin.cms.index') }}">
                        <div class="sidebar-icon-box" style="background: #CCFBF1; color: #0D9488;">
                            <i class="bi bi-layers"></i>
                        </div>
                        <span class="text-nowrap">Headless CMS Engine</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <div class="sidebar-icon-box" style="background: #F3E8FF; color: #9333EA;">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <span class="text-nowrap">Site Dynamic Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <div class="sidebar-icon-box" style="background: #FEF3C7; color: #B45309;">
                            <i class="bi bi-people"></i>
                        </div>
                        <span class="text-nowrap">CBAC Users &amp; Roles</span>
                    </a>
                </li>
            </ul>

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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <div class="fw-semibold">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                        <div class="fw-semibold">{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
