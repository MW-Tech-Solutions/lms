<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('lms_theme') === 'dark',
          isScrolled: false,
          showTopbar: true,
          showBackToTop: false,
          mobileMenuOpen: false,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('lms_theme', this.darkMode ? 'dark' : 'light');
              document.documentElement.setAttribute('data-bs-theme', this.darkMode ? 'dark' : 'light');
          },
          countdown: {
              days: '04',
              hours: '18',
              minutes: '42',
              seconds: '55',
              timer: null,
              start() {
                  let totalSec = (4 * 86400) + (18 * 3600) + (42 * 60) + 55;
                  this.timer = setInterval(() => {
                      if (totalSec > 0) {
                          totalSec--;
                          this.days = String(Math.floor(totalSec / 86400)).padStart(2, '0');
                          this.hours = String(Math.floor((totalSec % 86400) / 3600)).padStart(2, '0');
                          this.minutes = String(Math.floor((totalSec % 3600) / 60)).padStart(2, '0');
                          this.seconds = String(totalSec % 60).padStart(2, '0');
                      }
                  }, 1000);
              }
          }
      }"
      x-init="
          document.documentElement.setAttribute('data-bs-theme', darkMode ? 'dark' : 'light');
          countdown.start();
          window.addEventListener('scroll', () => {
              isScrolled = window.scrollY > 50;
              showBackToTop = window.scrollY > 300;
          });
      "
      :data-bs-theme="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $site_settings['site_title'] ?? 'Enterprise Dual-Audience LMS') - {{ $site_settings['brand_name'] ?? 'Enterprise Academy' }}</title>
    <meta name="description" content="@yield('meta_description', $site_settings['meta_description'] ?? 'Enterprise Learning Management System')">

    <!-- Favicon from Dynamic Brand Settings -->
    <link rel="icon" type="image/x-icon" href="{{ !empty($site_settings['brand_favicon_url']) ? $site_settings['brand_favicon_url'] : asset('favicon.ico') }}">

    <!-- Google Fonts: Manrope (Body) & Urbanist (Headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Urbanist:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Edunex LMS Design System & Tokens -->
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body>
    <!-- 1. EDUNEX PROMO & COUNTDOWN TOPBAR -->
    <div class="edunex-topbar" x-show="showTopbar" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3 mx-auto mx-lg-0">
                <div class="countdown-wrap">
                    <span class="countdown-box" x-text="countdown.days + 'd'">04d</span>
                    <span class="text-white-50">:</span>
                    <span class="countdown-box" x-text="countdown.hours + 'h'">18h</span>
                    <span class="text-white-50">:</span>
                    <span class="countdown-box" x-text="countdown.minutes + 'm'">42m</span>
                    <span class="text-white-50">:</span>
                    <span class="countdown-box" x-text="countdown.seconds + 's'">55s</span>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2 small">
                    <span class="badge bg-warning text-dark fw-bold px-2"><i class="bi bi-fire me-1"></i> SPECIAL EVENT</span>
                    <span>{{ $site_settings['topbar_promo_text'] ?? 'Get 50% Off Annual Enterprise Compliance & Accredited Certifications' }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto ms-lg-0">
                <a href="{{ route('cms.show', ['slug' => 'pricing']) }}" class="topbar-claim-btn">
                    <span>Claim Offer</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <button type="button" class="topbar-close-btn" @click="showTopbar = false" title="Dismiss">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. EDUNEX MAIN NAVBAR (STRICT 1-LINE FIT TO SCREEN) -->
    <nav class="navbar navbar-expand-lg lms-navbar sticky-top px-3" :class="{ 'header-sticky': isScrolled }">
        <div class="container-fluid px-3 px-xl-4 d-flex align-items-center justify-content-between flex-nowrap">
            <!-- Left: Brand Logo & Optional Search -->
            <div class="d-flex align-items-center gap-3 flex-nowrap flex-shrink-0">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-nowrap m-0 p-0" href="{{ url('/') }}">
                    @if(!empty($site_settings['brand_logo_url']))
                        <img src="{{ $site_settings['brand_logo_url'] }}" alt="{{ $site_settings['brand_name'] ?? 'Logo' }}" height="34">
                    @else
                        <span class="badge rounded-3 p-2 shadow-sm" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1rem; color: #FFF;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </span>
                    @endif
                    <div class="d-flex flex-column text-nowrap">
                        <span style="font-family: var(--lms-font-heading); font-size: 1.25rem; font-weight: 800; color: var(--lms-text-primary); letter-spacing: -0.5px; line-height: 1;">
                            {{ $site_settings['brand_name'] ?? 'Enterprise Academy' }}
                        </span>
                        <span class="text-muted" style="font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Enterprise LMS</span>
                    </div>
                </a>

                <div class="d-none d-lg-block ms-2 position-relative" 
                     x-data="{
                         searchQuery: '',
                         results: [],
                         loading: false,
                         openDropdown: false,
                         fetchResults() {
                             let q = this.searchQuery.trim();
                             if (q.length < 1) {
                                 this.results = [];
                                 this.openDropdown = false;
                                 return;
                             }
                             this.openDropdown = true;
                             this.loading = true;
                             fetch('{{ route('courses.search.api') }}?q=' + encodeURIComponent(q))
                                 .then(res => res.json())
                                 .then(data => {
                                     this.results = data.results || [];
                                     this.loading = false;
                                 })
                                 .catch(() => {
                                     this.loading = false;
                                 });
                         }
                     }"
                     @click.outside="openDropdown = false"
                     @keydown.escape.window="openDropdown = false">
                    
                    <form action="{{ route('courses.index') }}" method="GET" class="header-search-form m-0 position-relative" @submit="openDropdown = false">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search courses, tracks..." 
                               autocomplete="off"
                               x-model="searchQuery"
                               @input.debounce.150ms="fetchResults()"
                               @focus="if (searchQuery.trim().length > 0) openDropdown = true">
                        
                        <template x-if="loading">
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 spinner-border spinner-border-sm text-primary" style="width: 14px; height: 14px;"></span>
                        </template>
                    </form>

                    <!-- Floating Live Search Results Dropdown -->
                    <div x-show="openDropdown" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         x-cloak
                         class="position-absolute start-0 mt-2 shadow-lg rounded-4 p-2"
                         style="top: 100%; width: 400px; background: var(--lms-surface-card); border: 1px solid var(--lms-border); z-index: 1080;">
                        
                        <div class="px-2 py-1.5 border-bottom d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Live Matches</span>
                            <span class="badge badge-purple-soft rounded-pill" style="font-size: 0.62rem;" x-text="results.length + ' found'"></span>
                        </div>

                        <template x-if="loading && results.length === 0">
                            <div class="text-center py-4 text-muted small">
                                <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                                <div>Searching courses...</div>
                            </div>
                        </template>

                        <template x-if="!loading && results.length === 0 && searchQuery.trim().length > 0">
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-search fs-3 text-muted d-block mb-1"></i>
                                <div>No courses matching "<span class="fw-bold text-dark" x-text="searchQuery"></span>"</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">Try searching "ISO", "Cloud", or "Compliance"</div>
                            </div>
                        </template>

                        <div class="overflow-y-auto" style="max-height: 320px;">
                            <template x-for="item in results" :key="item.id">
                                <a :href="item.url" class="d-flex align-items-center gap-2.5 p-2 rounded-3 text-decoration-none text-reset mb-1 search-item-hover transition-all">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                         :style="item.audience === 'CORPORATE' ? 'background: linear-gradient(135deg, #0F172A, #1E293B); width: 38px; height: 38px;' : 'background: linear-gradient(135deg, #723EC3, #2563EB); width: 38px; height: 38px;'">
                                        <i :class="item.audience === 'CORPORATE' ? 'bi bi-shield-check text-warning' : 'bi bi-mortarboard-fill text-light'" style="font-size: 1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold small text-truncate text-dark" x-text="item.title" style="line-height: 1.25;"></div>
                                        <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                            <span class="badge rounded-pill" 
                                                  :class="item.audience === 'CORPORATE' ? 'bg-warning-subtle text-dark' : 'bg-primary-subtle text-primary'" 
                                                  style="font-size: 0.6rem;" 
                                                  x-text="item.category"></span>
                                            <span class="text-muted font-mono" style="font-size: 0.65rem;" x-text="item.price"></span>
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted small pe-1"></i>
                                </a>
                            </template>
                        </div>

                        <div class="pt-2 mt-1 border-top text-center" x-show="results.length > 0">
                            <a :href="'{{ route('courses.index') }}?search=' + encodeURIComponent(searchQuery)" class="text-primary small fw-semibold text-decoration-none">
                                View all results in catalog &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center: Desktop Nav Menu (Single Line, Never Wraps) -->
            <div class="d-none d-lg-flex align-items-center justify-content-center flex-nowrap mx-auto px-2">
                <ul class="navbar-nav d-flex flex-row align-items-center gap-1 mb-0 flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                            Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('corporate.compliance') ? 'active' : '' }}" href="{{ route('corporate.compliance') }}">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 py-0 me-1" style="font-size: 0.65rem;">Track</span>
                            Compliance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->is('p/pricing') ? 'active' : '' }}" href="{{ route('cms.show', ['slug' => 'pricing']) }}">
                            Pricing
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Right: Action Icons & User Dropdown (Single Line) -->
            <div class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">
                <!-- Light / Dark Mode Toggle with Alpine.js -->
                <button class="btn btn-sm rounded-circle border p-0" 
                        style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: var(--lms-bg-secondary);"
                        @click="toggleTheme()" 
                        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        type="button">
                    <i :class="darkMode ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill text-primary'" style="font-size: 0.9rem;"></i>
                </button>

                <!-- Cart / Catalog Badge -->
                <a href="{{ route('courses.index') }}" class="btn btn-sm rounded-circle border p-0 position-relative"
                   style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: var(--lms-bg-secondary);"
                   title="Browse Catalog">
                    <i class="bi bi-bag text-primary" style="font-size: 0.9rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 2px 4px;">
                        2
                    </span>
                </a>

                @auth
                    <!-- Management Hub Button -->
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 0.85rem; height: 36px;">
                        <i class="bi bi-grid-fill"></i>
                        <span>Management Hub</span>
                    </a>

                    <!-- User Account Dropdown with Truncated Name -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-inline-flex align-items-center gap-1 border rounded-pill px-2 py-1 text-nowrap" 
                                type="button" data-bs-toggle="dropdown" style="height: 36px; font-size: 0.85rem;">
                            <i class="bi bi-person-circle fs-5" style="color: var(--lms-accent);"></i>
                            <span class="lms-navbar-username">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
                            <li>
                                <div class="px-3 py-2 border-bottom mb-2">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                    <span class="badge bg-primary-subtle text-primary mt-1">{{ Auth::user()->user_type }}</span>
                                </div>
                            </li>
                            <li><a class="dropdown-item rounded-3 py-2" href="{{ route('my-learning') }}"><i class="bi bi-collection-play me-2 text-primary"></i>My Learning</a></li>
                            <li><a class="dropdown-item rounded-3 py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-primary"></i>Admin Portal</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="tj-btn-outline py-1 px-3 text-nowrap" style="font-size: 0.85rem; height: 36px;">
                        <span>Log In</span>
                    </a>
                    <a href="{{ route('register') }}" class="tj-btn-primary py-1 px-3 text-nowrap" style="font-size: 0.85rem; height: 36px;">
                        <span>Enroll Now</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button class="navbar-toggler border-0 shadow-none p-1 ms-1 d-lg-none" type="button" @click="mobileMenuOpen = true">
                    <i class="bi bi-list fs-2 text-primary"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- 3. OFFCANVAS MOBILE DRAWER -->
    <div class="offcanvas offcanvas-start border-0" tabindex="-1" id="mobileOffcanvas" 
         x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         x-cloak
         style="background: var(--lms-surface-card); width: 320px; display: block; z-index: 1060; position: fixed; top: 0; bottom: 0; left: 0;">
        <div class="offcanvas-header border-bottom p-3 d-flex align-items-center justify-content-between">
            <h5 class="offcanvas-title fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-accent);">
                {{ $site_settings['brand_name'] ?? 'Edunex' }}
            </h5>
            <button type="button" class="btn-close" @click="mobileMenuOpen = false"></button>
        </div>
        <div class="offcanvas-body p-3">
            <form action="{{ route('courses.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search courses...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <ul class="nav flex-column gap-2 mb-4">
                <li class="nav-item"><a class="nav-link fw-semibold" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('courses.index') }}">Courses</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('corporate.compliance') }}">Corporate Compliance</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('cms.show', ['slug' => 'pricing']) }}">Pricing</a></li>
            </ul>
            <div class="d-grid gap-2 border-top pt-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="tj-btn-primary text-center">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="tj-btn-outline text-center">Sign In</a>
                    <a href="{{ route('register') }}" class="tj-btn-primary text-center">Enroll Now</a>
                @endauth
            </div>
        </div>
    </div>
    <!-- Offcanvas Backdrop -->
    <div class="modal-backdrop fade show" x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak style="z-index: 1050;"></div>

    <!-- 4. GLOBAL FLASH ALERTS -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- 5. MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- Global Edunex Footer on Non-CMS Pages -->
    @if(!isset($page) || (isset($page->sections) && !$page->sections->where('section_type', 'FOOTER')->count()))
        @include('cms.sections.footer', ['section' => null])
    @endif

    <!-- 6. BACK TO TOP BUTTON -->
    <button id="back-to-top" type="button" class="back-to-top-btn" 
            x-show="showBackToTop" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            x-cloak
            title="Scroll to top">
        <i class="bi bi-arrow-up-short fs-3"></i>
    </button>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
