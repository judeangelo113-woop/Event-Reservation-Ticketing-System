@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')

    <h1>Edit Event</h1>

    <p>Update the event information below.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Event Title</label>
        <input type="text" name="title"
            value="{{ old('title', $event->title) }}" required>

        <label>Description</label>
        <textarea name="description" rows="4">{{ old('description', $event->description) }}</textarea>

        <label>Event Date</label>
        <input type="date" name="event_date"
            value="{{ old('event_date', $event->event_date) }}" required>

        <label>Event Time</label>
        <input type="time" name="event_time"
            value="{{ old('event_time', $event->event_time) }}" required>

        <label>Venue</label>
        <input type="text" name="venue"
            value="{{ old('venue', $event->venue) }}" required>

        <label>Capacity</label>
        <input type="number" name="capacity" min="1"
            value="{{ old('capacity', $event->capacity) }}" required>

        <button type="submit">Update Event</button>

        <a href="{{ route('events.index') }}">Cancel</a>
    </form>

@endsection
