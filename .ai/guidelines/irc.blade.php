# Twitch IRC Integration

## IRC Command Usage

```bash
# Connect to specific channel
php artisan twitch:relay --channel=channelname

# Dry run mode (log only, don't store)
php artisan twitch:relay --channel=channelname --dry-run
```

## IRC Message Flow

1. **Connect**: TCP connection to `irc.chat.twitch.tv:6667`
2. **Authenticate**: Send `PASS` (OAuth token) and `NICK` commands
3. **Join**: `JOIN #channelname`
4. **Parse**: Extract display name, badges, username, message from `PRIVMSG`
5. **Store**: Persist to database and cache
6. **Broadcast**: Fire `MessageReceived` event to Reverb
7. **Handle PING**: Respond to `PING` with `PONG` to maintain connection

## Message Processing

- Use `TwitchMessageProcessor` for parsing IRC messages
- Extract Twitch-specific metadata (badges, emotes, etc.)
- Normalize data before storage
- Handle reconnection with exponential backoff

## OAuth Token

Get your OAuth token from: https://twitchapps.com/tmi/

Add to `.env`:
```dotenv
TWITCH_NICK=your_twitch_username
TWITCH_OAUTH=oauth:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWITCH_CHANNEL=targetchannel
```

## Important Notes

- **Channel name has no # prefix** in the command or .env file
- **OAuth token must be valid** - regenerate if connection fails
- **Connection resilience** - The relay handles disconnections with exponential backoff
- **PING/PONG** - Critical for maintaining the IRC connection
