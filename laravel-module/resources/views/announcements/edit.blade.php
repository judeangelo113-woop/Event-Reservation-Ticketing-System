
@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')

<div class="announcement-edit-page">

    {{-- Page Header --}}
    <div class="announcement-edit-header">
        <div>
            <h1>Edit Announcement</h1>
            <p>Update the announcement information for your selected event.</p>
        </div>

        <a href="{{ route('announcements.index') }}"
           class="announcement-edit-back">
            ← Back to Announcements
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="announcement-edit-errors">
            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form Card --}}
    <div class="announcement-edit-card">

        <div class="announcement-edit-card-header">
            <div class="announcement-edit-icon">📢</div>

            <div>
                <h2>Announcement Information</h2>
                <p>Modify the fields below and save your changes.</p>
            </div>
        </div>

        <form action="{{ route('announcements.update', $announcement->id) }}"
              method="POST"
              class="announcement-edit-form">

            @csrf
            @method('PUT')

            {{-- Event --}}
            <div class="announcement-edit-group">
                <label for="event_id">Select Event</label>

                <select name="event_id" id="event_id" required>
                    <option value="">-- Select an Event --</option>

                    @foreach($events as $event)
                        <option value="{{ $event->id }}"
                            {{ old('event_id', $announcement->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                    @endforeach
                </select>

                @error('event_id')
                    <small class="announcement-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Announcement Title --}}
            <div class="announcement-edit-group">
                <label for="title">Announcement Title</label>

                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title', $announcement->title) }}"
                       placeholder="Enter announcement title"
                       maxlength="255"
                       required>

                @error('title')
                    <small class="announcement-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Announcement Message --}}
            <div class="announcement-edit-group">
                <label for="message">Announcement Message</label>

                <textarea name="message"
                          id="message"
                          rows="7"
                          placeholder="Write your announcement message here..."
                          required>{{ old('message', $announcement->message) }}</textarea>

                @error('message')
                    <small class="announcement-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="announcement-edit-actions">

                <a href="{{ route('announcements.index') }}"
                   class="announcement-edit-cancel">
                    Cancel
                </a>

                <button type="submit" class="announcement-edit-submit">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
