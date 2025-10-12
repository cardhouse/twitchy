# Twitchy - Stream Overlay System

A Laravel-based real-time overlay system for streamers to display chat messages, notifications, and other interactive content on their streams.

## Overview

Twitchy provides a clean, customizable overlay system that displays chat messages and notifications as toasts on your stream. It uses a direct Livewire approach with cache-based polling for reliable, real-time updates without the complexity of WebSocket connections.

## Features

- 🎨 **Customizable Toasts**: Display chat messages with user badges, themes, and animations
- 🔄 **Real-time Updates**: Automatic polling system for live updates
- 🎮 **Multi-Platform Support**: Compatible with Twitch, Discord, YouTube, IRC, and more
- 🎯 **Simple API**: Easy-to-use REST endpoints for external integrations
- 🎨 **Theme Support**: Dark and light themes with customizable styling
- 📱 **Responsive Design**: Works on various screen sizes and resolutions
- ⚡ **Performance**: Lightweight with minimal overhead

## Installation

### Prerequisites

- PHP 8.4+
- Laravel 12
- Composer
- Node.js & NPM

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url> twitchy
   cd twitchy
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure the overlay key** (in `.env`)
   ```env
   OVERLAY_KEY=local
   ```

5. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Start the server**
   ```bash
   php artisan serve
   ```

## Usage

### Basic Overlay Setup

1. **Access your overlay** at: `http://your-domain.test/overlay/local`
2. **Add as Browser Source** in OBS/Streamlabs with these settings:
   - URL: `http://your-domain.test/overlay/local`
   - Width: 1920
   - Height: 1080
   - Custom CSS (optional): Transparent background

### URL Parameters

Customize the overlay appearance with URL parameters:

```
http://your-domain.test/overlay/local?theme=dark&fontScale=1.2&safeMargin=50
```

**Available Parameters:**
- `theme`: `dark` or `light` (default: dark)
- `fontScale`: 0.5 to 3.0 (default: 1.0)
- `animation`: `slide-up`, `slide-down`, `slide-left`, `slide-right`, `fade`, `zoom` (default: slide-up)
- `safeMargin`: 0 to 100 pixels (default: 24)

## API Endpoints

### Chat Messages

**Endpoint:** `POST /hooks/chat-message`

Display chat messages on the overlay.

```bash
curl -X POST http://your-domain.test/hooks/chat-message \
  -H "Content-Type: application/json" \
  -d '{
    "display_name": "StreamerFan123",
    "username": "streamerfan123",
    "message": "Hello from chat! 👋",
    "badges": [
      {"name": "subscriber"},
      {"name": "vip"}
    ],
    "platform": "twitch",
    "overlay_key": "local"
  }'
```

**Required Fields:**
- `display_name`: Display name of the user
- `message`: The chat message content

**Optional Fields:**
- `username`: Username (defaults to lowercase display_name)
- `badges`: Array of badge objects with `name` field
- `platform`: `twitch`, `discord`, `youtube`, `irc` (affects styling)
- `overlay_key`: Target overlay (defaults to configured key)

### Notifications

**Endpoint:** `POST /hooks/notification`

Display system notifications (follows, subscriptions, donations, etc.).

```bash
curl -X POST http://your-domain.test/hooks/notification \
  -H "Content-Type: application/json" \
  -d '{
    "type": "follow",
    "title": "New Follower!",
    "message": "StreamerFan123 just followed! 🎉",
    "duration_ms": 10000,
    "overlay_key": "local"
  }'
```

**Required Fields:**
- `type`: `follow`, `subscribe`, `donation`, `raid`, `host`
- `title`: Notification title
- `message`: Notification message

**Optional Fields:**
- `overlay_key`: Target overlay (defaults to configured key)
- `duration_ms`: Display duration (3000-15000ms, default: 10000)

### Direct Toast API

**Endpoint:** `POST /overlay/{key}/toast`

Low-level API for custom toast messages.

```bash
curl -X POST http://your-domain.test/overlay/local/toast \
  -H "Content-Type: application/json" \
  -d '{
    "message": {
      "display_name": "CustomUser",
      "username": "customuser",
      "badges": [{"name": "custom"}],
      "message": "Custom toast message!"
    },
    "options": {
      "duration_ms": 8000,
      "theme": "dark",
      "fontScale": 1.0,
      "animation": "slide-up"
    }
  }'
```

