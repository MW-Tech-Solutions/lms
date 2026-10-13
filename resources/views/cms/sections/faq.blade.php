@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Frequently Asked Questions';
    $subtitle = $c['subtitle'] ?? 'Everything you need to know about ' . ($site_settings['brand_name'] ?? 'Lerntix') . '. Find clarity in seconds.';
    
    $supportTitle = $c['support_title'] ?? 'Still Need Help?';
    $supportSubtitle = $c['support_subtitle'] ?? 'Our support team is available 24/7 to guide you through your learning journey.';
    $supportEmail = $c['support_email'] ?? ($site_settings['contact_email'] ?? 'hello@lerntix.com');
    $supportImage = $c['support_image'] ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80';

    $defaultFaqs = [
        [
            'question' => 'What makes ' . ($site_settings['brand_name'] ?? 'Lerntix') . ' different from other LMS platforms?',
            'answer' => ($site_settings['brand_name'] ?? 'Lerntix') . ' combines AI-driven learning paths, hands-on labs, and real-time progress analytics. Unlike traditional LMS, we offer adaptive quizzes, industry-verified certificates, and a vibrant mentor community — all in one intuitive dashboard.',
        ],
        [
            'question' => 'Are the certificates recognized by employers?',
            'answer' => 'Yes! Every certificate issued contains a unique verification UUID and QR code that can be authenticated instantly by employers and university registries worldwide.',
        ],
        [
            'question' => 'Can I try ' . ($site_settings['brand_name'] ?? 'Lerntix') . ' before subscribing?',
            'answer' => 'Absolutely. You can explore foundational modules and select workshops completely free before deciding on individual or enterprise corporate subscriptions.',
        ],
        [
            'question' => 'Are the courses self-paced or live?',
            'answer' => 'Both. We offer flexible self-paced video modules with interactive coding playgrounds alongside scheduled live masterclasses and instructor office hours.',
        ],
        [
            'question' => 'Is there a mobile app for offline learning?',
            'answer' => 'Yes. Our platform is completely responsive and supports offline lesson caching so you can study smoothly on smartphones, tablets, or laptops anywhere.',
        ],
        [
            'question' => 'Can teams or businesses use ' . ($site_settings['brand_name'] ?? 'Lerntix') . '?',
            'answer' => 'Yes. Our dedicated Corporate Console features CBAC role assignment, SCORM packages, employee completion matrices, and automated compliance recertification engines.',
        ],
    ];

    $faqs = !empty($c['faqs']) ? $c['faqs'] : $defaultFaqs;
@endphp

<section class="faq-section py-5 position-relative" style="background: var(--lms-bg-body);"
         x-data="{ activeIndex: 0 }">
    <div class="container py-3">
        
        <!-- Centered Header -->
        <div class="text-center mb-5 max-w-700 mx-auto">
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                {{ $title }}
            </h2>
            <p class="text-secondary small mb-0" style="font-size: 0.95rem;">
                {{ $subtitle }}
            </p>
        </div>

        <!-- 2 Columns: Left Support Card + Right Accordion -->
        <div class="row g-4 g-lg-5 align-items-start position-relative">
            
            <!-- Left Column: Still Need Help Card -->
            <div class="col-lg-5">
                <div class="lms-support-card p-4 rounded-4 shadow-sm border position-relative" 
                     style="background: var(--lms-surface-card); border-color: var(--lms-border) !important; border-radius: 24px;">
                    
                    <!-- Support Photo Frame -->
                    <div class="support-image-frame rounded-4 overflow-hidden mb-4 shadow-sm" style="max-height: 220px;">
                        <img src="{{ $supportImage }}" 
                             alt="Customer Support Desk" 
                             class="w-100 h-100 object-fit-cover">
                    </div>

                    <h3 class="h4 fw-bold mb-2" style="color: var(--lms-text-primary); font-family: var(--lms-font-heading);">
                        {{ $supportTitle }}
                    </h3>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">
                        {{ $supportSubtitle }}
                    </p>

                    <!-- Contact Pill Badge -->
                    <a href="mailto:{{ $supportEmail }}" 
                       class="btn btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 border text-decoration-none"
                       style="background: rgba(114, 62, 195, 0.08); color: var(--lms-accent, #723EC3); border-color: rgba(114, 62, 195, 0.2) !important;">
                        <i class="bi bi-envelope"></i>
                        <span class="font-mono fw-semibold" style="font-size: 0.85rem;">{{ $supportEmail }}</span>
                    </a>
                </div>
            </div>

            <!-- Decorative Center Geometric Star (visible on large displays) -->
            <div class="d-none d-xl-block position-absolute start-50 top-50 translate-middle" style="width: 60px; pointer-events: none; z-index: 1;">
                <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27 3C28.5 18 36 25.5 51 27C36 28.5 28.5 36 27 51C25.5 36 18 28.5 3 27C18 25.5 25.5 18 27 3Z" fill="var(--lms-accent, #723EC3)" opacity="0.8"/>
                </svg>
            </div>

            <!-- Right Column: Interactive Accordion List -->
            <div class="col-lg-7 ps-xl-4">
                <div class="d-flex flex-column gap-3">
                    @foreach($faqs as $index => $faq)
                        <div class="faq-item-card border-bottom pb-3 transition-all"
                             style="border-color: var(--lms-border) !important;">
                            
                            <!-- Question Button -->
                            <button type="button" 
                                    class="btn w-100 p-0 text-start d-flex justify-content-between align-items-center gap-3 border-0 bg-transparent shadow-none"
                                    @click="activeIndex = (activeIndex === {{ $index }} ? null : {{ $index }})">
                                
                                <span class="fw-bold fs-6 transition-all" 
                                      :style="activeIndex === {{ $index }} ? 'color: var(--lms-accent, #723EC3);' : 'color: var(--lms-text-primary);'"
                                      style="font-size: 1rem; line-height: 1.4;">
                                    {{ $faq['question'] }}
                                </span>

                                <span class="faq-toggle-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                      :style="activeIndex === {{ $index }} ? 'color: var(--lms-accent, #723EC3); transform: rotate(45deg);' : 'color: var(--lms-text-secondary);'"
                                      style="width: 28px; height: 28px; font-size: 1.25rem; transition: transform 0.25s ease;">
                                    <i class="bi bi-plus-lg"></i>
                                </span>
                            </button>

                            <!-- Accordion Answer Body -->
                            <div x-show="activeIndex === {{ $index }}" 
                                 x-collapse
                                 x-transition:enter="transition ease-out duration-250"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="pt-2 text-secondary small" 
                                 style="font-size: 0.9rem; line-height: 1.65;"
                                 x-cloak>
                                {{ $faq['answer'] }}
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</section>
