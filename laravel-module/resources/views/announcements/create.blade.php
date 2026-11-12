@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')

    <h1>Create New Announcement</h1>

    <p>Publish an announcement for a selected event.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('announcements.store') }}" method="POST">
        @csrf

        <label>Select Event</label>
        <select name="event_id" required>
            <option value="">-- Choose an Event --</option>

            @foreach($events as $event)
                <option value="{{ $event->id }}"
                    {{ old('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
            @endforeach
        </select>

        <label>Announcement Title</label>
        <input type="text" name="title"
            value="{{ old('title') }}" required>

        <label>Announcement Message</label>
        <textarea name="message" rows="5" required>{{ old('message') }}</textarea>

        <button type="submit">Publish Announcement</button>

        <a href="{{ route('announcements.index') }}">Cancel</a>
    </form>

@endsection
