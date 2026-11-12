@extends('layouts.app')

@section('title', 'Event Details')

@section('content')

    <h1>Event Details</h1>

    <div style="line-height: 2;">

        <p><strong>Event ID:</strong> {{ $event->id }}</p>

        <p><strong>Title:</strong> {{ $event->title }}</p>

        <p><strong>Description:</strong> {{ $event->description }}</p>

        <p><strong>Date:</strong> {{ $event->event_date }}</p>

        <p><strong>Time:</strong> {{ $event->event_time }}</p>

        <p><strong>Venue:</strong> {{ $event->venue }}</p>

        <p><strong>Capacity:</strong> {{ $event->capacity }}</p>

        <p><strong>Available Slots:</strong> {{ $event->available_slots }}</p>

        <p><strong>Status:</strong> {{ $event->status }}</p>

        <p>
            <strong>Total Reservations:</strong>
            {{ $event->reservations->count() }}
        </p>

    </div>

    <hr>

    <h2>Event Announcements</h2>

    @if($event->announcements->count() > 0)

        @foreach($event->announcements as $announcement)

            <div style="padding: 15px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 5px;">

                <h3>{{ $announcement->title }}</h3>

                <p>{{ $announcement->message }}</p>

                <small>
                    Published:
                    {{ $announcement->published_at?->format('F d, Y h:i A') }}
                </small>

            </div>

        @endforeach

    @else

        <p>No announcements available for this event.</p>

    @endif

    <a href="{{ route('events.index') }}" class="button">
        Back to Events
    </a>

@endsection
