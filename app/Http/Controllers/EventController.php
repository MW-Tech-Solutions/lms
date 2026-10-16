<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventReservation;
use App\Models\LiveEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $level = trim((string) $request->get('level', ''));

        $query = LiveEvent::where('is_active', true)
            ->where('event_date', '>=', now()->subDays(2))
            ->orderBy('event_date');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($level !== '') {
            $query->where('level_badge', 'like', "%{$level}%");
        }

        $events = $query->paginate(9)->withQueryString();

        return view('events.index', compact('events', 'search', 'level'));
    }

    public function reserve(Request $request, LiveEvent $event): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (!$event->is_active) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'This event is currently closed for reservations.'], 422);
            }
            return back()->with('error', 'This event is currently closed for reservations.');
        }

        if ($event->remaining_seats <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'All seats for this live session have been fully booked.'], 422);
            }
            return back()->with('error', 'All seats for this live session have been fully booked.');
        }

        // Check if already reserved
        $existing = EventReservation::where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existing) {
            $msg = 'You have already submitted a reservation for this session. Your current status is: ' . $existing->status;
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg, 'reservation' => $existing]);
            }
            return back()->with('info', $msg);
        }

        $reservation = EventReservation::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'PENDING',
        ]);

        $event->increment('reserved_count');

        $successMsg = 'Your spot has been reserved! Our academic team will review and email your confirmation link shortly.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMsg, 'reservation' => $reservation]);
        }

        return back()->with('success', $successMsg);
    }
}
