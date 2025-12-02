# Twitchy - Stream Overlay System

A Laravel-based real-time overlay system for streamers to display chat messages, notifications, and other interactive content on their streams.

## Overview

Twitchy provides a clean, customizable overlay system that displays chat messages and notifications as toasts on your stream. It uses Laravel Reverb WebSockets for real-time updates with a control panel for managing promoted messages.

## Features

- 🎨 **Customizable Toasts**: Display chat messages with user badges, themes, and animations
- 🔄 **Real-time Updates**: Laravel Reverb WebSockets for instant updates
- 🎮 **IRC Integration**: Built-in Twitch IRC relay for automatic chat message capture
- 🎯 **Control Panel**: Web interface for managing and promoting chat messages
- 🎨 **Theme Support**: Dark and light themes with customizable styling
- 📱 **Responsive Design**: Works on various screen sizes and resolutions
- ⚡ **Performance**: Lightweight with minimal overhead
- ⌨️ **Typing Animation**: Realistic typing effect for message display

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

### Control Panel

1. **Access the control panel** at: `http://your-domain.test/control`
2. **Start the IRC relay** to capture chat messages:
   ```bash
   php artisan twitch:relay --channel=yourchannel
   ```
3. **Promote messages** by clicking on them in the chat feed
4. **Manage promoted messages** using the toast preview panel

### Overlay Setup

1. **Access your overlay** at: `http://your-domain.test/overlay/local`
2. **Add as Browser Source** in OBS/Streamlabs with these settings:
   - URL: `http://your-domain.test/overlay/local`
   - Width: 1920
   - Height: 1080
   - Custom CSS (optional): Transparent background

### IRC Relay Commands

**Start IRC relay for a specific channel:**
```bash
php artisan twitch:relay --channel=yourchannel
```

**Run the full stack (IRC relay + frontend dev server):**
```bash
php artisan twitchy:run --channel=yourchannel
```

**Available options:**
- `--channel=channelname`: Twitch channel to connect to
- `--dry-run`: Test mode without saving messages
- `--no-frontend`: Skip frontend dev server
- `--no-relay`: Skip IRC relay

## Configuration

### Environment Variables

Configure your Twitch credentials in `.env`:

```env
# Twitch IRC Configuration
TWITCH_OAUTH=oauth:your_oauth_token
TWITCH_NICK=your_bot_username
TWITCH_CHANNEL=your_channel_name

# Overlay Configuration
OVERLAY_KEY=local

# Reverb Configuration (for real-time updates)
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Twitch OAuth Setup

1. Go to [Twitch Developer Console](https://dev.twitch.tv/console)
2. Create a new application
3. Generate an OAuth token with `chat:read` scope
4. Add the token to your `.env` file

## Development

### Local Development

**Start the development server:**
```bash
php artisan serve
```

**Start IRC relay in another terminal:**
```bash
php artisan twitch:relay --channel=yourchannel
```

**Or run everything together:**
```bash
php artisan twitchy:run --channel=yourchannel
```

### Testing

Test the overlay system:

1. **View overlay**: `http://your-domain.test/overlay/local`
2. **Control panel**: `http://your-domain.test/control`
3. **Start IRC relay**: `php artisan twitch:relay --channel=yourchannel`

## Architecture

### Components

- **OverlayController**: Handles overlay display
- **TwitchRelayCommand**: IRC relay for capturing Twitch chat messages
- **Message Actions**: CreateMessage, PromoteMessage, DemoteMessage for message management
- **ToastDisplay**: Livewire component for rendering toasts with typing animation
- **Control Panel**: Web interface for managing promoted messages

### Data Flow

1. **Twitch IRC** → TwitchRelayCommand → Creates Message → Broadcasts MessageReceived event via Reverb
2. **Control Panel** → User promotes message → Broadcasts MessagePromoted event via Reverb
3. **Overlay** → Listens to Reverb events → Displays toast with typing animation
4. **Auto-dismiss** → Clears toast after duration

### Real-time System

- **Laravel Reverb**: WebSocket server for real-time communication
- **Events**: MessageReceived, MessagePromoted, MessageDemoted broadcast via Reverb
- **Livewire**: Real-time UI updates using Reverb WebSocket connections
- **Typing Animation**: JavaScript-based typing effect for realistic message display

## Troubleshooting

### Common Issues

**Toasts not appearing:**
- Check overlay URL and key
- Verify Reverb WebSocket connection in browser dev tools
- Ensure IRC relay is running and connected to Twitch
- Check browser console for JavaScript errors

**IRC relay not connecting:**
- Verify Twitch OAuth token is valid
- Check channel name is correct
- Ensure bot has permission to read chat
- Check Laravel logs for connection errors

**Real-time updates not working:**
- Verify Reverb server is running: `php artisan reverb:start`
- Check WebSocket connection in browser dev tools
- Ensure events are being broadcast correctly

### Debug Mode

Enable debug logging in `.env`:
```env
LOG_LEVEL=debug
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

Test Reverb connection:
```bash
php artisan reverb:start --debug
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
