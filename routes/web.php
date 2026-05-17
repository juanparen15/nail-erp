<?php

use App\Livewire\BookingForm;
use Illuminate\Support\Facades\Route;

Route::get('/', BookingForm::class)->name('home');
Route::get('/reservar', BookingForm::class)->name('booking');
