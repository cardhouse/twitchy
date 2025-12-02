# Directory Structure & Conventions

## Application Structure

```
app/
├── Console/Commands/           # Artisan commands (auto-registered)
│   ├── TwitchRelayCommand.php  # IRC connection to Twitch
│   └── TwitchyRunCommand.php   # Main orchestration command
├── Events/                     # Broadcastable events
│   ├── MessageReceived.php
│   ├── MessagePromoted.php
│   └── MessageDemoted.php
├── Http/Controllers/
│   └── OverlayController.php   # Overlay display
├── Listeners/                  # Event listeners
│   ├── MessageDemotedListener.php
│   └── MessagePromotedListener.php
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
└── Providers/
    ├── AppServiceProvider.php
    ├── EventServiceProvider.php
    └── VoltServiceProvider.php

resources/views/
├── control/                    # Control panel views
│   └── index.blade.php
├── layouts/                    # Layout files
│   ├── control.blade.php       # Control panel layout
│   └── overlay.blade.php       # Transparent layout for OBS
├── livewire/                   # Livewire component views
│   ├── control/
│   │   ├── chat-feed.blade.php
│   │   ├── stream-toggle.blade.php
│   │   └── toast-preview.blade.php
│   └── overlay/
│       └── toast-display.blade.php
└── overlay/                    # Overlay-specific views
    └── show.blade.php
```
