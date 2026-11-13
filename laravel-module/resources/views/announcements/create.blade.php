
@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')

<div class="announcement-create-page">

    {{-- Page Header --}}
    <div class="announcement-create-header">
        <div>
            <h1>Create New Announcement</h1>
            <p>Publish important updates and announcements for an event.</p>
        </div>

        <a href="{{ route('announcements.index') }}"
           class="announcement-create-back">
            ← Back to Announcements
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="announcement-create-errors">
            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="announcement-create-card">

        <div class="announcement-create-card-header">
            <div class="announcement-create-icon">📢</div>

            <div>
                <h2>Announcement Information</h2>
                <p>Complete the details below before publishing.</p>
            </div>
        </div>

        <form action="{{ route('announcements.store') }}"
              method="POST"
              class="announcement-create-form">

            @csrf

            {{-- Select Event --}}
            <div class="announcement-create-group">
                <label for="event_id">Select Event</label>

                <select name="event_id" id="event_id" required>
                    <option value="">-- Choose an Event --</option>

                    @foreach($events as $event)
                        <option value="{{ $event->id }}"
                            {{ old('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                    @endforeach
                </select>

                @error('event_id')
                    <small class="announcement-create-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Announcement Title --}}
            <div class="announcement-create-group">
                <label for="title">Announcement Title</label>

                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title') }}"
                       placeholder="Enter announcement title"
                       maxlength="255"
                       required>

                @error('title')
                    <small class="announcement-create-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Announcement Message --}}
            <div class="announcement-create-group">
                <label for="message">Announcement Message</label>

                <textarea name="message"
                          id="message"
                          rows="6"
                          placeholder="Write your announcement here..."
                          required>{{ old('message') }}</textarea>

                <small class="announcement-create-help">
                    Provide clear and important information for event attendees.
                </small>

                @error('message')
                    <small class="announcement-create-field-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="announcement-create-actions">

                <a href="{{ route('announcements.index') }}"
                   class="announcement-create-cancel">
                    Cancel
                </a>

                <button type="submit"
                        class="announcement-create-submit">
                    📢 Publish Announcement
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
