
@extends('layouts.app')

@section('title', 'Create Event')

@section('content')

<div class="event-create-page">

    {{-- Page Header --}}
    <div class="event-create-header">
        <div>
            <h1>Create New Event</h1>
            <p>Add a new event to the EventlyPH ticketing system.</p>
        </div>

        <a href="{{ route('events.index') }}" class="event-create-back">
            ← Back to Events
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="event-create-errors">
            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="event-create-card">

        <div class="event-create-card-header">
            <div class="event-create-icon">📅</div>

            <div>
                <h2>Event Information</h2>
                <p>Enter the necessary details for your new event.</p>
            </div>
        </div>

        <form action="{{ route('events.store') }}"
              method="POST"
              class="event-create-form">

            @csrf

            {{-- Event Title --}}
            <div class="event-create-group">
                <label for="title">Event Title</label>

                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title') }}"
                       placeholder="Enter event title"
                       maxlength="255"
                       required>

                @error('title')
                    <small class="event-create-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Description --}}
            <div class="event-create-group">
                <label for="description">Event Description</label>

                <textarea name="description"
                          id="description"
                          rows="5"
                          placeholder="Enter event description...">{{ old('description') }}</textarea>

                @error('description')
                    <small class="event-create-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Date and Time --}}
            <div class="event-create-grid">

                <div class="event-create-group">
                    <label for="event_date">Event Date</label>

                    <input type="date"
                           name="event_date"
                           id="event_date"
                           value="{{ old('event_date') }}"
                           required>

                    @error('event_date')
                        <small class="event-create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="event-create-group">
                    <label for="event_time">Event Time</label>

                    <input type="time"
                           name="event_time"
                           id="event_time"
                           value="{{ old('event_time') }}"
                           required>

                    @error('event_time')
                        <small class="event-create-field-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            {{-- Venue --}}
            <div class="event-create-group">
                <label for="venue">Venue</label>

                <input type="text"
                       name="venue"
                       id="venue"
                       value="{{ old('venue') }}"
                       placeholder="Enter event venue"
                       maxlength="255"
                       required>

                @error('venue')
                    <small class="event-create-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Capacity --}}
            <div class="event-create-group">
                <label for="capacity">Event Capacity</label>

                <input type="number"
                       name="capacity"
                       id="capacity"
                       min="1"
                       value="{{ old('capacity') }}"
                       placeholder="Enter maximum attendees"
                       required>

                <small class="event-create-help">
                    Specify the maximum number of attendees allowed for this event.
                </small>

                @error('capacity')
                    <small class="event-create-field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="event-create-actions">

                <a href="{{ route('events.index') }}"
                   class="event-create-cancel">
                    Cancel
                </a>

                <button type="submit" class="event-create-submit">
                    + Create Event
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
