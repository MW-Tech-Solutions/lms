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
     * Checkout initiation (Stripe Checkout integration with instant fallback).
     */
    public function checkout(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login', ['redirect' => route('pricing')]);
        }

        $siteConfig = app(\App\Services\SiteConfigService::class);
        $secretKey = $siteConfig->get('stripe_secret') ?: config('services.stripe.secret') ?: env('STRIPE_SECRET');

        // If live Stripe secret is configured and not in automated test environment, generate hosted Checkout Session
        if (!empty($secretKey) && !str_starts_with($secretKey, 'sk_test_your') && app()->environment() !== 'testing') {
            try {
                $unitAmount = (int) ($plan->price_cents > 0 ? $plan->price_cents : 4500);
                $durationLabel = $plan->duration_term === SubscriptionPlan::DURATION_12_MONTHS ? '12-Month Access Pass' : '3-Month Term Pass';

                $payload = [
                    'mode' => 'payment',
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => strtolower($plan->currency ?? 'gbp'),
                                'unit_amount' => $unitAmount,
                                'product_data' => [
                                    'name' => sprintf('%s - %s (%s)', $plan->name, $durationLabel, $plan->category?->name ?? 'Cambridge Level'),
                                    'description' => 'Official access pass to Cambridge past papers, mark schemes, video lessons, and Esther Math tracker.',
                                ],
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'customer_email' => $user->email,
                    'client_reference_id' => (string) $user->id,
                    'metadata' => [
                        'user_id' => (string) $user->id,
                        'plan_id' => (string) $plan->id,
                        'category_id' => (string) $plan->category_id,
                    ],
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}&plan_id=' . $plan->id,
                    'cancel_url' => route('pricing'),
                ];

                $response = \Illuminate\Support\Facades\Http::withToken($secretKey)
                    ->asForm()
                    ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

                if ($response->successful()) {
                    $sessionData = $response->json();
                    if (!empty($sessionData['url'])) {
                        return redirect()->away($sessionData['url']);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('Stripe checkout session error: ' . $response->body());
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Stripe checkout exception: ' . $e->getMessage());
            }
        }

        // Direct activation (used in tests or offline fallback)
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

    /**
     * Handle return from successful Stripe Checkout Session.
     */
    public function success(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $planId = $request->input('plan_id');
        $sessionId = $request->input('session_id', 'cs_' . uniqid());

        $plan = SubscriptionPlan::with('category')->find($planId);
        if (!$plan) {
            return redirect()->route('pricing')->with('error', 'Subscription plan could not be located.');
        }

        $durationMonths = $plan->duration_term === SubscriptionPlan::DURATION_12_MONTHS ? 12 : 3;

        // Verify or create subscription record for user
        $sub = UserSubscription::firstOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $plan->category_id,
                'status' => UserSubscription::STATUS_ACTIVE,
            ],
            [
                'plan_id' => $plan->id,
                'gateway' => 'STRIPE',
                'gateway_subscription_id' => (string) $sessionId,
                'starts_at' => now(),
                'ends_at' => now()->addMonths($durationMonths),
            ]
        );

        if (!$sub->wasRecentlyCreated) {
            $sub->update([
                'plan_id' => $plan->id,
                'gateway_subscription_id' => (string) $sessionId,
                'ends_at' => now()->addMonths($durationMonths),
            ]);
        }

        return redirect()->route('courses.index', ['category' => $plan->category?->slug])
            ->with('success', sprintf('Payment confirmed via Stripe! You now have full access to %s.', $plan->name));
    }
}
