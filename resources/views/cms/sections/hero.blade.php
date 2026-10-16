@php
    $c = $section->content_json ?? [];

    // Master 3-Slide Dynamic Configuration with Fallbacks
    $slides = [];

    // Slide 1: Primary Value Proposition
    if (($site_settings['hero_slide_1_enabled'] ?? '1') === '1') {
        $slides[] = [
            'id' => 1,
            'image' => !empty($site_settings['hero_slide_1_image']) ? $site_settings['hero_slide_1_image'] : asset('images/hero/hero-slide-1.webp'),
            'badge' => $site_settings['hero_slide_1_badge'] ?? 'CAMBRIDGE INTERNATIONAL PLATFORM',
            'badge_icon' => 'bi-mortarboard-fill',
            'heading' => $site_settings['hero_slide_1_heading'] ?? 'Master Cambridge Curriculum with Verified Solutions.',
            'highlight' => $site_settings['hero_slide_1_highlight'] ?? 'Diagnostic Past Papers & Automated Trackers.',
            'desc' => $site_settings['hero_slide_1_desc'] ?? 'The premier academic hub featuring 5 isolated Cambridge levels, LaTeX formula rendering, official mark scheme breakdowns, and real-time student mastery analytics.',
            'cta1_text' => $site_settings['hero_slide_1_cta1_text'] ?? 'Explore Past Papers',
            'cta1_url' => $site_settings['hero_slide_1_cta1_url'] ?? route('courses.index'),
            'cta2_text' => $site_settings['hero_slide_1_cta2_text'] ?? 'Try Esther Math Tracker',
            'cta2_url' => $site_settings['hero_slide_1_cta2_url'] ?? (Auth::check() ? route('my-learning') . '#esther-tracker' : route('login')),
            'overlay' => (float)($site_settings['hero_slide_1_overlay'] ?? 0.58),
            'accent_pill' => 'Primary • Checkpoint • IGCSE • AS & A Level',
        ];
    }

    // Slide 2: STEM & Advanced Mathematics
    if (($site_settings['hero_slide_2_enabled'] ?? '1') === '1') {
        $slides[] = [
            'id' => 2,
            'image' => !empty($site_settings['hero_slide_2_image']) ? $site_settings['hero_slide_2_image'] : asset('images/hero/hero-slide-2.webp'),
            'badge' => $site_settings['hero_slide_2_badge'] ?? 'STEM & SCIENTIFIC RIGOR',
            'badge_icon' => 'bi-calculator-fill',
            'heading' => $site_settings['hero_slide_2_heading'] ?? 'Accelerate Calculus, Physics & Chemistry Mastery.',
            'highlight' => $site_settings['hero_slide_2_highlight'] ?? 'Step-by-Step Derivations & LaTeX Formulas.',
            'desc' => $site_settings['hero_slide_2_desc'] ?? 'Dissect complex exam permutations with clear KaTeX formula derivations, topical question packs, and interactive Cambridge examiner guidance.',
            'cta1_text' => $site_settings['hero_slide_2_cta1_text'] ?? 'Open Esther Tracker',
            'cta1_url' => $site_settings['hero_slide_2_cta1_url'] ?? (Auth::check() ? route('my-learning') . '#esther-tracker' : route('login')),
            'cta2_text' => $site_settings['hero_slide_2_cta2_text'] ?? 'Print Diagnostic Report',
            'cta2_url' => $site_settings['hero_slide_2_cta2_url'] ?? route('scores.report'),
            'overlay' => (float)($site_settings['hero_slide_2_overlay'] ?? 0.52),
            'accent_pill' => 'KaTeX Scientific Engine • Real-Time Matrix Calculations',
        ];
    }

    // Slide 3: Accredited Examiners & Mentorship
    if (($site_settings['hero_slide_3_enabled'] ?? '1') === '1') {
        $slides[] = [
            'id' => 3,
            'image' => !empty($site_settings['hero_slide_3_image']) ? $site_settings['hero_slide_3_image'] : asset('images/hero/hero-slide-3.webp'),
            'badge' => $site_settings['hero_slide_3_badge'] ?? 'VERIFIED EXAMINER NETWORK',
            'badge_icon' => 'bi-patch-check-fill',
            'heading' => $site_settings['hero_slide_3_heading'] ?? 'Learn Directly from Accredited Cambridge Mentors.',
            'highlight' => $site_settings['hero_slide_3_highlight'] ?? '1-on-1 Feedback, Diagnostic Clinic & Global Trust.',
            'desc' => $site_settings['hero_slide_3_desc'] ?? 'Connect with experienced syllabus moderators, review individualized assessment profiles, and prepare with confidence for upcoming May/June exam series.',
            'cta1_text' => $site_settings['hero_slide_3_cta1_text'] ?? 'Meet Cambridge Mentors',
            'cta1_url' => $site_settings['hero_slide_3_cta1_url'] ?? route('mentors.index'),
            'cta2_text' => $site_settings['hero_slide_3_cta2_text'] ?? 'View Subscription Plans',
            'cta2_url' => $site_settings['hero_slide_3_cta2_url'] ?? route('cms.show', ['slug' => 'pricing']),
            'overlay' => (float)($site_settings['hero_slide_3_overlay'] ?? 0.52),
            'accent_pill' => 'Senior CIE Faculty • Syllabus Category 1 Moderators',
        ];
    }

    // Fallback if all disabled
    if (empty($slides)) {
        $slides[] = [
            'id' => 1,
            'image' => asset('images/hero/hero-slide-1.webp'),
            'badge' => 'CAMBRIDGE INTERNATIONAL PLATFORM',
            'badge_icon' => 'bi-mortarboard-fill',
            'heading' => 'Master Cambridge Curriculum with Verified Solutions.',
            'highlight' => 'Diagnostic Past Papers & Automated Trackers.',
            'desc' => 'The premier academic hub featuring 5 isolated Cambridge levels, LaTeX formula rendering, and real-time student mastery analytics.',
            'cta1_text' => 'Explore Past Papers',
            'cta1_url' => route('courses.index'),
            'cta2_text' => 'Try Esther Math Tracker',
            'cta2_url' => (Auth::check() ? route('my-learning') . '#esther-tracker' : route('login')),
            'overlay' => 0.55,
            'accent_pill' => 'Primary • Checkpoint • IGCSE • AS & A Level',
        ];
    }
