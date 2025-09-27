<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/pitches/{pitchId}/slots', [BookingController::class, 'availableSlots']);
Route::post('/bookings', [BookingController::class, 'book']);
