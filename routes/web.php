<?php

use App\Http\Controllers\OverlayController;
use Illuminate\Support\Facades\Route;

// Control panel
Route::get('/control', function () {
    return view('control.index');
})->name('control.index');

// Overlay routes with key validation
Route::get('/overlay/{key}', [OverlayController::class, 'show'])->name('overlay.show');