@endphp

<!-- PREMIUM ANIMATED HERO SLIDER WITH EXACTLY THREE DYNAMIC SLIDES -->
<section class="master-hero-slider-section position-relative overflow-hidden" 
         id="masterHeroSlider"
         x-data="{
             slides: {{ Js::from($slides) }},
             currentIndex: 0,
             autoplayInterval: null,
             timerDuration: 7000,
             progress: 0,
             progressInterval: null,
             isHovered: false,
             isInView: true,
             observer: null,

             startAutoplay() {
                 this.stopAutoplay();
                 if (this.slides.length <= 1) return;

                 const stepTime = 50;
                 const stepIncrement = (stepTime / this.timerDuration) * 100;

                 this.progressInterval = setInterval(() => {
                     // Only advance if hero is actively visible in viewport and not hovered
                     if (!this.isHovered && this.isInView) {
                         this.progress += stepIncrement;
                         if (this.progress >= 100) {
                             this.nextSlide();
                         }
                     }
                 }, stepTime);
             },

             stopAutoplay() {
                 if (this.progressInterval) {
                     clearInterval(this.progressInterval);
                     this.progressInterval = null;
                 }
                 this.progress = 0;
             },

             nextSlide() {
                 this.progress = 0;
                 this.currentIndex = (this.currentIndex + 1) % this.slides.length;
             },

             prevSlide() {
                 this.progress = 0;
                 this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
             },

             goToSlide(index) {
                 this.progress = 0;
                 this.currentIndex = index;
             },

             initObserver() {
                 if ('IntersectionObserver' in window) {
                     this.observer = new IntersectionObserver((entries) => {
                         entries.forEach(entry => {
                             this.isInView = entry.isIntersecting;
                         });
                     }, { threshold: 0.1 });
                     this.observer.observe(this.$el);
                 }
             }
         }"
         x-init="initObserver(); startAutoplay();"
         @mouseenter="isHovered = true"
         @mouseleave="isHovered = false"
         @keydown.right.window="if (isHovered || (isInView && window.scrollY < 250)) nextSlide()"
         @keydown.left.window="if (isHovered || (isInView && window.scrollY < 250)) prevSlide()"
         style="min-height: 640px; background: #0A0D16; color: #FFFFFF;">

    <!-- Background Images with Ken Burns Zoom & Smooth Crossfade -->
    <template x-for="(slide, index) in slides" :key="slide.id">
        <div class="position-absolute top-0 start-0 w-100 h-100 hero-slide-bg"
             :class="{ 'is-active': currentIndex === index }"
             style="transition: opacity 1000ms cubic-bezier(0.4, 0, 0.2, 1); z-index: 1;"
             :style="currentIndex === index ? 'opacity: 1; pointer-events: auto;' : 'opacity: 0; pointer-events: none;'">
            
            <!-- Ken Burns Animated Image -->
            <img :src="slide.image" 
                 :alt="slide.heading"
                 class="w-100 h-100 object-fit-cover ken-burns-img"
                 :class="{ 'animate-ken-burns': currentIndex === index }"
                 loading="eager"
                 fetchpriority="high">

            <!-- Dynamic Theme Tint & Gradient Overlay -->
            <div class="position-absolute top-0 start-0 w-100 h-100"
                 :style="`background: linear-gradient(90deg, rgba(10, 13, 22, ${Math.min(0.95, slide.overlay + 0.3)}) 0%, rgba(10, 13, 22, ${slide.overlay}) 50%, rgba(15, 19, 33, 0.45) 100%);`"></div>

            <!-- Secondary Top/Bottom Atmospheric Vignette -->
            <div class="position-absolute top-0 start-0 w-100 h-100"
                 style="background: linear-gradient(180deg, rgba(10, 13, 22, 0.6) 0%, transparent 25%, transparent 75%, rgba(10, 13, 22, 0.9) 100%);"></div>
        </div>
    </template>

    <!-- Foreground Content Container -->
    <div class="container position-relative py-5 d-flex flex-column justify-content-center" style="z-index: 10; min-height: 640px;">
        <div class="row align-items-center g-4 py-4">
            
            <!-- Left Column: Copy, Staggered Reveal Typography & Real HTML CTAs (Grid Overlap eliminates height jumps) -->
            <div class="col-lg-7 col-xl-8 text-center text-lg-start hero-slides-wrapper"
                 style="display: grid; grid-template-columns: 1fr; grid-template-rows: 1fr; align-items: center; min-height: 480px;">
                <template x-for="(slide, index) in slides" :key="'content-' + slide.id">
                    <div x-show="currentIndex === index"
                         class="hero-slide-content-item"
                         style="grid-area: 1 / 1 / 2 / 2;"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-3">
                        
                        <!-- Eyebrow Badge Pill -->
                        <div class="mb-3 d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-lg border"
                             style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-color: rgba(255, 255, 255, 0.2) !important;">
                            <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold shadow-xs" 
                                  style="background: var(--lms-accent, #723EC3); font-size: 0.72rem; letter-spacing: 0.6px;">
                                <i class="bi me-1" :class="slide.badge_icon"></i>
                                <span x-text="slide.badge"></span>
                            </span>
                            <span class="small fw-semibold text-white-50 text-truncate d-none d-sm-inline" 
                                  style="font-size: 0.8rem;" 
                                  x-text="slide.accent_pill"></span>
                        </div>

                        <!-- Main Heading with Highlight Gradient -->
                        <h1 class="display-4 fw-extrabold mb-3 text-white lh-sm" 
                            style="font-family: var(--lms-font-heading); letter-spacing: -1.2px; text-shadow: 0 4px 20px rgba(0,0,0,0.6);">
                            <span x-text="slide.heading"></span><br>
                            <span style="background: linear-gradient(135deg, #38BDF8 0%, #818CF8 50%, #34D399 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"
                                  x-text="slide.highlight"></span>
                        </h1>

                        <!-- Supporting Description -->
                        <p class="lead text-white-50 mb-4 fs-6" 
                           style="line-height: 1.85; max-width: 620px; font-weight: 400; text-shadow: 0 2px 10px rgba(0,0,0,0.4);"
                           x-text="slide.desc"></p>

                        <!-- Primary and Secondary Action CTAs -->
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-3 mb-4">
                            <a :href="slide.cta1_url" class="tj-btn-primary py-3 px-4 shadow-lg text-nowrap" style="font-size: 0.96rem;">
                                <span x-text="slide.cta1_text"></span>
                                <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                            </a>
                            <template x-if="slide.cta2_text && slide.cta2_url">
                                <a :href="slide.cta2_url" class="btn btn-outline-light rounded-pill py-3 px-4 fw-semibold text-nowrap shadow-sm" style="font-size: 0.96rem; border-color: rgba(255,255,255,0.35);">
                                    <i class="bi bi-compass text-warning me-1.5"></i>
                                    <span x-text="slide.cta2_text"></span>
                                </a>
                            </template>
                        </div>

                        <!-- 5 Cambridge Silo Pills for Quick Access -->
                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 pt-1 text-white-50 small">
                            <span class="badge rounded-pill px-2.5 py-1.5 border" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.15) !important;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Primary Checkpoint
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1.5 border" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.15) !important;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Lower Secondary
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1.5 border" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.15) !important;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>IGCSE (Y10-11)
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1.5 border" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.15) !important;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>AS &amp; A Level (Y12-13)
                            </span>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Right Column: Interactive Quick Card Preview -->
            <div class="col-lg-5 col-xl-4 d-none d-lg-block">
                <div class="p-4 rounded-4 shadow-2xl border position-relative"
                     style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-color: rgba(255, 255, 255, 0.15) !important;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-circle p-2" style="background: var(--lms-accent, #723EC3);">
                                <i class="bi bi-mortarboard text-white" style="font-size: 0.9rem;"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-white small" style="line-height: 1.2;">Cambridge Portal</div>
                                <span class="text-white-50" style="font-size: 0.68rem;">Curriculum Hub</span>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                            <i class="bi bi-patch-check-fill me-1"></i> CIE 2026 Ready
                        </span>
                    </div>

                    <!-- Slide Progress Indicator Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-white-50 mb-1" style="font-size: 0.72rem;">
                            <span>Active Campaign Slide</span>
                            <span class="font-mono text-white fw-bold" x-text="`${currentIndex + 1} of ${slides.length}`"></span>
                        </div>
                        <div class="progress rounded-pill overflow-hidden" style="height: 4px; background: rgba(255, 255, 255, 0.15);">
                            <div class="progress-bar" 
                                 :style="`width: ${progress}%; background: var(--lms-accent, #723EC3); transition: width 50ms linear;`"></div>
                        </div>
                    </div>

                    <!-- Quick Navigation Thumbnails -->
                    <div class="vstack gap-2">
                        <template x-for="(s, idx) in slides" :key="'thumb-' + s.id">
                            <button type="button" 
                                    @click="goToSlide(idx)"
                                    class="btn text-start p-2.5 rounded-3 d-flex align-items-center justify-content-between border transition-all"
                                    :style="currentIndex === idx 
                                        ? 'background: rgba(255, 255, 255, 0.12); border-color: var(--lms-accent, #723EC3) !important;' 
                                        : 'background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.08);'">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <div class="rounded-2 overflow-hidden flex-shrink-0" style="width: 44px; height: 32px;">
                                        <img :src="s.image" :alt="s.heading" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-white small text-truncate" style="font-size: 0.78rem;" x-text="s.badge"></div>
                                        <div class="text-white-50 small text-truncate" style="font-size: 0.7rem;" x-text="s.heading"></div>
                                    </div>
                                </div>
                                <i class="bi small ms-2 flex-shrink-0" 
                                   :class="currentIndex === idx ? 'bi-play-circle-fill text-warning' : 'bi-chevron-right text-white-50'"></i>
                            </button>
                        </template>
                    </div>

                    <!-- Bottom Tag -->
                    <div class="mt-3 pt-2 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center text-white-50 small" style="font-size: 0.7rem;">
                        <span><i class="bi bi-shield-lock-fill text-info me-1"></i> Official Syllabus</span>
                        <span>Auto-Advance: 7s</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Navigation Arrows & Pagination Dots Controls Bar -->
    <div class="position-absolute bottom-0 start-0 end-0 p-3 p-md-4 d-flex justify-content-between align-items-center" style="z-index: 20; pointer-events: none;">
        
        <!-- Previous & Next Controls -->
        <div class="d-flex align-items-center gap-2" style="pointer-events: auto;">
            <button type="button" 
                    class="btn btn-sm rounded-circle border border-white border-opacity-25 text-white d-inline-flex align-items-center justify-content-center shadow-lg transition-all"
                    style="width: 42px; height: 42px; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);"
                    @click="prevSlide()"
                    title="Previous Slide (Left Arrow)">
                <i class="bi bi-chevron-left fs-6"></i>
            </button>
            <button type="button" 
                    class="btn btn-sm rounded-circle border border-white border-opacity-25 text-white d-inline-flex align-items-center justify-content-center shadow-lg transition-all"
                    style="width: 42px; height: 42px; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);"
                    @click="nextSlide()"
                    title="Next Slide (Right Arrow)">
                <i class="bi bi-chevron-right fs-6"></i>
            </button>
        </div>

        <!-- 3-Position Pagination Dots -->
        <div class="d-flex align-items-center gap-2" style="pointer-events: auto;">
            <template x-for="(s, idx) in slides" :key="'dot-' + s.id">
                <button type="button" 
                        @click="goToSlide(idx)"
                        class="border-0 rounded-pill transition-all p-0"
                        :style="currentIndex === idx 
                            ? 'width: 32px; height: 8px; background: var(--lms-accent, #723EC3);' 
                            : 'width: 10px; height: 8px; background: rgba(255, 255, 255, 0.35);'"
                        :title="'Jump to Slide ' + (idx + 1)">
                </button>
            </template>
        </div>

    </div>

</section>

<!-- Custom Styles for Ken Burns and Crossfade -->
<style>
    .master-hero-slider-section {
        position: relative;
    }
    .ken-burns-img {
        transform: scale(1);
        transition: transform 9s cubic-bezier(0.25, 1, 0.5, 1);
        will-change: transform;
    }
    .ken-burns-img.animate-ken-burns {
        transform: scale(1.08) translate(-1%, -1%);
    }
    @media (prefers-reduced-motion: reduce) {
        .ken-burns-img {
            transform: none !important;
            transition: none !important;
        }
    }
</style>

