<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ExamNotificationSubscribedMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse|JsonResponse
    {
        $email = $request->input('email') ?? $request->input('work_email');

        $validated = validator(['email' => $email], [
            'email' => ['required', 'email', 'max:255'],
        ])->validate();

        $levelInterest = $request->input('level_interest', 'All Cambridge Levels');

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'level_interest' => $levelInterest,
                'is_active' => true,
                'subscribed_at' => now(),
            ]
        );

        try {
            Mail::to($subscriber->email)->send(new ExamNotificationSubscribedMail($subscriber));
        } catch (\Throwable $e) {
            Log::warning('Exam notification confirmation email delivery failed: ' . $e->getMessage());
        }

        $msg = 'Thank you for subscribing to Cambridge Exam Notifications! A confirmation email has been dispatched.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}
