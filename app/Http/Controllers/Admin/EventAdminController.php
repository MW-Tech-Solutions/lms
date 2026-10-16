<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EventReservationApprovedMail;
use App\Models\CourseCategory;
use App\Models\EventReservation;
use App\Models\LiveEvent;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventAdminController extends Controller
{
    public function index(Request $request): View
    {
        $events = LiveEvent::withCount('reservations')->latest('event_date')->paginate(10, ['*'], 'events_page');
        
        $reservationStatus = strtoupper((string) $request->get('reservation_status', 'ALL'));
        $resQuery = EventReservation::with('event')->latest();

        if (in_array($reservationStatus, ['PENDING', 'CONFIRMED', 'REJECTED'], true)) {
            $resQuery->where('status', $reservationStatus);
        }

        $reservations = $resQuery->paginate(15, ['*'], 'reservations_page')->withQueryString();

        $categories = CourseCategory::all();

        return view('admin.events.index', compact('events', 'reservations', 'reservationStatus', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'level_badge' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'instructor_name' => ['required', 'string', 'max:255'],
            'instructor_role' => ['required', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['title']) . '-' . rand(100, 999);

        $event = LiveEvent::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_id' => $validated['category_id'] ?? null,
            'level_badge' => $validated['level_badge'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'],
            'duration_minutes' => (int) $validated['duration_minutes'],
            'instructor_name' => $validated['instructor_name'],
            'instructor_role' => $validated['instructor_role'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'max_capacity' => (int) $validated['max_capacity'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditService::log('EVENT_CREATED', $event, null, [
            'created_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Live Event "%s" created successfully.', $event->title));
    }

    public function update(Request $request, LiveEvent $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'level_badge' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'instructor_name' => ['required', 'string', 'max:255'],
            'instructor_role' => ['required', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $event->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? null,
            'level_badge' => $validated['level_badge'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'],
            'duration_minutes' => (int) $validated['duration_minutes'],
            'instructor_name' => $validated['instructor_name'],
            'instructor_role' => $validated['instructor_role'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'max_capacity' => (int) $validated['max_capacity'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditService::log('EVENT_UPDATED', $event, null, [
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Event "%s" updated successfully.', $event->title));
    }

    public function toggleActive(LiveEvent $event): RedirectResponse
    {
        $event->is_active = !$event->is_active;
        $event->save();

        AuditService::log('EVENT_STATUS_TOGGLED', $event, null, [
            'is_active' => $event->is_active,
            'toggled_by' => auth()->user()->name ?? 'Administrator',
        ]);

        $stateText = $event->is_active ? 'Active' : 'Inactive';
        return back()->with('success', sprintf('Event "%s" is now %s.', $event->title, $stateText));
    }

    public function destroy(LiveEvent $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();

        AuditService::log('EVENT_DELETED', null, null, [
            'title' => $title,
            'deleted_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Event "%s" deleted.', $title));
    }

    public function updateReservationStatus(Request $request, EventReservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:CONFIRMED,REJECTED,PENDING'],
        ]);

        $newStatus = $validated['status'];
        $reservation->status = $newStatus;

        if ($newStatus === 'CONFIRMED') {
            $reservation->approval_email_sent_at = now();

            try {
                Mail::to($reservation->email)->send(new EventReservationApprovedMail($reservation));
            } catch (\Throwable $e) {
                Log::warning('Event reservation email delivery failed: ' . $e->getMessage());
            }
        }

        $reservation->save();

        AuditService::log('RESERVATION_STATUS_UPDATED', $reservation, null, [
            'new_status' => $newStatus,
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ]);

        $msg = $newStatus === 'CONFIRMED'
            ? sprintf('Reservation for %s approved! Confirmation email dispatched to %s.', $reservation->name, $reservation->email)
            : sprintf('Reservation for %s marked as %s.', $reservation->name, $newStatus);

        return back()->with('success', $msg);
    }
}
