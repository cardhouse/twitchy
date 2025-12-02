# Application Architecture

## Two-Window System

**1. Overlay Page (OBS Browser Source)**
- URL: `/overlay/{key}` (e.g., `/overlay/local`)
- Transparent background for OBS integration
- Displays single toast of selected chat message
- Supports URL parameters for customization: `?theme=dark&fontScale=1.2&safeMargin=50&animation=slide-up`
- Auto-dismiss (timed) or persistent (manual clear) modes

**2. Control Panel (Management Interface)**
- URL: `/control`
- Live chat feed streaming from configured Twitch channel
- Search/filter capabilities
- Click on message to promote it to overlay
- "Clear Messages" button to clear all messages
- Stream toggle to pause/resume the IRC relay

## Data Flow Architecture

```
[Twitch IRC] → [Artisan Command: twitch:relay] → [Database + Cache] → [Reverb Broadcasting]
                                                                              ↓
                                                        [Control Panel] ← → [Overlay Page]
```

## Real-time Channels

- `local` (public): Control Panel subscribes via Echo to receive MessageReceived events
- `overlay.{key}` (public): Overlay subscribes to receive toast show/hide events (MessagePromoted, MessageDemoted)
- Message flow: IRC → Store → Broadcast → UI components

## Key Conventions

**Message Architecture**
- Use the Action pattern for message operations: `CreateMessage`, `PromoteMessage`, `DemoteMessage`
- Store pattern for data persistence: `ChatMessageStore`, `PromotedMessageStore`
- Processor pattern for parsing external message formats: `TwitchMessageProcessor`
- Message types inherit from base `Message` model

**Event-Driven Architecture**
- Events: `MessageReceived`, `MessagePromoted`, `MessageDemoted`
- Listeners handle side effects: `MessagePromotedListener`, `MessageDemotedListener`
- Events implement `ShouldBroadcast` for real-time updates

**Cache-Based State Management**
- Messages stored in cache with overlay-specific keys
- Automatic cleanup via TTL
- Polling interval: 2 seconds for real-time feel via `wire:poll`

**Events & Broadcasting**
- All events implement `ShouldBroadcast`
- Event naming: `MessageReceived`, `MessagePromoted`, `MessageDemoted`
- Channel naming: `local`, `overlay.{key}`
- Always include necessary payload data in event properties
