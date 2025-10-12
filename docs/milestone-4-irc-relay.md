# Milestone 4: IRC Relay

## Goal
Implement the Twitch IRC connection and message processing system. Create an Artisan command that connects to Twitch IRC, joins a specified channel, parses incoming messages, stores them in the database, and broadcasts them via Reverb for real-time updates.

## Success Criteria
- Artisan command `twitch:relay` connects to Twitch IRC successfully
- Command accepts `--channel` parameter or uses environment variable
- IRC connection handles authentication and channel joining
- Message parsing extracts display name, badges, and message content
- Messages are stored in `chat_messages` table
- `NewChatMessage` events are broadcast via Reverb
- Connection resilience with automatic reconnection
- Proper error handling and logging

## Implementation Steps

### Step 4.1: Create Artisan Command Structure
**Files to create:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Create command class extending `Illuminate\Console\Command`
2. Define command signature: `twitch:relay --channel={channel?}`
3. Add command description and help text
4. Implement basic command structure with options handling
5. Add dry-run option for testing

**Verification:**
- Command is registered and accessible via `php artisan list`
- Help text displays correctly
- Options are parsed properly

### Step 4.2: Implement IRC Connection Logic
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Add properties for IRC connection (socket, channel, etc.)
2. Implement connection method using PHP streams
3. Connect to `TWITCH_IRC_HOST:TWITCH_IRC_PORT`
4. Handle connection errors and timeouts
5. Add connection status logging

**Verification:**
- Connection establishes successfully
- Connection errors are handled gracefully
- Logging provides clear status information

### Step 4.3: Implement IRC Authentication
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Send authentication commands to IRC server:
   - `PASS {TWITCH_OAUTH}`
   - `NICK {TWITCH_NICK}`
2. Wait for authentication response
3. Handle authentication errors
4. Validate successful authentication
5. Add authentication status logging

**Verification:**
- Authentication succeeds with valid credentials
- Authentication errors are handled properly
- Logging shows authentication status

