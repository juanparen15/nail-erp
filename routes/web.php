<?php

use App\Http\Controllers\CalendarController;
use App\Livewire\BookingForm;
use Illuminate\Support\Facades\Route;

Route::get('/', BookingForm::class)->name('home');
Route::get('/reservar', BookingForm::class)->name('booking');

// Calendar appointments endpoint (admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/calendar/appointments', [CalendarController::class, 'appointments'])
        ->name('admin.calendar.appointments');
});
