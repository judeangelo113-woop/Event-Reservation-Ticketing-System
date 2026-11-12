@extends('layouts.app')

@section('title', 'Event Management')

@section('content')

    <h1>Event Management</h1>

    <p>Manage all events in the Event Ticketing System.</p>

    <a href="{{ route('events.create') }}" class="button">
        + Add New Event
    </a>

    <a href="{{ route('announcements.index') }}" class="button">
        Manage Announcements
    </a>

    @if(session('success'))
        <div class="message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
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
                    <td>{{ $event->id }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->event_date }}</td>
                    <td>{{ $event->event_time }}</td>
                    <td>{{ $event->venue }}</td>
                    <td>{{ $event->capacity }}</td>
                    <td>{{ $event->available_slots }}</td>

                    <td>
                        <a href="{{ route('events.show', $event->id) }}">
                            View
                        </a>

                        |

                        <a href="{{ route('events.edit', $event->id) }}">
                            Edit
                        </a>

                        |

                        <form action="{{ route('events.destroy', $event->id) }}"
                            method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this event?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No events available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
