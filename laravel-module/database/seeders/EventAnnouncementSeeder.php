<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventAnnouncement;

class EventAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        EventAnnouncement::updateOrCreate(
            [
                'event_id' => 8,
                'title' => 'IT Seminar Announcement',
            ],
            [
                'message' => 'The IT Seminar will be held at the Computer Laboratory. Please arrive early.',
                'published_at' => now(),
            ]
        );
    }
}
