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
                        ['name' => 'Cambridge Primary Checkpoint', 'lessons' => '12+ subjects', 'icon' => 'code-window', 'url' => route('courses.index')],
                        ['name' => 'Cambridge Lower Secondary', 'lessons' => '18+ subjects', 'icon' => 'data-network', 'url' => route('courses.index')],
                        ['name' => 'Cambridge IGCSE', 'lessons' => '24+ subjects', 'icon' => 'it-software', 'url' => route('courses.index')],
                        ['name' => 'Cambridge AS & A Level', 'lessons' => '30+ subjects', 'icon' => 'ui-ux', 'url' => route('courses.index')],
                        ['name' => 'Cambridge Mathematics (9709)', 'lessons' => 'Pure Math & Mechanics', 'icon' => 'marketing', 'url' => route('courses.index')],
                        ['name' => 'Cambridge Physics (9702)', 'lessons' => 'Theory & Practical Labs', 'icon' => 'cybersecurity', 'url' => route('courses.index')],
                        ['name' => 'Cambridge Chemistry (9701)', 'lessons' => 'Inorganic & Organic', 'icon' => 'finance', 'url' => route('courses.index')],
                        ['name' => 'Cambridge Biology (9700)', 'lessons' => 'Cell Biology & Genetics', 'icon' => 'health', 'url' => route('courses.index')],
                    ],
                    'highlights' => [
                        ['icon' => 'bi-globe2', 'text' => '100% Cambridge Assessment Aligned'],
                        ['icon' => 'bi-star-fill text-warning', 'text' => '98% Pass & Merit Distinction Rate'],
                        ['icon' => 'bi-mortarboard-fill text-primary', 'text' => 'Primary, Lower Sec, IGCSE & A-Level'],
                    ],
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_CATALOG,
                'order_index' => 3,
                'content_json' => [
                    'tagline' => 'Curated Programs',
                    'title' => 'Explore Cambridge Courses',
                ],
            ],
            [
                'section_type' => CmsSection::TYPE_MENTORS,
                'order_index' => 4,
                'content_json' => [
                    'title' => 'Meet Our Mentors',
                    'subtitle' => 'Learn from accredited Cambridge international educators and subject matter examiners.',
                    'mentors' => [
                        [
                            'name' => 'Darren Treadway',
                            'role' => 'Cambridge Physics & Mechanics Head',
                            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                            'students' => '28k+',
                            'courses' => '8',
                            'rating' => '98%',
                        ],
                        [
                            'name' => 'Tammi Allison',
                            'role' => 'Cambridge Chemistry Specialist',
                            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                            'students' => '19k+',
                            'courses' => '12',
                            'rating' => '96%',
                        ],
                        [
                            'name' => 'Micheal Cabot',
                            'role' => 'Cambridge Pure Mathematics Lead',
                            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                            'students' => '24k+',
                            'courses' => '10',
                            'rating' => '97%',
                        ],
                        [
                            'name' => 'Sofia Bennett',
                            'role' => 'Cambridge Biology & Genetics Instructor',
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
                    'subtitle' => 'Join live revision workshops, past paper reviews, and exam clinics in real-time.',
                    'view_all_text' => 'View All Events',
                    'view_all_url' => route('courses.index'),
                    'featured_image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
                    'events' => [
                        [
                            'title' => 'Cambridge IGCSE & A-Level Exam Masterclass',
                            'date' => 'Jul 02, 2026',
                            'location' => 'Live Online Session',
                            'price' => '£49',
                            'price_label' => '/ comprehensive',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'Cambridge Mathematics (9709) Problem Clinic',
                            'date' => 'Jul 10, 2026',
                            'location' => 'Interactive Virtual Lab',
                            'price' => 'Free',
                            'price_label' => '/ limited seats',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'Cambridge Sciences Practical Exam Workshop',
                            'date' => 'Jul 20, 2026',
                            'location' => 'Cambridge & Online',
                            'price' => '£39',
                            'price_label' => '/ early bird',
                            'reserve_url' => route('courses.index'),
                        ],
                        [
                            'title' => 'Lower Secondary Checkpoint Revision Clinic',
                            'date' => 'Jul 28, 2026',
                            'location' => 'Virtual Classroom',
                            'price' => '£15',
                            'price_label' => '/ student pass',
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
                    'subtitle' => 'Everything you need to know about AllThingsCambridge. Find clarity in seconds.',
                    'support_title' => 'Still Need Help?',
                    'support_subtitle' => 'Our academic counseling and exam support team is here to assist learners and parents.',
                    'support_email' => 'support@allthingscambridge.com',
                    'support_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
                    'faqs' => [
                        [
                            'question' => 'What makes AllThingsCambridge different from other LMS platforms?',
                            'answer' => 'AllThingsCambridge is purpose-built exclusively for Cambridge International curricula (Primary Checkpoint, Lower Secondary, IGCSE, and AS & A Levels). We offer rigorous syllabus alignment, verified past papers, and interactive marking schemes.',
                        ],
                        [
                            'question' => 'Are your courses aligned with Cambridge International syllabus standards?',
                            'answer' => 'Yes! Every course, assessment, and lesson module is meticulously mapped against official Cambridge assessment objectives (AO1, AO2, AO3) and updated annually.',
                        ],
                        [
                            'question' => 'Can parents monitor their child\'s progress and grades?',
                            'answer' => 'Absolutely. Parents have a dedicated Parent Portal where they can monitor live attendance, view exam score cards, download term reports, and communicate directly with educators.',
                        ],
                        [
                            'question' => 'Are past paper solutions and marking schemes included?',
                            'answer' => 'Yes. Learners gain access to worked step-by-step video solutions, examiner tip sheets, and self-assessment rubrics for recent Cambridge examination series.',
                        ],
                        [
                            'question' => 'Can I access lessons on mobile and tablet?',
                            'answer' => 'Yes. Our platform is fully responsive and supports offline study caching across desktop, laptop, tablet, and smartphone devices.',
                        ],
                        [
                            'question' => 'Can schools and tutoring centers enroll cohorts?',
                            'answer' => 'Yes. Our institutional console offers multi-seat management, custom grading scales, teacher gradebooks, and cohort progress reports.',
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
