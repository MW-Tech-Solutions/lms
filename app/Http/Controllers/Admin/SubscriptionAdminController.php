<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\SubscriptionPlan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionAdminController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::with('category')->orderBy('category_id')->orderBy('duration_term')->paginate(15);
        $categories = CourseCategory::all();

        return view('admin.subscriptions.index', compact('plans', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'duration_term' => ['required', 'in:3_MONTHS,12_MONTHS'],
            'price_pounds' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $featuresArray = array_filter(array_map('trim', explode("\n", (string) ($validated['features'] ?? ''))));

        $slug = Str::slug($validated['name']) . '-' . rand(100, 999);

        $plan = SubscriptionPlan::create([
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'duration_term' => $validated['duration_term'],
            'price_pence' => (int) round(((float) $validated['price_pounds']) * 100),
            'currency' => 'GBP',
            'features' => $featuresArray,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditService::log('SUBSCRIPTION_PLAN_CREATED', $plan, null, [
            'created_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Plan "%s" created successfully at £%.2f.', $plan->name, $plan->price_pence / 100));
    }

    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'duration_term' => ['required', 'in:3_MONTHS,12_MONTHS'],
            'price_pounds' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $featuresArray = array_filter(array_map('trim', explode("\n", (string) ($validated['features'] ?? ''))));

        $plan->update([
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'duration_term' => $validated['duration_term'],
            'price_pence' => (int) round(((float) $validated['price_pounds']) * 100),
            'features' => $featuresArray,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditService::log('SUBSCRIPTION_PLAN_UPDATED', $plan, null, [
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Plan "%s" updated to £%.2f.', $plan->name, $plan->price_pence / 100));
    }

    public function destroy(SubscriptionPlan $plan): RedirectResponse
    {
        $name = $plan->name;
        $plan->delete();

        AuditService::log('SUBSCRIPTION_PLAN_DELETED', null, null, [
            'name' => $name,
            'deleted_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Subscription plan "%s" deleted.', $name));
    }
}
