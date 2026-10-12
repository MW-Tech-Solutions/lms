<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsController extends Controller
{
    /**
     * Render dynamic CMS Page and its ordered section blocks.
     */
    public function show(string $slug = 'home'): View
    {
        // 1. If a dedicated custom view exists in resources/views/pages/{slug}.blade.php
        if (view()->exists('pages.' . $slug)) {
            return view('pages.' . $slug);
        }

        $page = CmsPage::with(['sections' => fn ($q) => $q->orderBy('order_index')])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$page) {
            // Auto-fallback or 404
            if ($slug === 'home') {
                $page = new CmsPage([
                    'title' => 'Home',
                    'slug' => 'home',
                    'meta_title' => 'Enterprise Dual-Audience LMS',
                    'meta_description' => 'Workforce Compliance and Academic Certification Platform',
                    'is_published' => true,
                ]);
            } else {
                abort(404, 'CMS Page not found.');
            }
        }

        return view('cms.page', compact('page'));
    }

    /**
     * Handle public inquiry dynamic form submissions.
     */
    public function handleFormSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'work_email' => ['required', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Thank you! Your enterprise inquiry has been submitted. Our compliance solutions team will contact you within 24 hours.');
    }
}
