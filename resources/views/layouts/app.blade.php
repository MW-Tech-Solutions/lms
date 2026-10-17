<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('lms_theme') === 'dark',
          isScrolled: false,
          showTopbar: {{ ($site_settings['topbar_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
          showBackToTop: false,
          mobileMenuOpen: false,
          searchModalOpen: false,
          searchQuery: '',
          searchResults: [],
          searchLoading: false,
          openSearchModal() {
              this.searchModalOpen = true;
              setTimeout(() => {
                  const input = document.getElementById('headerModalSearchInput');
                  if (input) {
                      input.focus();
                      input.select();
                  }
              }, 70);
          },
          closeSearchModal() {
              this.searchModalOpen = false;
          },
          performSearch() {
              let q = this.searchQuery.trim();
              if (q.length < 1) {
                  this.searchResults = [];
                  this.searchLoading = false;
                  return;
              }
              this.searchLoading = true;
              fetch('{{ route('courses.search.api') }}?q=' + encodeURIComponent(q))
                  .then(res => res.json())
                  .then(data => {
                      this.searchResults = data.results || [];
                      this.searchLoading = false;
                  })
                  .catch(() => {
                      this.searchLoading = false;
                  });
          },
          selectSearchTag(tag) {
              this.searchQuery = tag;
              this.performSearch();
              const input = document.getElementById('headerModalSearchInput');
              if (input) input.focus();
          },
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('lms_theme', this.darkMode ? 'dark' : 'light');
              document.documentElement.setAttribute('data-bs-theme', this.darkMode ? 'dark' : 'light');
          },
          countdown: {
              days: '{{ str_pad((string)($site_settings['topbar_countdown_days'] ?? '4'), 2, '0', STR_PAD_LEFT) }}',
              hours: '{{ str_pad((string)($site_settings['topbar_countdown_hours'] ?? '18'), 2, '0', STR_PAD_LEFT) }}',
              minutes: '{{ str_pad((string)($site_settings['topbar_countdown_minutes'] ?? '35'), 2, '0', STR_PAD_LEFT) }}',
              seconds: '{{ str_pad((string)($site_settings['topbar_countdown_seconds'] ?? '30'), 2, '0', STR_PAD_LEFT) }}',
              timer: null,
              start() {
                  let totalSec = ({{ (int)($site_settings['topbar_countdown_days'] ?? 4) }} * 86400) + 
                                 ({{ (int)($site_settings['topbar_countdown_hours'] ?? 18) }} * 3600) + 
                                 ({{ (int)($site_settings['topbar_countdown_minutes'] ?? 35) }} * 60) + 
                                 {{ (int)($site_settings['topbar_countdown_seconds'] ?? 30) }};
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
          window.addEventListener('keydown', (e) => {
              if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                  e.preventDefault();
                  openSearchModal();
              }
          });
      "
      @keydown.escape.window="if (searchModalOpen) closeSearchModal()"
      :data-bs-theme="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $site_settings['brand_name'] ?? config('app.name', 'AllThingsCambridge') }} &bull; @yield('title', $site_settings['site_title'] ?? 'Enterprise Study Platform')</title>
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
    
    <!-- Edunex LMS Design System & Tokens (Cache-Busted) -->
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}?v={{ filemtime(public_path('css/lms.css')) }}">

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

        /* Modern Scroll-Triggered Reveal Animations on Components */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.45s ease-out, transform 0.45s ease-out;
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Global Bootstrap Modal Positioning & Width Enforcement */
        .modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 1055 !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 1050 !important;
        }
        .modal-dialog {
            width: 92% !important;
            max-width: 560px !important;
            margin: 1.75rem auto !important;
        }
        .modal-dialog.modal-lg {
            max-width: 820px !important;
        }
        .modal-dialog.modal-xl {
            max-width: 1140px !important;
        }
        .modal-dialog.modal-sm {
            max-width: 380px !important;
        }
        .modal-content {
            width: 100% !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25) !important;
        }
    </style>

    <!-- Scroll Reveal Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Target component cards and inner elements only — never root sections or hero containers
            const targets = document.querySelectorAll('.lms-card, .reveal-target, .category-card-modern, .course-card-modern, .mentor-card-modern');
            targets.forEach(el => el.classList.add('reveal-on-scroll'));

            if ('IntersectionObserver' in window && targets.length > 0) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -20px 0px' });

                targets.forEach(el => observer.observe(el));
            }
        });
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- KaTeX Scientific & Mathematical Formula Rendering (Cambridge 9709 & Sciences) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" integrity="sha384-nB0miv6/jRmo5UMMR1wu3Gz6NLsoNdYKUPUwVM9BQhd08q542xprV5956zEN2KST" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" integrity="sha384-7zkQWkzuo3B5mTepMUcHkMB5jZaigo2TQ447D7euSAuvZvG4bbWBEQ698WNPZIhl" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" integrity="sha384-43gviWU0YVjaDtb/GhzOouOXtZko/WgXcBr8gkgvQbd45IX4P/cv9GDAcx3SUKbr" crossorigin="anonymous"
        onload="renderMathInElement(document.body);"></script>

    @stack('styles')
