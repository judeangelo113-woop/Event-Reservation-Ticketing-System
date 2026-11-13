
@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')

<div class="event-edit-page">

    {{-- Page Header --}}
    <div class="event-edit-header">
        <div>
            <h1>Edit Event</h1>
            <p>Update the event details and information below.</p>
        </div>

        <a href="{{ route('events.index') }}" class="event-edit-back">
            ← Back to Events
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="event-edit-errors">
            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form Card --}}
    <div class="event-edit-card">

        <div class="event-edit-card-header">
            <div class="event-edit-icon">📅</div>

            <div>
                <h2>Event Information</h2>
                <p>Modify the event details and save your changes.</p>
            </div>
        </div>

        <form action="{{ route('events.update', $event->id) }}"
              method="POST"
              class="event-edit-form">

            @csrf
            @method('PUT')

            {{-- Event Title --}}
            <div class="event-edit-group">
                <label for="title">Event Title</label>

                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title', $event->title) }}"
                       placeholder="Enter event title"
                       maxlength="255"
                       required>

                @error('title')
                    <small class="event-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Description --}}
            <div class="event-edit-group">
                <label for="description">Event Description</label>

                <textarea name="description"
                          id="description"
                          rows="5"
                          placeholder="Enter event description...">{{ old('description', $event->description) }}</textarea>

                @error('description')
                    <small class="event-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Date and Time --}}
            <div class="event-edit-grid">

                <div class="event-edit-group">
                    <label for="event_date">Event Date</label>

                    <input type="date"
                           name="event_date"
                           id="event_date"
                           value="{{ old('event_date', $event->event_date) }}"
                           required>

                    @error('event_date')
                        <small class="event-edit-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="event-edit-group">
                    <label for="event_time">Event Time</label>

                    <input type="time"
                           name="event_time"
                           id="event_time"
                           value="{{ old('event_time', $event->event_time) }}"
                           required>

                    @error('event_time')
                        <small class="event-edit-field-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            {{-- Venue --}}
            <div class="event-edit-group">
                <label for="venue">Venue</label>

                <input type="text"
                       name="venue"
                       id="venue"
                       value="{{ old('venue', $event->venue) }}"
                       placeholder="Enter event venue"
                       maxlength="255"
                       required>

                @error('venue')
                    <small class="event-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Capacity --}}
            <div class="event-edit-group">
                <label for="capacity">Event Capacity</label>

                <input type="number"
                       name="capacity"
                       id="capacity"
                       min="1"
                       value="{{ old('capacity', $event->capacity) }}"
                       required>

                <small class="event-edit-help">
                    Enter the maximum number of attendees allowed for this event.
                </small>

                @error('capacity')
                    <small class="event-edit-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="event-edit-actions">

                <a href="{{ route('events.index') }}"
                   class="event-edit-cancel">
                    Cancel
                </a>

                <button type="submit" class="event-edit-submit">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
