# Key Patterns & Best Practices

## Action Pattern

Use action classes for discrete message operations:

```php
use App\Messages\Actions\PromoteMessage;
use App\Models\Message;

// Promote a message to overlay
$action = app(PromoteMessage::class);
$action->handle($message);

// Demote a message from overlay
$action = app(\App\Messages\Actions\DemoteMessage::class);
$action->handle($messageId);

// Clear all messages
app(\App\Messages\Actions\ClearMessages::class)->handle();
```

## Message Store Pattern

Always use stores for data persistence:

```php
use App\Messages\Stores\ChatMessageStore;

$store = new ChatMessageStore('local');

// Store message
$store->push($messageData);

// List recent messages
$messages = $store->list(limit: 100);

// Clear all messages
$store->clear();
```

## Event-Driven Architecture

Events and listeners handle side effects:

```php
// Dispatching events (done automatically by Actions)
MessagePromoted::dispatch($message);
MessageDemoted::dispatch($messageId);
MessageReceived::dispatch($message);

// Listeners handle the side effects
// MessagePromotedListener - handles post-promotion logic
// MessageDemotedListener - handles post-demotion logic
```

## Livewire Component Conventions

- **Prefer Volt**: Use Volt single-file components for new components when possible
- **Real-time Updates**: Subscribe to Reverb channels using Echo in component lifecycle
- **State Management**: Keep state server-side, UI reflects it
- **Loading States**: Use `wire:loading` for better UX

```blade
@verbatim
{{-- Example Livewire component pattern with Echo --}}
<?php
use Livewire\Attributes\On;

new class extends Component {
    public Collection $messages;

    #[On('echo:local,MessageReceived')]
    public function addMessage($event): void
    {
        $this->messages->push($event['message']);
    }
}
?>

<div>
    @foreach ($messages as $message)
        <div wire:key="msg-{{ $message['id'] }}">
            {{ $message['message'] }}
        </div>
    @endforeach
</div>
@endverbatim
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