### Step 4.4: Implement Channel Joining
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Send `JOIN #{channel}` command
2. Wait for join confirmation
3. Handle join errors (channel doesn't exist, banned, etc.)
4. Validate successful channel join
5. Add channel join status logging

**Verification:**
- Channel join succeeds for valid channels
- Join errors are handled properly
- Logging shows join status

### Step 4.5: Implement Message Reading Loop
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Create main message reading loop
2. Read lines from IRC socket
3. Handle socket read errors and timeouts
4. Implement graceful shutdown on command interruption
5. Add loop status logging

**Verification:**
- Message loop runs continuously
- Socket errors are handled gracefully
- Command can be interrupted cleanly
- Logging shows loop status

### Step 4.6: Implement PING/PONG Handling
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Parse incoming lines for PING messages
2. Respond with PONG to keep connection alive
3. Handle PING/PONG timing
4. Log PING/PONG activity for debugging
5. Ensure connection stays alive

**Verification:**
- PING messages are detected correctly
- PONG responses are sent properly
- Connection stays alive during idle periods
- Logging shows PING/PONG activity

### Step 4.7: Implement Message Parsing
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Parse `PRIVMSG` lines for chat messages
2. Extract IRC tags for badges and metadata
3. Parse display name, username, and message content
4. Handle special characters and encoding
5. Validate parsed message data

**Verification:**
- PRIVMSG lines are parsed correctly
- All message components are extracted
- Special characters are handled properly
- Invalid messages are filtered out

### Step 4.8: Create Message Model and Storage
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`
- `app/Models/ChatMessage.php`

**Actions:**
1. Create `ChatMessage` instances from parsed data
2. Set all required fields (username, display_name, badges, message, etc.)
3. Save messages to database
4. Handle database errors gracefully
5. Add message storage logging

**Verification:**
- Messages are saved to database correctly
- All fields are populated properly
- Database errors are handled gracefully
- Logging shows storage status

### Step 4.9: Implement Event Broadcasting
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`
- `app/Events/NewChatMessage.php`

**Actions:**
1. Create `NewChatMessage` event instances
2. Dispatch events after message storage
3. Handle broadcast errors gracefully
4. Ensure events are sent to `chat.messages` channel
5. Add broadcast logging
6. For local, prefer `ShouldBroadcastNow` to avoid queue latency; document and revisit if queues are enabled later

**Verification:**
- Events are dispatched successfully
- Events contain correct payload data
- Broadcast errors are handled gracefully
- Logging shows broadcast status

### Step 4.10: Implement Connection Resilience
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Detect connection drops and errors
2. Implement exponential backoff for reconnection
3. Automatically reconnect on connection loss
4. Handle reconnection authentication and channel joining
5. Add reconnection logging

**Verification:**
- Connection drops are detected
- Reconnection attempts work properly
- Exponential backoff prevents spam
- Logging shows reconnection attempts

### Step 4.11: Add Error Handling and Logging
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Add comprehensive error handling for all operations
2. Implement structured logging with different levels
3. Add command progress indicators
4. Handle fatal errors gracefully
5. Add debug logging for troubleshooting

**Verification:**
- All errors are handled gracefully
- Logging provides useful debugging information
- Command progress is visible
- Fatal errors don't crash the command

### Step 4.12: Implement Dry-Run Mode
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Add `--dry-run` option to command
2. Skip database storage in dry-run mode
3. Skip event broadcasting in dry-run mode
4. Log what would be done in dry-run mode
5. Allow testing without affecting database

**Verification:**
- Dry-run mode works correctly
- No database changes occur in dry-run mode
- No events are broadcast in dry-run mode
- Logging shows what would be done

### Step 4.13: Add Configuration Validation
**Files to modify:**
- `app/Console/Commands/TwitchRelayCommand.php`

**Actions:**
1. Validate all required environment variables
2. Check IRC connection parameters
3. Validate channel name format
4. Provide helpful error messages for missing config
5. Exit gracefully if configuration is invalid

**Verification:**
- Missing configuration is detected
- Helpful error messages are shown
- Command exits gracefully on invalid config
- All required parameters are validated

### Step 4.14: Test IRC Relay Functionality
**Files to create:**
- `tests/Feature/TwitchRelayCommandTest.php`

**Actions:**
1. Create feature tests for the command
2. Test connection establishment
3. Test message parsing
4. Test database storage
5. Test event broadcasting
6. Test error handling

**Verification:**
- All tests pass
- Command functionality works as expected
- Error scenarios are handled properly
- Integration with database and events works

### Step 4.15: Create Message Simulator (Optional)
**Files to create:**
- `app/Console/Commands/TwitchSimulateCommand.php`

**Actions:**
1. Create `twitch:simulate` command for testing
2. Generate fake chat messages
3. Store messages in database
4. Broadcast `NewChatMessage` events
5. Allow configurable message frequency

**Verification:**
- Simulator generates realistic messages
- Messages are stored and broadcast correctly
- Simulator can be controlled for testing
- Integration with control panel works

## Testing Checklist
- [ ] Command is registered and accessible
- [ ] IRC connection establishes successfully
- [ ] Authentication works with valid credentials
- [ ] Channel joining works for valid channels
- [ ] PING/PONG keeps connection alive
- [ ] Message parsing extracts all components correctly
- [ ] Messages are stored in database
- [ ] Events are broadcast successfully
- [ ] Connection resilience works on drops
- [ ] Error handling works for all scenarios
- [ ] Dry-run mode works correctly
- [ ] Configuration validation works
- [ ] All tests pass
- [ ] Simulator works for testing (optional)

## Next Steps
Once this milestone is complete, proceed to **Milestone 5: Polish** to add animations, font scaling, badge rendering, filtering, and final polish to the application.
