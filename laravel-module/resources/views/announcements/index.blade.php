
@extends('layouts.app')

@section('title', 'Announcement Management')

@section('content')

<div class="announcement-management">

    {{-- Page Header --}}
    <div class="announcement-header">
        <div>
            <h1>Announcement Management</h1>
            <p>Manage and publish announcements for your events.</p>
        </div>

        <div class="announcement-header-actions">
            <a href="{{ route('announcements.create') }}" class="announcement-btn announcement-btn-primary">
                + Create Announcement
            </a>

            <a href="{{ route('events.index') }}" class="announcement-btn announcement-btn-secondary">
                Manage Events
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="announcement-alert announcement-alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="announcement-alert announcement-alert-error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Announcement Summary --}}
    <div class="announcement-summary">
        <div>
            <span class="announcement-summary-label">Total Announcements</span>
            <h2>{{ $announcements->count() }}</h2>
        </div>

        <div class="announcement-summary-icon">
            <span>📢</span>
        </div>
    </div>

    {{-- Announcements Table --}}
    <div class="announcement-table-card">

        <div class="announcement-table-heading">
            <div>
                <h2>All Announcements</h2>
                <p>View and manage your published announcements.</p>
            </div>
        </div>

        <div class="announcement-table-wrapper">
            <table class="announcement-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Announcement</th>
                        <th>Message</th>
                        <th>Published Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($announcements as $announcement)
                        <tr>
                            <td>
                                <span class="announcement-id">
                                    #{{ $announcement->id }}
                                </span>
                            </td>

                            <td>
                                <span class="announcement-event">
                                    {{ $announcement->event->title ?? 'No Event Assigned' }}
                                </span>
                            </td>

                            <td>
                                <strong class="announcement-title">
                                    {{ $announcement->title }}
                                </strong>
                            </td>

                            <td>
                                <span class="announcement-message">
                                    {{ $announcement->message }}
                                </span>
                            </td>

                            <td>
                                <span class="announcement-date">
                                    {{ $announcement->published_at
                                        ? \Carbon\Carbon::parse($announcement->published_at)->format('M d, Y h:i A')
                                        : 'Not Published' }}
                                </span>
                            </td>

                            <td>
                                <div class="announcement-actions">

                                    <a href="{{ route('announcements.show', $announcement->id) }}"
                                       class="announcement-action-btn announcement-view-btn">
                                        View
                                    </a>

                                    <a href="{{ route('announcements.edit', $announcement->id) }}"
                                       class="announcement-action-btn announcement-edit-btn">
                                        Edit
                                    </a>

                                    <form action="{{ route('announcements.destroy', $announcement->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this announcement?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="announcement-action-btn announcement-delete-btn">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="announcement-empty">
                                    <div class="announcement-empty-icon">📢</div>
                                    <h3>No Announcements Yet</h3>
                                    <p>There are currently no announcements available.</p>

                                    <a href="{{ route('announcements.create') }}"
                                    class="announcement-btn announcement-btn-primary">
                                        Create Your First Announcement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
