<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSampleSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Syllabus & Scoring',
                'question' => 'How does the 5-tier Cambridge Assessment Isolation work?',
                'answer' => 'To prevent curriculum confusion, our platform isolates the 5 distinct Cambridge tiers (Primary Checkpoint, Lower Secondary, IGCSE, AS Level, and A Level). A student subscribed to A-Level Mathematics accesses only relevant A-Level resources and syllabus requirements.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'category' => 'Syllabus & Scoring',
                'question' => 'How does the automated Esther Math Past-Paper Tracker work?',
                'answer' => 'Whenever you complete a past paper series (e.g. May/June 2026 P1), you key in your raw score (e.g. 45/50). The system immediately calculates your percentage and color-codes the cell: Green (>=75% Mastery), Amber (50-74% Developing), or Red (<50% Remedial). Red/Amber cells feature an instant One-Click Retake Paper action.',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'category' => 'Parent Portal',
                'question' => 'How do Parent and Student accounts link securely?',
                'answer' => 'Inside the student dashboard, clicking "Generate Parent Invite" produces a unique, secure 6-character code (e.g. CMP-742K) valid for 48 hours. The parent enters this code to instantly link. Parents can view high-level progress, study streaks, and Esther Math matrices across multiple children, but cannot alter student quiz submissions or timers.',
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'category' => 'General',
                'question' => 'What resources are Free vs. Paid (Freemium Gatekeeper)?',
                'answer' => 'Standard unworked past exam question papers and official Cambridge syllabus specifications remain completely free to download. Step-by-step worked solutions, downloadable revision summaries, topical past paper packs, video explanations, interactive flashcards, and the automated performance tracker require an active subscription.',
                'sort_order' => 4,
                'is_published' => true,
            ],
            [
                'category' => 'Technical & LaTeX',
                'question' => 'Are mathematical equations and chemical formulas rendered clearly?',
                'answer' => 'Yes! We use native LaTeX and KaTeX rendering engines for Cambridge Mathematics (9709), Physics (9702), and Chemistry. Equations, integrals, matrices, fractions, and chemical notations render razor-sharp across smartphones, tablets, and desktops.',
                'sort_order' => 5,
                'is_published' => true,
            ],
            [
                'category' => 'Subscriptions & Payments',
                'question' => 'What payment options and subscription terms are available?',
                'answer' => 'Subscriptions are priced in British Pounds (GBP / £) for 3-Month and 12-Month recurring terms powered by Stripe (accepting UK cards, international cards, Apple Pay, Google Pay, and PayPal).',
                'sort_order' => 6,
                'is_published' => true,
            ],
        ];

        foreach ($faqs as $item) {
            Faq::firstOrCreate(['question' => $item['question']], $item);
        }
    }
}
