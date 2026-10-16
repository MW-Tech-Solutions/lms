<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?: 'Inquiry via AllThingsCambridge Contact Portal',
            'message' => $validated['message'],
            'status' => 'PENDING',
        ]);

        AuditService::log('CONTACT_MESSAGE_SUBMITTED', $message, null, [
            'from_name' => $message->name,
            'from_email' => $message->email,
        ]);

        // Send confirmation notification to user
        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.contact_confirmation',
                [
                    'messageRecord' => $message,
                    'subject' => "We received your inquiry: {$message->subject}",
                ],
                function ($m) use ($message) {
                    $m->to($message->email)->subject("We received your inquiry: {$message->subject}");
                }
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Contact confirmation email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you! Your message has been received. Our team will review your inquiry and respond to your email shortly.');
    }
}
