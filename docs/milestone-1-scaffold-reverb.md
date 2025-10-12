# Milestone 1: Scaffold & Reverb Wired

## Goal
Set up the foundational Laravel 12 application with Reverb broadcasting, Livewire 3 with Volt single-file components, and Flux UI components. Establish the real-time communication infrastructure with dummy broadcasts for testing.

## Success Criteria
- Laravel 12 application with all required packages installed
- Reverb broadcasting configured and working
- Echo client bootstrapped for frontend
- Dummy broadcasts can be sent and received round-trip
- Basic project structure established

## Implementation Steps

### Step 1.1: Install Core Dependencies
**Files to modify:**
- `composer.json`
- `package.json`

**Actions:**
1. Install Laravel Reverb: `composer require laravel/reverb`
2. Install Livewire 3 and Volt: `composer require livewire/livewire livewire/volt:^1`
3. Install Flux UI: `npm install @flux-ui/pro`
4. Ensure PHP version requirement is set to 8.4.11 (this app uses PHP 8.4.11)
5. Run `composer install` and `npm install`

**Verification:**
- All packages install without errors
- Check `composer.json` and `package.json` for correct versions

### Step 1.2: Configure Environment Variables
**Files to modify:**
- `.env`
- `config/overlay.php` (new)

**Actions:**
1. Add Reverb configuration:
   ```dotenv
   REVERB_APP_ID=chat-overlay
   REVERB_APP_KEY=local-key
   REVERB_APP_SECRET=local-secret
   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   BROADCAST_CONNECTION=reverb
   ```
2. Add overlay key to `.env` and create config mapping:
   ```dotenv
   OVERLAY_KEY=local
   ```
   Create `config/overlay.php` with:
   ```php
   <?php
   return [
       'key' => env('OVERLAY_KEY', 'local'),
   ];
   ```
   Use `config('overlay.key')` in application code instead of calling `env()` directly.
3. Add Twitch configuration (placeholder values):
   ```dotenv
   TWITCH_NICK=your_twitch_username
   TWITCH_OAUTH=oauth:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   TWITCH_CHANNEL=targetchannel
   TWITCH_IRC_HOST=irc.chat.twitch.tv
   TWITCH_IRC_PORT=6667
   ```

**Verification:**
- Environment variables are properly set
- No syntax errors in `.env` file

### Step 1.3: Configure Broadcasting
**Files to modify:**
- `config/broadcasting.php`
- `bootstrap/app.php`

**Actions:**
1. Update `config/broadcasting.php` to include Reverb driver configuration
2. Register broadcasting middleware/providers in `bootstrap/app.php` per Laravel 12 structure (no legacy Kernel)
3. Set default broadcast connection to 'reverb'

**Verification:**
- Broadcasting configuration loads without errors
- Reverb driver is properly configured

### Step 1.4: Bootstrap Echo Client
**Files to modify:**
- `resources/js/bootstrap.js`
- `resources/js/app.js`
- `resources/css/app.css`
- `vite.config.js`

**Actions:**
1. Configure `window.Echo` in `resources/js/bootstrap.js` with Reverb settings
2. Import bootstrap in `resources/js/app.js`
3. Ensure Vite builds include Echo bootstrap for all routes
4. Import Tailwind v4 in `resources/css/app.css` using `@import "tailwindcss";` (v4 style; do not use `@tailwind` directives)
5. Test Echo connection with dummy event

**Verification:**
- Echo client initializes without errors
- Can connect to Reverb server
- Console shows successful connection

### Step 1.5: Create Basic Event Classes
**Files to create:**
- `app/Events/NewChatMessage.php`
- `app/Events/ToastShow.php`
- `app/Events/ToastHide.php`

**Actions:**
1. Create `NewChatMessage` event with proper payload structure
2. Create `ToastShow` event with message and options payload
3. Create `ToastHide` event with reason payload
4. Implement `ShouldBroadcast` (or `ShouldBroadcastNow` for immediate UI reflection during local dev) on all events
5. Set broadcast channels (`chat.messages`, `overlay.{key}`)

**Verification:**
- Events can be dispatched without errors
- Payload structure matches specification
- Broadcast channels are correctly configured

### Step 1.6: Create Database Schema
**Files to create:**
- `database/migrations/xxxx_create_chat_messages_table.php`
- `database/migrations/xxxx_create_overlay_states_table.php`

**Actions:**
1. Create `chat_messages` migration with fields:
   - `id` (primary key)
   - `twitch_msg_id` (nullable string)
   - `username` (string)
   - `display_name` (string)
   - `badges` (json)
   - `message` (text)
   - `received_at` (timestamp)
   - `meta` (json, nullable)
2. Create `overlay_states` migration with fields:
   - `id` (primary key)
   - `overlay_key` (string, unique)
   - `active_message_id` (nullable foreign key)
   - `dismiss_at` (nullable timestamp)
   - `style` (json)
3. Run migrations

4. Add helpful indexes:
   - Index `chat_messages.username`
   - Index `chat_messages.received_at`

**Verification:**
- Migrations run successfully
- Tables created with correct structure
- Foreign key constraints work properly

### Step 1.7: Create Models
**Files to create:**
- `app/Models/ChatMessage.php`
- `app/Models/OverlayState.php`

**Actions:**
1. Create `ChatMessage` model with proper relationships and casts (use a `casts()` method, not `$casts`)
2. Create `OverlayState` model with proper relationships and casts (use a `casts()` method)
3. Add any necessary scopes or accessors

**Verification:**
- Models can be instantiated without errors
- JSON casts work properly
- Relationships are correctly defined

### Step 1.8: Test Dummy Broadcasts
**Files to create:**
- `routes/web.php` (add test routes)
- `app/Http/Controllers/TestController.php` (optional)

**Actions:**
1. Create test route to dispatch dummy events
2. Test `NewChatMessage` broadcast to `chat.messages` channel
3. Test `ToastShow` broadcast to `overlay.local` channel
4. Test `ToastHide` broadcast to `overlay.local` channel
5. Verify events are received by Echo client

**Verification:**
- Events dispatch without errors
- Echo client receives events in browser console
- Payload structure is correct
- Round-trip communication works

### Step 1.9: Start Reverb Server
**Actions:**
1. Start Reverb server: `php artisan reverb:start`
2. Verify server starts on configured port
3. Test connection from Echo client

**Verification:**
- Reverb server starts without errors
- Echo client can connect to server
- No connection timeouts or errors

## Testing Checklist
- [ ] All packages install correctly
- [ ] Environment variables are set
- [ ] `config/overlay.php` exists and code uses `config('overlay.key')`
- [ ] Broadcasting configuration works
- [ ] Echo client connects to Reverb
- [ ] Dummy events can be dispatched and received
- [ ] Database migrations run successfully
- [ ] Models can be created and saved
- [ ] Reverb server starts and runs
- [ ] Tailwind v4 is imported via `@import "tailwindcss";`
- [ ] `vendor/bin/pint --dirty` has been run and code is formatted
- [ ] If UI doesn’t update, `npm run dev` is running

## Next Steps
Once this milestone is complete, proceed to **Milestone 2: Overlay MVP** to build the static toast component with URL-based appearance control.
