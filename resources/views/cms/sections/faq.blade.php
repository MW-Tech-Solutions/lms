@php
    $c = $section->content_json ?? [];
    $rawTitle = $c['title'] ?? 'Frequently Asked Questions';
    $title = trim(str_ireplace(['(admin updated)', '(admin updated', 'admin updated)'], '', $rawTitle));
    $subtitle = $c['subtitle'] ?? 'Everything you need to know about Cambridge preparation, past papers, and account access.';
    
    $supportTitle = $c['support_title'] ?? 'Need Cambridge Advisory?';
    $supportSubtitle = $c['support_subtitle'] ?? 'Our academic support team helps students and teachers excel in Cambridge International examinations.';
    $supportEmail = $c['support_email'] ?? ($site_settings['contact_email'] ?? 'support@allthingscambridge.co.uk');
    $supportImage = $c['support_image'] ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80';

    // Query active FAQs directly from database managed by admin, unique by question
    $dbFaqs = \App\Models\Faq::where('is_published', true)
        ->orderBy('sort_order')
        ->get()
        ->unique(fn($item) => trim(strtolower($item->question)));

    $faqs = [];
    if ($dbFaqs->isNotEmpty()) {
        foreach ($dbFaqs as $f) {
            $faqs[] = [
                'question' => $f->question,
                'answer' => $f->answer,
            ];
        }
    } else {
        $faqs = [
            [
                'question' => 'How does the 5-tier Cambridge Assessment Isolation work?',
                'answer' => 'To prevent curriculum confusion, our platform isolates the 5 distinct Cambridge tiers (Primary Checkpoint, Lower Secondary, IGCSE, AS Level, and A Level). A student subscribed to A-Level Mathematics accesses only relevant A-Level resources and syllabus requirements.',
            ],
            [
                'question' => 'How does the automated Esther Math Past-Paper Tracker work?',
                'answer' => 'Whenever you complete a past paper series (e.g. May/June 2026 P1), you key in your raw score (e.g. 45/50). The system immediately calculates your percentage and color-codes the cell: Green (>=75% Mastery), Amber (50-74% Developing), or Red (<50% Remedial). Red/Amber cells feature an instant One-Click Retake Paper action.',
            ],
            [
                'question' => 'How do Parent and Student accounts link securely?',
                'answer' => 'Inside the student dashboard, clicking "Generate Parent Invite" produces a unique, secure 6-character code (e.g. CMP-742K) valid for 48 hours. The parent enters this code to instantly link. Parents can view high-level progress, study streaks, and Esther Math matrices across multiple children, but cannot alter student quiz submissions or timers.',
            ],
            [
                'question' => 'What resources are Free vs. Paid (Freemium Gatekeeper)?',
                'answer' => 'Standard unworked past exam question papers and official Cambridge syllabus specifications remain completely free to download. Step-by-step worked solutions, downloadable revision summaries, topical past paper packs, video explanations, interactive flashcards, and the automated performance tracker require an active subscription.',
            ],
            [
                'question' => 'Are mathematical equations and chemical formulas rendered clearly?',
                'answer' => 'Yes! We use native LaTeX and KaTeX rendering engines for Cambridge Mathematics (9709), Physics (9702), and Chemistry. Equations, integrals, matrices, fractions, and chemical notations render razor-sharp across smartphones, tablets, and desktops.',
            ],
            [
                'question' => 'What payment options and subscription terms are available?',
                'answer' => 'Subscriptions are priced in British Pounds (GBP / £) for 3-Month and 12-Month recurring terms powered by Stripe (accepting UK cards, international cards, Apple Pay, Google Pay, and PayPal).',
            ],
        ];
    }
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
