# Development Workflows

## Starting Development Environment

```bash
# Start Reverb WebSocket server
php artisan reverb:start

# Start Laravel development server
php artisan serve

# Start Twitch IRC relay (in separate terminal)
php artisan twitch:relay --channel=yourchannel

# Or use the unified command
php artisan twitchy:run --channel=yourchannel

# Start frontend build (in separate terminal)
npm run dev

# Or use composer dev script (runs all in parallel)
composer run dev
```

## Configuration Files

**Environment Variables (Critical)**
```dotenv
# Overlay Security
OVERLAY_KEY=local                    # Must match URL path segment

# Reverb Configuration
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=chat-overlay
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080

# Twitch IRC (optional if using IRC relay)
TWITCH_NICK=your_twitch_username
TWITCH_OAUTH=oauth:token             # from https://twitchapps.com/tmi/
TWITCH_CHANNEL=targetchannel
```

**Configuration Files**
- `config/overlay.php` - Overlay-specific settings
- `config/broadcasting.php` - Reverb configuration
- Standard Laravel config files apply

## Testing Approach

**Test Organization**
- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`
- Browser tests: `tests/Browser/` (Pest v4 browser testing)
- Follow Pest conventions (use `it()` and `test()` functions)

**Test Coverage Requirements**
- Write tests for all new features
- Test message flow: IRC parsing → Storage → Broadcasting → Display
- Test overlay activation/deactivation
- Test real-time event handling
- Browser tests for overlay rendering and animations

**Running Tests**
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/OverlayTest.php

# Run with filter
php artisan test --filter=message

# Browser testing (if configured)
php artisan test tests/Browser/
```

## Code Quality Standards

**Before Committing**
```bash
# Format code with Pint
vendor/bin/pint --dirty

# Run tests
php artisan test

# Check for issues
php artisan route:list
php artisan config:clear
```

**Code Review Checklist**
- [ ] Follows existing naming conventions
- [ ] Uses appropriate design patterns (Action, Store, Processor)
- [ ] Includes tests for new functionality
- [ ] Events properly broadcast to correct channels
- [ ] Validates user input
- [ ] Error handling in place
- [ ] No hardcoded configuration (use `config()`)
- [ ] Pint formatting applied
