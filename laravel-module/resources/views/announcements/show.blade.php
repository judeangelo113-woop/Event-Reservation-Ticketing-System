
@extends('layouts.app')

@section('title', 'Announcement Details')

@section('content')

<div class="announcement-details-page">

    {{-- Page Header --}}
    <div class="announcement-details-header">
        <div>
            <h1>Announcement Details</h1>
            <p>View the complete information of this announcement.</p>
        </div>

        <a href="{{ route('announcements.index') }}"
           class="announcement-details-back">
            ← Back to Announcements
        </a>
    </div>

    {{-- Announcement Card --}}
    <div class="announcement-details-card">

        <div class="announcement-details-top">
            <div class="announcement-details-icon">📢</div>

            <div>
                <span class="announcement-details-label">ANNOUNCEMENT</span>
                <h2>{{ $announcement->title }}</h2>

                <span class="announcement-details-id">
                    Announcement #{{ $announcement->id }}
                </span>
            </div>
        </div>

        <div class="announcement-details-divider"></div>

        {{-- Event Information --}}
        <div class="announcement-details-section">
            <h3>Event Information</h3>

            <div class="announcement-details-field">
                <span>Event Name</span>
                <strong>
                    {{ $announcement->event->title ?? 'No Event Assigned' }}
                </strong>
            </div>
        </div>

        <div class="announcement-details-divider"></div>

        {{-- Announcement Message --}}
        <div class="announcement-details-section">
            <h3>Announcement Message</h3>

            <div class="announcement-message-content">
                {{ $announcement->message }}
            </div>
        </div>

        <div class="announcement-details-divider"></div>

        {{-- Publication Information --}}
        <div class="announcement-details-section">
            <h3>Publication Information</h3>

            <div class="announcement-details-field">
                <span>Published Date</span>

                <strong>
                    {{ $announcement->published_at
                        ? \Carbon\Carbon::parse($announcement->published_at)->format('F d, Y h:i A')
                        : 'Not Published' }}
                </strong>
            </div>
        </div>

        {{-- Actions --}}
        <div class="announcement-details-actions">

            <a href="{{ route('announcements.edit', $announcement->id) }}"
               class="announcement-details-edit">
                Edit Announcement
            </a>

            <form action="{{ route('announcements.destroy', $announcement->id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to permanently delete this announcement? This action cannot be undone.')">

                @csrf
                @method('DELETE')

                <button type="submit" class="announcement-details-delete">
                    Delete Announcement
                </button>

            </form>

            <a href="{{ route('announcements.index') }}"
               class="announcement-details-cancel">
                Back to List
            </a>

        </div>

    </div>

</div>

@endsection
