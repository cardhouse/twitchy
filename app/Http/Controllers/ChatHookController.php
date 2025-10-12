<?php

namespace App\Http\Controllers;

use App\Services\OverlayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatHookController extends Controller
{
    public function __construct(
        private readonly OverlayService $overlayService
    ) {}

    /**
     * Handle incoming chat messages from external services (e.g., Twitch, Discord, IRC).
     */
    public function handleChatMessage(NewChatRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $overlayKey = $request->get('overlay_key', config('overlay.key', 'local'));

        // Prepare toast options based on platform
        $options = $this->getOptionsForPlatform($validated['platform'] ?? 'twitch');

        // Queue the toast
        $success = $this->overlayService
            ->forOverlay($overlayKey)
            ->showChatMessage(
                displayName: $validated['display_name'],
                message: $validated['message'],
                username: $validated['username'] ?? strtolower($validated['display_name']),
                badges: $validated['badges'] ?? [],
                options: $options
            );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Chat message queued for overlay',
                'overlay_key' => $overlayKey,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to queue chat message',
        ], 500);
    }

    /**
     * Handle system notifications (follows, subscriptions, etc.).
     */
    public function handleNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:follow,subscribe,donation,raid,host',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:300',
            'overlay_key' => 'nullable|string',
            'duration_ms' => 'nullable|integer|min:3000|max:15000',
        ]);

        $overlayKey = $validated['overlay_key'] ?? config('overlay.key', 'local');

        $options = [
            'duration_ms' => $validated['duration_ms'] ?? 10000,
            'theme' => 'dark',
            'fontScale' => 1.2, // Larger for notifications
        ];

        $success = $this->overlayService
            ->forOverlay($overlayKey)
            ->showNotification(
                title: $validated['title'],
                message: $validated['message'],
                options: $options
            );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notification queued for overlay',
                'overlay_key' => $overlayKey,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to queue notification',
        ], 500);
    }

    /**
     * Get default options based on the platform.
     */
    private function getOptionsForPlatform(string $platform): array
    {
        return match ($platform) {
            'twitch' => [
                'duration_ms' => 8000,
                'theme' => 'dark',
                'fontScale' => 1.0,
            ],
            'discord' => [
                'duration_ms' => 6000,
                'theme' => 'dark',
                'fontScale' => 0.9,
            ],
            'youtube' => [
                'duration_ms' => 7000,
                'theme' => 'light',
                'fontScale' => 1.0,
            ],
            'irc' => [
                'duration_ms' => 5000,
                'theme' => 'dark',
                'fontScale' => 0.9,
            ],
            default => [
                'duration_ms' => 8000,
                'theme' => 'dark',
                'fontScale' => 1.0,
            ],
        };
    }
}