</head>
<body class="{{ Auth::check() ? 'session-active' : '' }}">
    @if(request()->is('/'))
    <!-- HOMEPAGE PRELOADER WITH PERFECT SEMI-CIRCLE AROUND SYSTEM LOGO -->
    <div id="homepage-preloader" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: radial-gradient(circle at center, #FFFFFF 0%, #F8FAFC 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.5s;
    ">
        <div style="position: relative; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
            <!-- Outer soft track -->
            <div style="
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 3px solid rgba(2, 132, 199, 0.12);
            "></div>
            <!-- Perfect Rotating Semi-Circle -->
            <div class="preloader-semi-circle" style="
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 3.5px solid transparent;
                border-top-color: var(--lms-accent, #723EC3);
                border-right-color: #0284C7;
                animation: lmsSemiSpin 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
                filter: drop-shadow(0 0 8px rgba(114, 62, 195, 0.35));
            "></div>
            <!-- Dynamic Centered System Logo -->
            <div style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: center; padding: 12px; background: #FFFFFF; border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.06); width: 80px; height: 80px;">
                @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                    <img src="{{ $site_settings['brand_logo_url'] }}" alt="Logo" style="max-height: 48px; max-width: 48px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                @elseif(file_exists(public_path('images/site_logo.png')))
                    <img src="{{ asset('images/site_logo.png') }}" alt="Logo" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-2" style="color: var(--lms-accent, #723EC3);"></i>
                @endif
            </div>
        </div>
        <div style="margin-top: 16px; font-family: var(--lms-font-heading); font-size: 0.85rem; font-weight: 700; color: #334155; letter-spacing: 1px; text-transform: uppercase;">
            {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
        </div>
    </div>
    <style>
        @keyframes lmsSemiSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        [data-bs-theme="dark"] #homepage-preloader {
            background: radial-gradient(circle at center, #0F1322 0%, #080A10 100%) !important;
        }
        [data-bs-theme="dark"] #homepage-preloader .preloader-semi-circle {
            border-top-color: #A855F7 !important;
            border-right-color: #38BDF8 !important;
        }
        [data-bs-theme="dark"] #homepage-preloader div[style*="background: #FFFFFF"] {
            background: #151928 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4) !important;
        }
    </style>
    <script>
        (function() {
            function dismissPreloader() {
                var loader = document.getElementById('homepage-preloader');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';
                    setTimeout(function() {
                        if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
                    }, 500);
                }
            }
            if (document.readyState === 'complete') {
                setTimeout(dismissPreloader, 350);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(dismissPreloader, 350);
                });
                setTimeout(dismissPreloader, 2500);
            }
        })();
    </script>
    @endif

    <!-- SITE HEADER WRAPPER (STICKY UNIFIED CONTAINER) -->
    <header class="site-header-wrapper" id="siteHeader">
        @if(($site_settings['topbar_enabled'] ?? '0') === '1')
        <!-- 1. EDUNEX PROMO & COUNTDOWN TOPBAR (DYNAMIC FROM ADMIN SETTINGS) -->
        <div class="edunex-topbar" x-show="showTopbar" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak style="overflow-x: hidden;">
            <div class="container d-flex align-items-center justify-content-between gap-1 gap-sm-2 flex-nowrap px-2 px-sm-3">
                <div class="d-flex align-items-center gap-1 gap-md-3 min-w-0">
                    <div class="countdown-wrap flex-shrink-0">
                        <span class="countdown-box d-none d-sm-inline-block" x-text="countdown.days + 'd'">{{ str_pad((string)($site_settings['topbar_countdown_days'] ?? '2'), 2, '0', STR_PAD_LEFT) }}d</span>
                        <span class="text-white-50 d-none d-sm-inline-block">:</span>
                        <span class="countdown-box" x-text="countdown.hours + 'h'">{{ str_pad((string)($site_settings['topbar_countdown_hours'] ?? '12'), 2, '0', STR_PAD_LEFT) }}h</span>
                        <span class="text-white-50">:</span>
                        <span class="countdown-box" x-text="countdown.minutes + 'm'">{{ str_pad((string)($site_settings['topbar_countdown_minutes'] ?? '44'), 2, '0', STR_PAD_LEFT) }}m</span>
                        <span class="text-white-50">:</span>
                        <span class="countdown-box" x-text="countdown.seconds + 's'">{{ str_pad((string)($site_settings['topbar_countdown_seconds'] ?? '57'), 2, '0', STR_PAD_LEFT) }}s</span>
                    </div>
                    <div class="d-none d-md-flex align-items-center gap-2 small text-truncate">
                        <span class="badge bg-warning text-dark fw-bold px-2 flex-shrink-0">
                            <i class="bi bi-fire me-1"></i> {{ $site_settings['topbar_badge_text'] ?? 'CAMBRIDGE EXAM COUNTDOWN' }}
                        </span>
                        <span class="text-truncate">{{ $site_settings['topbar_promo_text'] ?? 'May/June 2026 Cambridge Series Countdown: Revise with Topical Past Papers & Worked Solutions' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 gap-md-3 flex-shrink-0 ms-auto">
                    <a href="{{ $site_settings['topbar_btn_url'] ?? route('courses.index') }}" class="topbar-claim-btn text-nowrap">
                        <span>{{ $site_settings['topbar_btn_text'] ?? 'Get Certified' }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <button type="button" class="topbar-close-btn flex-shrink-0" @click="showTopbar = false" title="Dismiss">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- 2. EDUNEX MAIN NAVBAR (STRICT 1-LINE FIT TO SCREEN) -->
        <nav class="navbar navbar-expand-lg lms-navbar" :class="{ 'header-sticky': isScrolled }">
        <div class="container-fluid px-2 px-sm-3 px-xl-4 d-flex align-items-center justify-content-between flex-nowrap">
            <!-- Left: Brand Logo & Title (Compact & Fitted) -->
            <div class="d-flex align-items-center flex-nowrap flex-shrink-0">
                <a class="navbar-brand d-flex align-items-center fw-bold text-nowrap m-0 p-0" href="{{ url('/') }}" style="gap: 8px;">
                    @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                        <img src="{{ $site_settings['brand_logo_url'] }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="34" class="rounded-2 shadow-xs me-1.5" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                    @elseif(file_exists(public_path('images/site_logo.png')))
                        <img src="{{ asset('images/site_logo.png') }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="34" class="rounded-2 shadow-xs me-1.5">
                    @else
                        <span class="badge rounded-3 p-2 shadow-sm me-1.5" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 0.95rem; color: #FFF;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </span>
                    @endif
                    <div class="d-flex flex-column text-nowrap">
                        <span style="font-family: var(--lms-font-heading); font-size: 1.15rem; font-weight: 800; color: var(--lms-text-primary); letter-spacing: -0.4px; line-height: 1.1;">
                            {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
                        </span>
                        <span class="text-primary" style="font-size: 0.58rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 1px;">Cambridge International Platform</span>
                    </div>
                </a>
            </div>

            <!-- Center: Desktop Nav Menu (Single Line, Never Wraps) -->
            <div class="d-none d-lg-flex align-items-center justify-content-center flex-nowrap mx-auto px-1 px-xl-2">
                <ul class="navbar-nav d-flex flex-row align-items-center gap-0.5 gap-xl-1 mb-0 flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                            Past Papers &amp; Syllabus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('my-learning') ? 'active' : '' }}" href="{{ Auth::check() ? route('my-learning') . '#esther-tracker' : route('login') }}">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-1 py-0 me-1" style="font-size: 0.62rem;">Matrix</span>
                            Esther Math Tracker
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('mentors.*') ? 'active' : '' }}" href="{{ route('mentors.index') }}">
                            Mentors &amp; Faculty
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->is('p/pricing') ? 'active' : '' }}" href="{{ route('cms.show', ['slug' => 'pricing']) }}">
                            Subscriptions (£)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            Contact
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Right: Action Icons (Search, Dark/Light Toggle, Cart) & User Dropdown / Auth Buttons -->
            <div class="d-flex align-items-center gap-1.5 gap-md-2 flex-nowrap flex-shrink-0">
                <!-- Search Icon Button (Triggers Search Popup Modal) -->
                <button class="btn btn-sm rounded-circle border p-0 header-search-trigger" 
                        style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: var(--lms-bg-secondary);"
                        @click="openSearchModal()" 
                        title="Search Past Papers & Courses (Ctrl + K)"
                        type="button"
                        aria-label="Search">
                    <i class="bi bi-search text-primary" style="font-size: 0.9rem;"></i>
                </button>

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
                    @php
                        $authUser = Auth::user();
                        $userPrimaryRole = $authUser ? $authUser->primaryRole() : 'GUEST';
                        $hasAdminAccess = in_array($userPrimaryRole, ['SUPER_ADMIN', 'MANAGER', 'CORP_MANAGER', 'COURSE_CREATOR', 'TEACHER', 'TEACHER_NON_EDITING', 'INSTRUCTOR', 'TRAINER'], true);
                        $isParentUser = ($authUser->user_type ?? '') === 'PARENT' || (method_exists($authUser, 'children') && $authUser->children()->exists());
                    @endphp

                    <!-- User Account Dropdown with Truncated Name -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-inline-flex align-items-center gap-1.5 border rounded-pill px-2.5 py-1 text-nowrap shadow-xs" 
                                type="button" data-bs-toggle="dropdown" style="height: 34px; font-size: 0.82rem;">
                            <i class="bi bi-person-circle fs-5" style="color: var(--lms-accent);"></i>
                            <span class="lms-navbar-username">{{ $authUser->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
                            <li>
                                <div class="px-3 py-2 border-bottom mb-2">
                                    <div class="fw-bold">{{ $authUser->name }}</div>
                                    <div class="small text-muted">{{ $authUser->email }}</div>
                                    <span class="badge bg-primary-subtle text-primary mt-1">{{ $authUser->user_type }}</span>
                                </div>
                            </li>
                            @if($isParentUser)
                                <li><a class="dropdown-item rounded-3 py-2 fw-semibold text-primary" href="{{ route('parent.dashboard') }}"><i class="bi bi-person-heart me-2 text-info"></i>Guardian Portal</a></li>
                            @else
                                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('my-learning') }}"><i class="bi bi-collection-play me-2 text-primary"></i>My Learning</a></li>
                            @endif
                            @if($hasAdminAccess)
                                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-primary"></i>Admin Portal</a></li>
                            @endif
                            <li><a class="dropdown-item rounded-3 py-2" href="{{ route('profile.security') }}"><i class="bi bi-shield-lock me-2 text-warning"></i>2FA &amp; Security</a></li>
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
                    <a href="{{ route('login') }}" class="tj-btn-outline py-1 px-2 text-nowrap d-inline-flex align-items-center justify-content-center" style="font-size: 0.78rem; height: 32px;">
                        <span>Log In</span>
                    </a>
                    <a href="{{ route('register') }}" class="tj-btn-primary py-1 px-2.5 text-nowrap d-inline-flex align-items-center justify-content-center" style="font-size: 0.78rem; height: 32px;">
                        <span>Enroll Now</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button class="navbar-toggler border-0 shadow-none p-1 ms-1 d-lg-none" type="button" @click="mobileMenuOpen = true" title="Toggle Navigation Menu">
                    <i class="bi bi-list fs-2 text-primary"></i>
                </button>
            </div>
        </div>
    </nav>
    </header>

    <!-- GLOBAL SPOTLIGHT SEARCH POPUP MODAL -->
    <div x-show="searchModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="lms-search-modal-backdrop"
         @click.self="closeSearchModal()"
         x-cloak>
        <div class="lms-search-modal-card" 
             x-show="searchModalOpen"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-4">
            
            <!-- Modal Header / Search Form -->
            <form action="{{ route('courses.index') }}" method="GET" class="m-0" @submit="closeSearchModal()">
                <div class="d-flex align-items-center px-3.5 py-3 border-bottom gap-2.5">
                    <i class="bi bi-search text-primary fs-5 flex-shrink-0"></i>
                    <input type="text" 
                           id="headerModalSearchInput"
                           name="search" 
                           class="form-control border-0 shadow-none p-0 bg-transparent fs-6" 
                           style="color: var(--lms-text-primary);"
                           placeholder="Search Cambridge past papers, syllabus, notes, subjects..." 
                           autocomplete="off"
                           x-model="searchQuery"
                           @input.debounce.150ms="performSearch()">
                    
                    <template x-if="searchLoading">
                        <span class="spinner-border spinner-border-sm text-primary flex-shrink-0" style="width: 16px; height: 16px;"></span>
                    </template>
                    
                    <template x-if="searchQuery.length > 0">
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 border-0 flex-shrink-0" @click="searchQuery = ''; searchResults = []; $nextTick(() => document.getElementById('headerModalSearchInput').focus())" title="Clear">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </template>
                    
                    <span class="badge border text-muted px-1.5 py-0.5 rounded-2 d-none d-sm-inline-block" style="font-size: 0.68rem; font-family: monospace; background: var(--lms-bg-secondary);">ESC</span>
                    
                    <button type="button" class="btn-close ms-1" @click="closeSearchModal()" aria-label="Close"></button>
                </div>
            </form>

            <!-- Modal Body Content -->
            <div class="p-3">
                <!-- Suggested Quick Queries when search is empty -->
                <div x-show="searchQuery.trim().length === 0">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Popular Searches</span>
                        <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-lightning-charge-fill text-warning me-0.5"></i>Quick Filter</span>
                    </div>
                    <div class="d-flex flex-wrap gap-1.5 mb-3">
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Mathematics 9709')">
                            📐 Mathematics 9709
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Physics 9702')">
                            ⚡ Physics 9702
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Chemistry 9701')">
                            🧪 Chemistry 9701
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Biology')">
                            🧬 Biology 9700
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Past Papers')">
                            📄 Past Papers
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-decoration-none border search-chip-btn" 
                                style="font-size: 0.75rem; background: var(--lms-bg-secondary); color: var(--lms-text-primary);"
                                @click="selectSearchTag('Esther Math Tracker')">
                            📊 Esther Tracker
                        </button>
                    </div>
                    <div class="p-2.5 rounded-3 d-flex align-items-center gap-2" style="background: rgba(114, 62, 195, 0.06); border: 1px dashed rgba(114, 62, 195, 0.25);">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span class="small text-muted" style="font-size: 0.75rem;">Type any subject code (e.g. 9709, 0580), topic name, or syllabus keyword to find worked answers instantly.</span>
                    </div>
                </div>

                <!-- Live Results State -->
                <div x-show="searchQuery.trim().length > 0">
                    <div class="px-1 py-1 d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Matches Found</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.65rem;" x-text="searchResults.length + ' result(s)'"></span>
                    </div>

                    <!-- Loading State -->
                    <template x-if="searchLoading && searchResults.length === 0">
                        <div class="text-center py-4 text-muted small">
                            <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                            <div>Searching Cambridge catalog...</div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!searchLoading && searchResults.length === 0">
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-search fs-2 text-muted d-block mb-2"></i>
                            <div>No materials or past papers found for "<span class="fw-bold text-body" x-text="searchQuery"></span>"</div>
                            <div class="text-secondary mt-1" style="font-size: 0.75rem;">Try searching by subject name like "Math", "Physics", or syllabus code "9709"</div>
                        </div>
                    </template>

                    <!-- Results List -->
                    <div class="overflow-y-auto" style="max-height: 380px;">
                        <template x-for="item in searchResults" :key="item.id">
                            <a :href="item.url" class="d-flex align-items-center gap-3 p-2.5 rounded-3 text-decoration-none text-reset mb-1.5 search-modal-item transition-all" @click="closeSearchModal()">
                                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                     :style="item.audience === 'CORPORATE' ? 'background: linear-gradient(135deg, #0F172A, #1E293B); width: 42px; height: 42px;' : 'background: linear-gradient(135deg, var(--lms-accent), #2563EB); width: 42px; height: 42px;'">
                                    <i :class="item.audience === 'CORPORATE' ? 'bi bi-shield-check text-warning' : 'bi bi-mortarboard-fill text-light'" style="font-size: 1.15rem;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold small text-truncate" style="color: var(--lms-text-primary); font-size: 0.85rem;" x-text="item.title"></div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge rounded-pill" 
                                              :class="item.audience === 'CORPORATE' ? 'bg-warning-subtle text-dark' : 'bg-primary-subtle text-primary'" 
                                              style="font-size: 0.62rem;" 
                                              x-text="item.category"></span>
                                        <span class="text-muted font-mono" style="font-size: 0.7rem;" x-text="item.price"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-muted small pe-1 flex-shrink-0">
                                    <span style="font-size: 0.72rem;">Open</span>
                                    <i class="bi bi-arrow-right-short fs-5"></i>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Bottom Link to Full Catalog -->
                    <div class="pt-2.5 mt-2 border-top text-center" x-show="searchResults.length > 0">
                        <a :href="'{{ route('courses.index') }}?search=' + encodeURIComponent(searchQuery)" class="text-primary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                            <span>Browse all matching papers & courses in catalog</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MOBILE NAVIGATION DRAWER (ALPINE.JS NATIVE - STRICTLY MOBILE ONLY) -->
    <div class="lms-mobile-drawer d-lg-none" 
         x-show="mobileMenuOpen"
         :class="{ 'open': mobileMenuOpen }"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         tabindex="-1" 
         id="mobileOffcanvas"
         @keydown.escape.window="mobileMenuOpen = false"
         x-cloak
         style="display: none; position: fixed; top: 0; bottom: 0; left: 0; width: 310px; max-width: 86vw; height: 100vh; z-index: 1065;">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: var(--lms-surface-card);">
            <a class="d-flex align-items-center text-decoration-none" href="{{ url('/') }}" style="gap: 10px;">
                @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                    <img src="{{ $site_settings['brand_logo_url'] }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="32" class="rounded-2 me-2" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                @elseif(file_exists(public_path('images/site_logo.png')))
                    <img src="{{ asset('images/site_logo.png') }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="32" class="rounded-2 me-2">
                @else
                    <span class="badge rounded-3 p-1.5 shadow-sm me-2" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 0.85rem; color: #FFF;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                @endif
                <div class="d-flex flex-column">
                    <span class="fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); font-size: 1.05rem; line-height: 1.1;">
                        {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
                    </span>
                    <span class="text-muted" style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">Cambridge International Platform</span>
                </div>
            </a>
            <button type="button" class="btn-close" @click="mobileMenuOpen = false" aria-label="Close"></button>
        </div>

        <div class="p-3 d-flex flex-column justify-content-between h-100 overflow-y-auto">
            <div>
                <!-- Search Box -->
                <form action="{{ route('courses.index') }}" method="GET" class="mb-3">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search courses, tracks...">
                    </div>
                </form>

                <!-- Navigation Links -->
                <ul class="nav flex-column gap-1 mb-3">
                    <li class="nav-item">
                        <a class="nav-link rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('home') ? 'bg-primary-subtle text-primary' : 'text-body' }}" href="{{ url('/') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('courses.*') ? 'bg-primary-subtle text-primary' : 'text-body' }}" href="{{ route('courses.index') }}">
                            <i class="bi bi-book"></i> Courses &amp; Programs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('mentors.*') ? 'bg-primary-subtle text-primary' : 'text-body' }}" href="{{ route('mentors.index') }}">
                            <i class="bi bi-people"></i> Mentors &amp; Faculty
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ request()->routeIs('corporate.compliance') ? 'bg-primary-subtle text-primary' : 'text-body' }}" href="{{ route('corporate.compliance') }}">
                            <i class="bi bi-shield-check text-primary"></i> Corporate Compliance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ request()->is('p/pricing') ? 'bg-primary-subtle text-primary' : 'text-body' }}" href="{{ route('cms.show', ['slug' => 'pricing']) }}">
                            <i class="bi bi-tag"></i> Pricing Plans
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom Actions -->
            <div class="border-top pt-3 mt-auto">
                @auth
                    <div class="d-flex align-items-center gap-2 mb-3 px-2">
                        <i class="bi bi-person-circle fs-3 text-primary"></i>
                        <div class="min-w-0">
                            <div class="fw-bold small text-truncate text-body">{{ Auth::user()->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                            <span class="badge bg-primary-subtle text-primary mt-0.5" style="font-size: 0.65rem;">{{ Auth::user()->user_type }}</span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        @if((Auth::user()->user_type ?? '') === 'PARENT' || (method_exists(Auth::user(), 'children') && Auth::user()->children()->exists()))
                            <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-info rounded-pill py-2 small fw-semibold text-center">
                                <i class="bi bi-person-heart me-1"></i> Guardian Portal
                            </a>
                        @else
                            <a href="{{ route('my-learning') }}" class="btn btn-outline-primary rounded-pill py-2 small fw-semibold text-center">
                                <i class="bi bi-collection-play me-1"></i> My Learning
                            </a>
                        @endif
                        <a href="{{ route('admin.dashboard') }}" class="tj-btn-primary text-center py-2 small">
                            <i class="bi bi-speedometer2 me-1"></i> Management Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="btn btn-light w-100 text-danger border rounded-pill py-1.5 small fw-semibold">
                                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="tj-btn-outline text-center py-2">Sign In</a>
                        <a href="{{ route('register') }}" class="tj-btn-primary text-center py-2">Enroll Now &rarr;</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Offcanvas Backdrop -->
    <div class="lms-drawer-backdrop d-lg-none" 
         x-show="mobileMenuOpen"
         :class="{ 'open': mobileMenuOpen }" 
         @click="mobileMenuOpen = false" 
         x-cloak
         style="display: none; position: fixed; inset: 0; z-index: 1060; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(3px);">
    </div>

    <!-- 4. FLOATING FLASH TOAST NOTIFICATION (AUTO-DISMISS WITH TIMER) -->
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

    <!-- SweetAlert2 System-Wide Engine -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success') || session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: {!! json_encode(session('success') ?? session('status')) !!},
                    confirmButtonColor: '#723EC3',
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Notice',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonColor: '#723EC3'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: {!! json_encode(session('warning')) !!},
                    confirmButtonColor: '#723EC3'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: {!! json_encode(session('info')) !!},
                    confirmButtonColor: '#723EC3'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    html: `<ul class="text-start mb-0 small text-danger">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>`,
                    confirmButtonColor: '#723EC3'
                });
            @endif

            // Polyfill native window.alert to always use SweetAlert2
            window.alert = function (message) {
                Swal.fire({
                    icon: 'info',
                    title: 'Notification',
                    text: message,
                    confirmButtonColor: '#723EC3'
                });
            };
        });

        // Global SweetAlert2 Confirm Action Helper
        window.confirmAction = function(event, message, title = 'Are you sure?') {
            event.preventDefault();
            const form = event.target.closest('form') || event.target;
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#723EC3',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form.tagName === 'FORM') {
                        form.submit();
                    } else if (event.target.href) {
                        window.location.href = event.target.href;
                    }
                }
            });
            return false;
        };
    </script>
    @stack('scripts')
</body>
</html>