## Development

### Using the OverlayService

For internal application use, inject or create the `OverlayService`:

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
    message: 'Something important happened!',
    options: ['duration_ms' => 12000]
);

// Custom toast
$overlay->showToast(
    message: [
        'display_name' => 'System',
        'username' => 'system',
        'badges' => [['name' => 'bot']],
        'message' => 'Custom message'
    ],
    options: ['theme' => 'light']
);

// Multiple overlays
$overlay->forOverlay('stream2')->showNotification('Title', 'Message');
```

### Available Options

```php
$options = [
    'duration_ms' => 8000,        // Auto-dismiss time (1000-30000)
    'theme' => 'dark',            // 'dark' or 'light'
    'fontScale' => 1.0,           // Font size multiplier (0.5-3.0)
    'animation' => 'slide-up',    // Animation type
    'safeMargin' => 24,           // Margin from screen edges (0-100)
];
```

### Testing

Test the overlay system:

1. **View overlay**: `http://your-domain.test/overlay/local`
2. **Test toast**: `http://your-domain.test/test/toast-show`
3. **Test page**: `http://your-domain.test/test/livewire-toast`

### Platform Integration Examples

**Twitch Bot (Python)**:
```python
import requests

def send_chat_message(display_name, message, badges=None):
    requests.post('http://your-domain.test/hooks/chat-message', json={
        'display_name': display_name,
        'message': message,
        'badges': badges or [],
        'platform': 'twitch'
    })
```

**Discord Bot (JavaScript)**:
```javascript
const axios = require('axios');

async function sendChatMessage(user, message) {
    await axios.post('http://your-domain.test/hooks/chat-message', {
        display_name: user.displayName,
        username: user.username,
        message: message,
        platform: 'discord'
    });
}
```

**OBS/Streamlabs Integration**:
```javascript
// In your streaming software's script
fetch('http://your-domain.test/hooks/notification', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        type: 'follow',
        title: 'New Follower!',
        message: `${followerName} just followed!`
    })
});
```

## Configuration

### Overlay Settings

Edit `config/overlay.php`:
```php
return [
    'key' => env('OVERLAY_KEY', 'local'),
    'default_theme' => 'dark',
    'cache_ttl' => 30, // seconds
];
```

### Multiple Overlays

To support multiple overlays, update your overlay key validation in `OverlayController.php`:

```php
public function show(Request $request, string $key): Response
{
    $allowedKeys = ['local', 'stream1', 'stream2'];
    
    if (!in_array($key, $allowedKeys)) {
        abort(404);
    }
    
    // ... rest of method
}
```

## Architecture

### Components

- **OverlayController**: Handles overlay display and API endpoints
- **ChatHookController**: Processes incoming chat/notification webhooks
- **OverlayService**: Core service for managing overlay events
- **ToastDisplay**: Livewire component for rendering toasts

### Data Flow

1. **External Source** → Webhook Endpoint (`/hooks/*`)
2. **Webhook** → OverlayService → Cache
3. **Overlay Page** → Polls for events → Displays toast
4. **Auto-dismiss** → Clears toast after duration

### Cache Strategy

- Events stored in cache with overlay-specific keys
- 30-second TTL for cleanup
- `cache()->pull()` ensures single consumption
- 2-second polling interval for real-time feel

## Troubleshooting

### Common Issues

**Toasts not appearing:**
- Check overlay URL and key
- Verify events are being cached: `curl http://your-domain.test/overlay/local/pending-toasts`
- Check browser console for JavaScript errors

**API errors:**
- Ensure CSRF protection is disabled for webhook routes
- Validate JSON payload structure
- Check Laravel logs for detailed errors

**Performance issues:**
- Reduce polling interval if needed
- Adjust cache TTL for your use case
- Monitor server resources during high traffic

### Debug Mode

Enable debug logging in `.env`:
```env
LOG_LEVEL=debug
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## Support

For questions or issues:
- Check the troubleshooting section
- Review the logs for error details
- Open an issue with detailed information
