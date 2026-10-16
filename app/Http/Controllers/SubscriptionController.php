<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display Cambridge 5 Assessment Levels Subscription Plans.
     */
    public function index(): View
    {
        $categories = CourseCategory::whereIn('slug', [
            'primary-checkpoint',
            'lower-secondary',
            'igcse',
            'as-level',
            'a-level',
        ])->orderBy('id')->get();

        $plans = SubscriptionPlan::where('is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('duration_term')
            ->get()
            ->groupBy('category_id');

        $activeSubscriptions = collect();
        if (Auth::check()) {
            $activeSubscriptions = UserSubscription::where('user_id', Auth::id())
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->where('ends_at', '>', now())
                ->get()
                ->keyBy('category_id');
        }

        return view('pages.pricing', compact('categories', 'plans', 'activeSubscriptions'));
    }

    /**
     * Checkout initiation (Stripe Checkout simulation/ready).
     */
    public function checkout(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = Auth::user();

        // In test mode without live Stripe webhook, create active subscription record
        $durationMonths = $plan->duration_term === SubscriptionPlan::DURATION_12_MONTHS ? 12 : 3;

        UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'category_id' => $plan->category_id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'gateway' => 'STRIPE',
            'gateway_subscription_id' => 'sub_sim_' . uniqid(),
            'starts_at' => now(),
            'ends_at' => now()->addMonths($durationMonths),
        ]);

        return redirect()->route('courses.index', ['category' => $plan->category?->slug])
            ->with('success', sprintf('Congratulations! You now have active access to %s.', $plan->name));
    }
}
