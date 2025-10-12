<?php

use App\Http\Controllers\ChatHookController;
use App\Http\Controllers\OverlayController;
use App\Services\OverlayService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Control panel
Route::get('/control', function () {
    return view('control.index');
})->name('control.index');

// Overlay routes with key validation
Route::get('/overlay/{key}', [OverlayController::class, 'show'])->name('overlay.show');

// // Overlay API routes for triggering events
// Route::post('/overlay/{key}/toast', [OverlayController::class, 'showToast'])->name('overlay.toast')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
// Route::get('/overlay/{key}/pending-toasts', [OverlayController::class, 'getPendingToasts'])->name('overlay.pending-toasts');

// // Chat integration hooks (no CSRF protection for external webhooks)
// Route::post('/hooks/chat-message', [ChatHookController::class, 'handleChatMessage'])->name('hooks.chat-message')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
// Route::post('/hooks/notification', [ChatHookController::class, 'handleNotification'])->name('hooks.notification')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// // Control panel API endpoints
// Route::get('/api/control/recent-messages', function () {
//     $store = new \App\Messages\Stores\ChatMessageStore('local');

//     return response()->json([
//         'success' => true,
//         'messages' => $store->list(100),
//     ]);
// })->name('api.control.recent-messages');

