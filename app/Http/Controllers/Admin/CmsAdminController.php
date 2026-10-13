<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsAdminController extends Controller
{
    public function index(): View
    {
        $pages = CmsPage::with(['sections' => fn ($q) => $q->orderBy('order_index')])->get();
        $homePage = CmsPage::with(['sections' => fn ($q) => $q->orderBy('order_index')])->where('slug', 'home')->first();

        return view('admin.cms.index', compact('pages', 'homePage'));
    }

    public function updateSection(Request $request, CmsSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'order_index' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'view_all_text' => ['nullable', 'string', 'max:128'],
            'support_title' => ['nullable', 'string', 'max:255'],
            'support_subtitle' => ['nullable', 'string', 'max:500'],
            'support_email' => ['nullable', 'string', 'max:255'],
            'support_image' => ['nullable', 'string', 'max:500'],
            'featured_image_url' => ['nullable', 'string', 'max:500'],
            'content_raw_json' => ['nullable', 'string'],
        ]);

        $content = $section->content_json ?? [];

        if (!empty($validated['title'])) {
            $content['title'] = $validated['title'];
        }
        if (isset($validated['subtitle'])) {
            $content['subtitle'] = $validated['subtitle'];
        }
        if (isset($validated['tagline'])) {
            $content['tagline'] = $validated['tagline'];
        }
        if (isset($validated['view_all_text'])) {
            $content['view_all_text'] = $validated['view_all_text'];
        }
        if (isset($validated['support_title'])) {
            $content['support_title'] = $validated['support_title'];
        }
        if (isset($validated['support_subtitle'])) {
            $content['support_subtitle'] = $validated['support_subtitle'];
        }
        if (isset($validated['support_email'])) {
            $content['support_email'] = $validated['support_email'];
        }
        if (isset($validated['support_image'])) {
            $content['support_image'] = $validated['support_image'];
        }
        if (isset($validated['featured_image_url'])) {
            $content['featured_image_url'] = $validated['featured_image_url'];
        }

        // If raw JSON was provided for items/array customization
        if (!empty($validated['content_raw_json'])) {
            $decoded = json_decode($validated['content_raw_json'], true);
            if (is_array($decoded)) {
                $content = array_merge($content, $decoded);
            }
        }

        $section->update([
            'order_index' => (int) $validated['order_index'],
            'content_json' => $content,
        ]);

        return back()->with('success', "Section {$section->section_type} (Order #{$section->order_index}) updated successfully!");
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_id' => ['required', 'exists:cms_pages,id'],
            'section_type' => ['required', 'string', 'max:64'],
            'order_index' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
        ]);

        $content = [];
        if (!empty($validated['title'])) {
            $content['title'] = $validated['title'];
        }
        if (!empty($validated['subtitle'])) {
            $content['subtitle'] = $validated['subtitle'];
        }

        CmsSection::create([
            'page_id' => $validated['page_id'],
            'section_type' => strtoupper($validated['section_type']),
            'order_index' => (int) $validated['order_index'],
            'content_json' => $content,
        ]);

        return back()->with('success', "New section {$validated['section_type']} added successfully!");
    }

    public function destroySection(CmsSection $section): RedirectResponse
    {
        $type = $section->section_type;
        $section->delete();

        return back()->with('success', "Section {$type} removed from page.");
    }

    public function seedSamples(): RedirectResponse
    {
        try {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `cms_sections` MODIFY COLUMN `section_type` VARCHAR(64) NOT NULL");
            }
        } catch (\Throwable $e) {}

        $home = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'meta_title' => 'Enterprise Dual-Audience LMS',
                'meta_description' => 'Workforce Compliance and Academic Certification Platform',
                'is_published' => true,
            ]
        );

        // Define rich sample data matching user's exact uploaded screenshots
        $sampleSections = [
            [
                'section_type' => CmsSection::TYPE_HERO,
                'order_index' => 1,
                'content_json' => [
                    'tagline' => '#1 ACCREDITED ONLINE LMS ENGINE',
                    'title' => 'Transform Future Through Online Skill Building.',
                    'subtitle' => 'Unify mandatory corporate compliance recertifications and accredited academic degree tracks under one zero-trust enterprise umbrella.',
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_CATEGORIES,
                'order_index' => 2,
                'content_json' => [
                    'title' => 'Explore Categories',
                    'subtitle' => 'Browse top categories and find the perfect course to grow your skills.',
                    'view_all_text' => 'View All Categories',
                    'view_all_url' => route('courses.index'),
                    'categories' => [
                        ['name' => 'Web Development', 'lessons' => '48+ lessons', 'icon' => 'code-window', 'url' => route('courses.index')],
                        ['name' => 'Data Science', 'lessons' => '52+ lessons', 'icon' => 'data-network', 'url' => route('courses.index')],
                        ['name' => 'IT & Software', 'lessons' => '50+ lessons', 'icon' => 'it-software', 'url' => route('courses.index')],
                        ['name' => 'UI/UX Design', 'lessons' => '38+ lessons', 'icon' => 'ui-ux', 'url' => route('courses.index')],
                        ['name' => 'Marketing', 'lessons' => '36+ lessons', 'icon' => 'marketing', 'url' => route('courses.index')],
                        ['name' => 'Cybersecurity', 'lessons' => '47+ lessons', 'icon' => 'cybersecurity', 'url' => route('courses.index')],
                        ['name' => 'Finance & Accounting', 'lessons' => '25+ lessons', 'icon' => 'finance', 'url' => route('courses.index')],
                        ['name' => 'Health & Fitness', 'lessons' => '20+ lessons', 'icon' => 'health', 'url' => route('courses.index')],
                    ],
                    'highlights' => [
                        ['icon' => 'bi-globe2', 'text' => '500+ Expert-led categories'],
                        ['icon' => 'bi-star-fill text-warning', 'text' => '94% Career growth rate'],
                        ['icon' => 'bi-mortarboard-fill text-primary', 'text' => 'New category every month'],
                    ],
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_CATALOG,
                'order_index' => 3,
                'content_json' => [
                    'tagline' => 'Curated Programs',
                    'title' => 'Explore Dual-Audience Courses',
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_MENTORS,
                'order_index' => 4,
                'content_json' => [
                    'title' => 'Meet Our Mentors',
                    'subtitle' => 'Learn from industry experts who bring real-world experience straight to your screen.',
                    'mentors' => [
                        [
                            'name' => 'Darren Treadway',
                            'role' => 'Data Science Instructor',
                            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                            'students' => '28k+',
                            'courses' => '8',
                            'rating' => '98%',
                        ],
                        [
                            'name' => 'Tammi Allison',
                            'role' => 'UI/UX Design Mentor',
                            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                            'students' => '19k+',
                            'courses' => '12',
                            'rating' => '96%',
                        ],
                        [
                            'name' => 'Micheal Cabot',
                            'role' => 'Full-Stack Web Instructor',
                            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                            'students' => '24k+',
                            'courses' => '10',
                            'rating' => '97%',
                        ],
                        [
                            'name' => 'Sofia Bennett',
                            'role' => 'Marketing & Growth Coach',
                            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
                            'students' => '31k+',
                            'courses' => '9',
                            'rating' => '95%',
                        ],
                    ],
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_EVENTS,
                'order_index' => 5,
                'content_json' => [
                    'title' => 'Upcoming Live Events',
                    'subtitle' => 'Join mentors, industry experts, and global learners in real-time sessions.',
                    'view_all_text' => 'View All Event',
                    'view_all_url' => route('courses.index'),
                    'featured_image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
                    'events' => [
                        [
                            'title' => "Web3 Builder's Meetup",
                            'date' => 'Jul 02, 2026',
                            'location' => 'Singapore',
                            'price' => '$79',
                            'price_label' => '/ networking',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'Data Science Bootcamp',
                            'date' => 'Jul 10, 2026',
                            'location' => 'Online (Zoom + Discord)',
                            'price' => 'Free',
                            'price_label' => '/ limited seats',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'UI/UX Design Hackathon',
                            'date' => 'Jul 20, 2026',
                            'location' => 'Berlin & Online',
                            'price' => '$49',
                            'price_label' => '/ early bird',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'No-Code Hackathon',
                            'date' => 'Jul 28, 2026',
                            'location' => 'Virtual + Global',
                            'price' => '$15',
                            'price_label' => '/ student discount',
                            'reserve_url' => route('courses.index'),
                        ],
                    ],
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_FAQ,
                'order_index' => 6,
                'content_json' => [
                    'title' => 'Frequently Asked Questions',
                    'subtitle' => 'Everything you need to know about Lerntix. Find clarity in seconds.',
                    'support_title' => 'Still Need Help?',
                    'support_subtitle' => 'Our support team is available 24/7 to guide you through your learning journey.',
                    'support_email' => 'hello@lerntix.com',
                    'support_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
                    'faqs' => [
                        [
                            'question' => 'What makes Lerntix different from other LMS platforms?',
                            'answer' => 'Lerntix combines AI-driven learning paths, hands-on labs, and real-time progress analytics. Unlike traditional LMS, we offer adaptive quizzes, industry-verified certificates, and a vibrant mentor community — all in one intuitive dashboard.',
                        ],
                        [
                            'question' => 'Are the certificates recognized by employers?',
                            'answer' => 'Yes! Every certificate issued contains a unique verification UUID and QR code that can be authenticated instantly by employers and university registries worldwide.',
                        ],
                        [
                            'question' => 'Can I try Lerntix before subscribing?',
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
                            'question' => 'Can teams or businesses use Lerntix?',
                            'answer' => 'Yes. Our dedicated Corporate Console features CBAC role assignment, SCORM packages, employee completion matrices, and automated compliance recertification engines.',
                        ],
                    ],
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_DYNAMIC_FORM,
                'order_index' => 7,
                'content_json' => [
                    'title' => 'Inquire for Enterprise Deployment',
                    'subtitle' => 'Our enterprise solutions architects will design your custom CBAC permissions hierarchy, automated recertification cadence, and SSO integration.',
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_PRICING,
                'order_index' => 8,
                'content_json' => [
                    'title' => 'Transparent Enterprise & Academic Tiers',
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_FOOTER,
                'order_index' => 9,
                'content_json' => [],
            ],
        ];

        // Clear existing homepage sections and re-seed cleanly
        CmsSection::where('page_id', $home->id)->delete();

        foreach ($sampleSections as $sec) {
            CmsSection::create([
                'page_id' => $home->id,
                'section_type' => $sec['section_type'],
                'order_index' => $sec['order_index'],
                'content_json' => $sec['content_json'],
            ]);
        }

        return back()->with('success', 'Homepage sections successfully synced with sample screenshot modules (Categories, Mentors, Events, FAQ, Catalog, Bento, and Forms)!');
    }
}
