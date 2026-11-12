@extends('layouts.app')

@section('title', 'Announcement Details')

@section('content')

    <h1>Announcement Details</h1>

    <div style="line-height: 2;">

        <p>
            <strong>Announcement ID:</strong>
            {{ $announcement->id }}
        </p>

        <p>
            <strong>Event:</strong>
            {{ $announcement->event->title }}
        </p>

        <p>
            <strong>Title:</strong>
            {{ $announcement->title }}
        </p>

        <p>
            <strong>Message:</strong>
            {{ $announcement->message }}
        </p>

        <p>
            <strong>Published Date:</strong>
            {{ $announcement->published_at?->format('F d, Y h:i A') }}
        </p>

    </div>

    <a href="{{ route('announcements.index') }}" class="button">
        Back to Announcements
    </a>

@endsection
