@extends('layouts.app')

@section('title', 'Create Event')

@section('content')

    <h1>Create New Event</h1>

    <p>Fill in the details below to add a new event.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.store') }}" method="POST">
        @csrf

        <label>Event Title</label>
        <input type="text" name="title" value="{{ old('title') }}" required>

        <label>Description</label>
        <textarea name="description" rows="4">{{ old('description') }}</textarea>

        <label>Event Date</label>
        <input type="date" name="event_date" value="{{ old('event_date') }}" required>

        <label>Event Time</label>
        <input type="time" name="event_time" value="{{ old('event_time') }}" required>

        <label>Venue</label>
        <input type="text" name="venue" value="{{ old('venue') }}" required>

        <label>Capacity</label>
        <input type="number" name="capacity" min="1" value="{{ old('capacity') }}" required>

        <button type="submit">Create Event</button>

        <a href="{{ route('events.index') }}">Cancel</a>
    </form>

@endsection
