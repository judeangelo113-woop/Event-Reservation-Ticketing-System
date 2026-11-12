<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Display all events
    public function index()
    {
        $events = Event::with('reservations')->get();

        return view('events.index', compact('events'));
    }

    // Show the create event form
    public function create()
    {
        return view('events.create');
    }

    // Save a new event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $validated['available_slots'] = $validated['capacity'];
        $validated['status'] = 'OPEN';

        Event::create($validated);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event created successfully!');
    }

    // Display one event
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    // Show the edit event form
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    // Update an event
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $reserved = $event->reservations()
            ->where('status', 'CONFIRMED')
            ->sum('quantity');

        if ($validated['capacity'] < $reserved) {
            return back()->withErrors([
                'capacity' => 'Capacity cannot be lower than the number of reserved tickets.'
            ])->withInput();
        }

        $validated['available_slots'] = $validated['capacity'] - $reserved;

        $event->update($validated);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event updated successfully!');
    }

    // Delete an event
    public function destroy(Event $event)
    {
        if ($event->reservations()->exists()) {
            return back()->with(
                'error',
                'This event cannot be deleted because it has existing reservations.'
            );
        }

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }
}
