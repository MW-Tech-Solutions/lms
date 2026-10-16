<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqAdminController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::orderBy('sort_order')->latest('id')->paginate(15);
        $categories = ['General', 'Syllabus & Scoring', 'Subscriptions & Payments', 'Parent Portal', 'Technical & LaTeX'];

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $faq = Faq::create([
            'category' => $validated['category'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_published' => $request->boolean('is_published', true),
        ]);

        AuditService::log('FAQ_CREATED', $faq, null, [
            'created_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question created successfully.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $faq->update([
            'category' => $validated['category'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditService::log('FAQ_UPDATED', $faq, null, [
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        AuditService::log('FAQ_DELETED', null, null, [
            'question' => $faq->question,
            'deleted_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question removed.');
    }
}
