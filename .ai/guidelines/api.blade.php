# API Endpoints & Integration

## Webhook Endpoints (External Integration)

**Chat Message Webhook**
```
POST /hooks/chat-message
Content-Type: application/json

{
  "display_name": "StreamerFan123",
  "username": "streamerfan123",
  "message": "Hello! 👋",
  "badges": [{"name": "subscriber"}, {"name": "vip"}],
  "platform": "twitch",
  "overlay_key": "local"
}
```

**Required Fields:**
- `display_name`: Display name of the user
- `message`: The chat message content

**Optional Fields:**
- `username`: Username (defaults to lowercase display_name)
- `badges`: Array of badge objects with `name` field
- `platform`: `twitch`, `discord`, `youtube`, `irc` (affects styling)
- `overlay_key`: Target overlay (defaults to configured key)

**Notification Webhook**
```
POST /hooks/notification
Content-Type: application/json

{
  "type": "follow|subscribe|donation|raid|host",
  "title": "New Follower!",
  "message": "User just followed!",
  "duration_ms": 10000,
  "overlay_key": "local"
}
```

**Required Fields:**
- `type`: `follow`, `subscribe`, `donation`, `raid`, `host`
- `title`: Notification title
- `message`: Notification message

**Optional Fields:**
- `overlay_key`: Target overlay (defaults to configured key)
- `duration_ms`: Display duration (3000-15000ms, default: 10000)

## Direct Toast API

```
POST /overlay/{key}/toast
Content-Type: application/json

{
  "message": {
    "display_name": "Username",
    "username": "username",
    "badges": [{"name": "moderator"}],
    "message": "Toast message"
  },
  "options": {
    "duration_ms": 8000,
    "theme": "dark",
    "fontScale": 1.0,
    "animation": "slide-up",
    "safeMargin": 24
  }
}
```

## Available Options

```php
$options = [
    'duration_ms' => 8000,        // Auto-dismiss time (1000-30000)
    'theme' => 'dark',            // 'dark' or 'light'
    'fontScale' => 1.0,           // Font size multiplier (0.5-3.0)
    'animation' => 'slide-up',    // Animation type
    'safeMargin' => 24,           // Margin from screen edges (0-100)
];
```
