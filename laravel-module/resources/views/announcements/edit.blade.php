@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')

    <h1>Edit Announcement</h1>

    <p>Update the announcement information below.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('announcements.update', $announcement->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Select Event</label>
        <select name="event_id" required>
            @foreach($events as $event)
                <option value="{{ $event->id }}"
                    {{ old('event_id', $announcement->event_id) == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
            @endforeach
        </select>

        <label>Announcement Title</label>
        <input type="text" name="title"
            value="{{ old('title', $announcement->title) }}" required>

        <label>Announcement Message</label>
        <textarea name="message" rows="5" required>{{ old('message', $announcement->message) }}</textarea>

        <button type="submit">Update Announcement</button>

        <a href="{{ route('announcements.index') }}">Cancel</a>
    </form>

@endsection
