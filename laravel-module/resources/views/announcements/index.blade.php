@extends('layouts.app')

@section('title', 'Announcement Management')

@section('content')

    <h1>Announcement Management</h1>

    <p>Manage announcements for your events.</p>

    <a href="{{ route('announcements.create') }}" class="button">
        + Create Announcement
    </a>

    <a href="{{ route('events.index') }}" class="button">
        Manage Events
    </a>

    @if(session('success'))
        <div class="message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>Announcement Title</th>
                <th>Message</th>
                <th>Published Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($announcements as $announcement)
                <tr>
                    <td>{{ $announcement->id }}</td>
                    <td>{{ $announcement->event->title }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>{{ $announcement->message }}</td>
                    <td>{{ $announcement->published_at }}</td>

                    <td>
                        <a href="{{ route('announcements.show', $announcement->id) }}">
                            View
                        </a>

                        |

                        <a href="{{ route('announcements.edit', $announcement->id) }}">
                            Edit
                        </a>

                        |

                        <form action="{{ route('announcements.destroy', $announcement->id) }}"
                            method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this announcement?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No announcements available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
