# Directory Structure & Conventions

## Application Structure

```
app/
├── Actions/                    # Single-responsibility action classes
│   └── ClearChannel.php
├── Console/Commands/           # Artisan commands (auto-registered)
│   ├── TwitchRelayCommand.php  # IRC connection to Twitch
│   └── TwitchyRunCommand.php   # Main orchestration command
├── Events/                     # Broadcastable events
│   ├── MessageReceived.php
│   ├── MessagePromoted.php
│   └── MessageDemoted.php
├── Http/Controllers/
│   ├── ChatHookController.php  # Webhook endpoints for external integration
│   ├── OverlayController.php   # Overlay display and API
│   └── NewChatRequest.php      # Form request for validation
├── Livewire/                   # Livewire components
│   └── Overlay/
│       └── ToastDisplay.php    # Main overlay toast component
├── Messages/                   # Message handling domain
│   ├── Actions/                # Message-specific actions
│   │   ├── CreateMessage.php
│   │   ├── PromoteMessage.php
│   │   ├── DemoteMessage.php
│   │   ├── ClearMessages.php
│   │   └── ClearPromotedMessages.php
│   ├── Contracts/              # Interfaces
│   │   └── Store.php
│   ├── Processors/             # Message processing logic
│   │   └── TwitchMessageProcessor.php
│   └── Stores/                 # Data persistence strategies
│       ├── CacheStore.php
│       ├── ChatMessageStore.php
│       └── PromotedMessageStore.php
├── Models/                     # Eloquent models
│   ├── Chatroom.php
│   ├── Message.php             # Base message model
│   └── Messages/               # Message type hierarchy
│       ├── ChatMessage.php
│       ├── PingMessage.php
│       ├── PrivateMessage.php
│       └── UnknownMessage.php
└── Services/
    └── OverlayService.php      # Core overlay management service

resources/views/
├── control/                    # Control panel views
│   └── index.blade.php
├── layouts/                    # Layout files
│   ├── app.blade.php
│   └── overlay.blade.php       # Transparent layout for OBS
├── livewire/                   # Livewire component views
│   ├── control/
│   │   ├── chat-feed.blade.php
│   │   └── toast-preview.blade.php
│   └── overlay/
│       └── toast-display.blade.php
└── overlay/                    # Overlay-specific views
    └── show.blade.php
```
