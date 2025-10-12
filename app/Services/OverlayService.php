<?php

namespace App\Services;

class OverlayService
{
    public function __construct(
        private string $overlayKey = 'local'
    ) {}

    /**
     * Queue a toast to be displayed on the overlay.
     */
    public function showToast(array $message, array $options = []): bool
    {
        // Validate message structure
        if (! isset($message['display_name']) || ! isset($message['message'])) {
            throw new \InvalidArgumentException('Message must have display_name and message fields');
        }

        // Set default options
        $options = array_merge([
            'duration_ms' => 8000,
            'theme' => 'dark',
            'fontScale' => 1.0,
            'animation' => 'slide-up',
            'safeMargin' => 24,
        ], $options);

        // Prepare event data
        $eventData = [
            'message' => $message,
            'options' => $options,
            'timestamp' => now()->toISOString(),
        ];

        // Store in cache for the overlay to pick up
        cache()->put("overlay_toast_{$this->overlayKey}", $eventData, now()->addSeconds(30));

        return true;
    }

    /**
     * Create a toast from chat message data.
     */
    public function showChatMessage(
        string $displayName,
        string $message,
        ?string $username = null,
        array $badges = [],
        array $options = []
    ): bool {
        $messageData = [
            'display_name' => $displayName,
            'username' => $username ?? strtolower($displayName),
            'badges' => $badges,
            'message' => $message,
        ];

        return $this->showToast($messageData, $options);
    }

    /**
     * Create a simple notification toast.
     */
    public function showNotification(string $title, string $message, array $options = []): bool
    {
        $messageData = [
            'display_name' => $title,
            'username' => 'system',
            'badges' => [['name' => 'notification']],
            'message' => $message,
        ];

        return $this->showToast($messageData, $options);
    }

    /**
     * Set the overlay key for this service instance.
     */
    public function forOverlay(string $overlayKey): self
    {
        return new self($overlayKey);
    }

    /**
     * Check if there are pending events for this overlay.
     */
    public function hasPendingEvents(): bool
    {
        return cache()->has("overlay_toast_{$this->overlayKey}");
    }

    /**
     * Get and remove pending events for this overlay.
     */
    public function getPendingEvents(): ?array
    {
        return cache()->pull("overlay_toast_{$this->overlayKey}");
    }
}
