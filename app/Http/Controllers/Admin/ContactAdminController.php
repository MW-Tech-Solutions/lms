<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ContactAdminController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->get('status', 'ALL');
        $search = trim((string) $request->get('search', ''));
        $selectedId = $request->get('selected');

        $query = ContactMessage::with('responder')->latest();

        if ($statusFilter !== 'ALL') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $selectedMessage = null;
        if ($selectedId) {
            $selectedMessage = ContactMessage::with('responder')->find($selectedId);
        } elseif ($messages->count() > 0) {
            $selectedMessage = $messages->first();
        }

        $counts = [
            'all' => ContactMessage::count(),
            'pending' => ContactMessage::where('status', 'PENDING')->count(),
            'responded' => ContactMessage::where('status', 'RESPONDED')->count(),
            'archived' => ContactMessage::where('status', 'ARCHIVED')->count(),
        ];

        return view('admin.contact_messages.index', compact(
            'messages', 'selectedMessage', 'statusFilter', 'search', 'counts'
        ));
    }

    public function reply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string', 'max:5000'],
        ]);

        $admin = auth()->user();
        $replyText = $validated['admin_reply'];

        $contactMessage->admin_reply = $replyText;
        $contactMessage->status = 'RESPONDED';
        $contactMessage->replied_by = $admin?->id;
        $contactMessage->replied_at = Carbon::now();
        $contactMessage->save();

        // Dispatch Styled HTML Email to user
        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.contact_reply',
                [
                    'contactMessage' => $contactMessage,
                    'replyText' => $replyText,
                    'adminName' => $admin?->name ?? 'Academic Support Specialist',
                    'subject' => "Re: {$contactMessage->subject} - AllThingsCambridge Support",
                ],
                function ($m) use ($contactMessage) {
                    $m->to($contactMessage->email)
                      ->subject("Re: {$contactMessage->subject} - AllThingsCambridge Support");
                }
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed dispatching admin contact message reply email: ' . $e->getMessage());
        }

        AuditService::log('CONTACT_MESSAGE_RESPONDED', $contactMessage, null, [
            'replied_by' => $admin?->name ?? 'Administrator',
            'recipient_email' => $contactMessage->email,
        ]);

        return redirect()->route('admin.contact.index', ['selected' => $contactMessage->id])
            ->with('success', sprintf('Reply successfully sent to %s via email.', $contactMessage->email));
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:PENDING,RESPONDED,ARCHIVED'],
        ]);

        $contactMessage->status = $validated['status'];
        $contactMessage->save();

        return back()->with('success', 'Message status updated.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();
        return redirect()->route('admin.contact.index')->with('success', 'Inquiry deleted.');
    }
}
