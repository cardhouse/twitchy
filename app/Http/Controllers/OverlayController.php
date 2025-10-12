<?php

namespace App\Http\Controllers;

use App\Services\OverlayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OverlayController extends Controller
{
    /**
     * Show the overlay page for the given key.
     */
    public function show(Request $request, string $key): Response
    {
        // Validate overlay key against configuration
        if ($key !== config('overlay.key')) {
            abort(404);
        }

        return response()->view('overlay.show', [
            'overlayKey' => $key
        ]);
    }

    /**
     * Trigger a toast show event for the overlay.
     */
    public function showToast(Request $request, string $key): \Illuminate\Http\JsonResponse
    {
        // Validate overlay key
        if ($key !== config('overlay.key')) {
            abort(404);
        }

        $validated = $request->validate([
            'message' => 'required|array',
            'message.display_name' => 'required|string',
            'message.username' => 'nullable|string',
            'message.badges' => 'nullable|array',
            'message.message' => 'required|string',
            'options' => 'nullable|array',
            'options.duration_ms' => 'nullable|integer|min:1000|max:30000',
            'options.theme' => 'nullable|string|in:dark,light',
            'options.fontScale' => 'nullable|numeric|min:0.5|max:3.0',
            'options.animation' => 'nullable|string|in:slide-up,slide-down,slide-left,slide-right,fade,zoom',
            'options.safeMargin' => 'nullable|integer|min:0|max:100',
        ]);

        // Use the overlay service
        $overlayService = new OverlayService($key);
        $success = $overlayService->showToast($validated['message'], $validated['options'] ?? []);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Toast event queued for overlay',
                'data' => [
                    'message' => $validated['message'],
                    'options' => $validated['options'] ?? [],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to queue toast event',
        ], 500);
    }

    /**
     * Get pending toast events for an overlay.
     */
    public function getPendingToasts(Request $request, string $key): \Illuminate\Http\JsonResponse
    {
        // Validate overlay key
        if ($key !== config('overlay.key')) {
            abort(404);
        }

        $overlayService = new OverlayService($key);
        $eventData = $overlayService->getPendingEvents();

        return response()->json([
            'success' => true,
            'event' => $eventData,
        ]);
    }
}
