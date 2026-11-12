<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'venue',
        'capacity',
        'available_slots',
        'status',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'event_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(EventAnnouncement::class, 'event_id');
    }
}
