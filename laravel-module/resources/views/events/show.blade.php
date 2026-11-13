
@extends('layouts.app')

@section('title', 'Event Details')

@section('content')

<div class="event-details-page">

    {{-- Page Header --}}
    <div class="event-details-header">
        <div>
            <h1>Event Details</h1>
            <p>View the complete information and announcements for this event.</p>
        </div>

        <a href="{{ route('events.index') }}" class="event-details-back">
            ← Back to Events
        </a>
    </div>

    {{-- Event Overview --}}
    <div class="event-details-card">

        <div class="event-details-top">
            <div class="event-details-icon">📅</div>

            <div class="event-details-title">
                <span class="event-details-label">EVENT INFORMATION</span>

                <h2>{{ $event->title }}</h2>

                <span class="event-details-id">
                    Event #{{ $event->id }}
                </span>
            </div>

            <div class="event-details-status">
                <span class="event-status-badge
                    {{ strtolower($event->status) === 'active' ? 'event-status-active' : 'event-status-inactive' }}">
                    {{ $event->status }}
                </span>
            </div>
        </div>

        <div class="event-details-divider"></div>

        {{-- Description --}}
        <div class="event-details-section">
            <h3>Event Description</h3>

            <div class="event-description-content">
                {{ $event->description ?: 'No description provided.' }}
            </div>
        </div>

        <div class="event-details-divider"></div>

        {{-- Schedule and Venue --}}
        <div class="event-details-section">
            <h3>Schedule and Venue</h3>

            <div class="event-details-grid">

                <div class="event-info-item">
                    <span>Event Date</span>
                    <strong>
                        {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                    </strong>
                </div>

                <div class="event-info-item">
                    <span>Event Time</span>
                    <strong>
                        {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                    </strong>
                </div>

                <div class="event-info-item event-info-full">
                    <span>Venue</span>
                    <strong>{{ $event->venue }}</strong>
                </div>

            </div>
        </div>

        <div class="event-details-divider"></div>

        {{-- Capacity Information --}}
        <div class="event-details-section">
            <h3>Reservation Information</h3>

            <div class="event-capacity-grid">

                <div class="event-capacity-item">
                    <span>Total Capacity</span>
                    <strong>{{ number_format($event->capacity) }}</strong>
                </div>

                <div class="event-capacity-item">
                    <span>Available Slots</span>
                    <strong>{{ number_format($event->available_slots) }}</strong>
                </div>

                <div class="event-capacity-item">
                    <span>Total Reservations</span>
                    <strong>{{ $event->reservations->count() }}</strong>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="event-details-actions">

            <a href="{{ route('events.edit', $event->id) }}"
            class="event-details-edit">
                Edit Event
            </a>

            <a href="{{ route('events.index') }}"
            class="event-details-cancel">
                Back to Events
            </a>

        </div>

    </div>

    {{-- Event Announcements --}}
    <div class="event-announcements-section">

        <div class="event-announcements-header">
            <div>
                <h2>Event Announcements</h2>
                <p>Updates and announcements related to this event.</p>
            </div>

            <span class="event-announcements-count">
                {{ $event->announcements->count() }}
                {{ $event->announcements->count() === 1 ? 'Announcement' : 'Announcements' }}
            </span>
        </div>

        @forelse($event->announcements as $announcement)

            <div class="event-announcement-card">

                <div class="event-announcement-heading">
                    <div class="event-announcement-icon">📢</div>

                    <div>
                        <h3>{{ $announcement->title }}</h3>

                        <span>
                            {{ $announcement->published_at
                                ? \Carbon\Carbon::parse($announcement->published_at)->format('F d, Y h:i A')
                                : 'Not Published' }}
                        </span>
                    </div>
                </div>

                <div class="event-announcement-message">
                    {{ $announcement->message }}
                </div>

                <a href="{{ route('announcements.show', $announcement->id) }}"
                class="event-announcement-view">
                    View Announcement →
                </a>

            </div>

        @empty

            <div class="event-announcements-empty">
                <div>📢</div>
                <h3>No Announcements Yet</h3>
                <p>There are currently no announcements for this event.</p>
            </div>

        @endforelse

    </div>

</div>

@endsection
