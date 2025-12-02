# Key Patterns & Best Practices

## Service Layer: OverlayService

The `OverlayService` is the primary interface for overlay operations:

```php
use App\Services\OverlayService;

// Basic usage
$overlay = new OverlayService('local');

// Show chat message
$overlay->showChatMessage(
    displayName: 'Username',
    message: 'Hello World!',
    username: 'username',
    badges: [['name' => 'moderator']],
    options: ['theme' => 'dark', 'duration_ms' => 8000]
);

// Show notification
$overlay->showNotification(
    title: 'Alert!',
    message: 'Something happened!',
    options: ['duration_ms' => 12000]
);

// Switch overlay
$overlay->forOverlay('stream2')->showNotification('Title', 'Message');
```

## Message Store Pattern

Always use stores for data persistence:

```php
use App\Messages\Stores\ChatMessageStore;

$store = new ChatMessageStore('local');

// Store message
$store->store($messageData);

// List recent messages
$messages = $store->list(limit: 100);

// Clear all messages
$store->clear();
```

## Action Pattern

Use action classes for discrete operations:

```php
use App\Messages\Actions\PromoteMessage;

// Promote a message to overlay
$action = new PromoteMessage();
$action->execute($messageId, $options);
```

## Livewire Component Conventions

- **Prefer Volt**: Use Volt single-file components for new components when possible
- **Real-time Updates**: Subscribe to Reverb channels in component lifecycle
- **State Management**: Keep state server-side, UI reflects it
- **Loading States**: Use `wire:loading` for better UX

```blade
{{-- Example Livewire component pattern --}}
<div wire:poll.2s="checkForUpdates">
    @foreach ($messages as $message)
        <div wire:key="msg-{{ $message->id }}">
            {{-- Message content --}}
        </div>
    @endforeach
</div>
```

## Important Notes for AI Assistants

1. **Always use Actions, Stores, and Processors** - Don't bypass these patterns
2. **Test real-time features** - Reverb broadcasting is core functionality
3. **Maintain cache-based architecture** - Don't switch to database polling without discussion
4. **Respect overlay transparency** - Critical for OBS integration
5. **Follow Volt conventions** - Prefer single-file components for new Livewire components
6. **Use Flux Pro components** - Full Pro edition is available
7. **IRC resilience is critical** - Handle disconnections and reconnections gracefully
8. **Security via obscurity is insufficient** - Overlay keys are basic protection, not security
9. **Performance matters** - This runs in real-time during live streams
10. **Documentation in `/docs`** - Comprehensive guides exist, reference them
