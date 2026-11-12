<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventAnnouncementController;

Route::get('/', function () {
    return redirect()->route('events.index');
});

Route::middleware('php.admin')->group(function () {

    Route::resource('events', EventController::class);

    Route::resource('announcements', EventAnnouncementController::class);

});
