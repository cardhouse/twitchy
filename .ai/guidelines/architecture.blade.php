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
- "Activate" button to promote message to overlay
- "Clear" button to remove current toast
- Real-time preview of current overlay state

## Data Flow Architecture

```
[Twitch IRC] → [Artisan Command: twitch:relay] → [Database + Cache] → [Reverb Broadcasting]
                                                                              ↓
                                                        [Control Panel] ← → [Overlay Page]
```

## Real-time Channels

- `chat.messages` (public): Control Panel subscribes to receive new chat messages
- `overlay.{key}` (public): Overlay subscribes to receive toast show/hide events
- Message flow: IRC → Store → Broadcast → UI components

## Key Conventions

**Message Architecture**
- Use the Action pattern for message operations: `CreateMessage`, `PromoteMessage`, `DemoteMessage`
- Store pattern for data persistence: `ChatMessageStore`, `PromotedMessageStore`
- Processor pattern for parsing external message formats: `TwitchMessageProcessor`
- Message types inherit from base `Message` model

**Cache-Based State Management**
- Messages stored in cache with overlay-specific keys
- 30-second TTL for automatic cleanup
- Use `cache()->pull()` for single-consumption events
- Polling interval: 2 seconds for real-time feel

**Events & Broadcasting**
- All events implement `ShouldBroadcast`
- Event naming: `MessageReceived`, `MessagePromoted`, `MessageDemoted`
- Channel naming: `chat.messages`, `overlay.{key}`
- Always include necessary payload data in event properties
