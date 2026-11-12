<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAnnouncement;
use Illuminate\Http\Request;

class EventAnnouncementController extends Controller
{
    // Display all announcements
    public function index()
    {
        $announcements = EventAnnouncement::with('event')->get();

        return view('announcements.index', compact('announcements'));
    }

    // Show the create announcement form
    public function create()
    {
        $events = Event::all();

        return view('announcements.create', compact('events'));
    }

    // Save a new announcement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['published_at'] = now();

        EventAnnouncement::create($validated);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement published successfully!');
    }

    // Display one announcement
    public function show(EventAnnouncement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    // Show the edit announcement form
    public function edit(EventAnnouncement $announcement)
    {
        $events = Event::all();

        return view('announcements.edit', compact('announcement', 'events'));
    }

    // Update an announcement
    public function update(Request $request, EventAnnouncement $announcement)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $announcement->update($validated);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    // Delete an announcement
    public function destroy(EventAnnouncement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}
