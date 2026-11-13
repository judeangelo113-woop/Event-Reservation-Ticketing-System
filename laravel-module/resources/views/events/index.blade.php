
@extends('layouts.app')

@section('title', 'Event Management')

@section('content')

<div class="event-management">

    <!-- Page Header -->
    <div class="event-management-header">

        <div>
            <span class="event-management-label">
                EVENTLYPH ADMINISTRATION
            </span>

            <h1>Event Management</h1>

            <p>
                Create, view, update, and manage events in the system.
            </p>
        </div>

        <div class="event-management-actions">

            <a href="{{ route('events.create') }}"
               class="action-button primary-button">
                + Add New Event
            </a>

            <a href="{{ route('announcements.index') }}"
               class="action-button secondary-button">
                Manage Announcements
            </a>

        </div>

    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="event-management-alert event-management-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="event-management-alert event-management-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Event Summary -->
    <div class="event-management-summary">

        <div class="event-management-summary-icon">
            E
        </div>

        <div>
            <p>Events Displayed</p>
            <h2>{{ $events->count() }}</h2>
        </div>

    </div>

    <!-- Events Table -->
    <div class="event-management-card">

        <div class="event-management-card-heading">
            <div>
                <h2>All Events</h2>
                <p>Event details and available actions.</p>
            </div>
        </div>

        <div class="event-management-table-wrapper">

            <table class="event-management-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Capacity</th>
                        <th>Available Slots</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($events as $event)

                        <tr>
                            <td class="event-id">
                                #{{ $event->id }}
                            </td>

                            <td class="event-title">
                                {{ $event->title }}
                            </td>

                            <td>
                                {{ $event->event_date }}
                            </td>

                            <td>
                                {{ $event->event_time }}
                            </td>

                            <td>
                                {{ $event->venue }}
                            </td>

                            <td class="event-capacity">
                                {{ number_format($event->capacity) }}
                            </td>

                            <td class="event-capacity">
                                {{ number_format($event->available_slots) }}
                            </td>

                            <td>
                                <div class="event-management-actions-cell">

                                    <a href="{{ route('events.show', $event->id) }}"
                                       class="small-action view-action">
                                        View
                                    </a>

                                    <a href="{{ route('events.edit', $event->id) }}"
                                       class="small-action edit-action">
                                        Edit
                                    </a>

                                    <form action="{{ route('events.destroy', $event->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this event?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="small-action delete-action">
                                            Delete
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="event-management-empty">
                                <strong>No Events Available</strong>
                                Start by creating your first event.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
