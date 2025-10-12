# Milestone 3: Control Panel MVP

## Goal
Create a control panel interface that displays a live chat feed, allows operators to activate specific messages as toasts, and provides a preview of the current active toast. The interface should use Flux UI components and receive real-time updates via Reverb. Use Volt single-file components for interactivity.

## Success Criteria
- Control panel accessible at `/control`
- Live chat feed displays messages with user info, badges, and timestamps
- Search/filter functionality by username or message content
- "Activate toast" and "Clear toast" buttons for individual messages
- Global clear button for current active toast
- Duration input when activating toasts
- Preview panel showing current active toast state
- Real-time updates via `chat.messages` channel

## Implementation Steps

### Step 3.1: Create Control Panel Route and Layout
**Files to create:**
- `routes/web.php` (add control route)
- `resources/views/layouts/control.blade.php`

**Actions:**
1. Add route `GET /control` to `routes/web.php`
2. Create control panel layout with Flux UI styling
3. Include Echo bootstrap for real-time events
4. Set up responsive grid layout for chat feed and preview panel
5. Include necessary CSS and JavaScript assets

**Verification:**
- Route responds correctly
- Layout renders with proper styling
- Echo client initializes properly

### Step 3.2: Create Chat Feed Volt Component
**Files to create:**
- `resources/views/livewire/control/chat-feed.blade.php` (Volt single-file component)

**Actions:**
1. Create Volt component with:
   - Properties for messages, search, filters
   - Methods for activating and clearing toasts
   - Real-time message handling
   - Search and filter functionality
2. In the same file, create the Blade template with Flux UI components using `<flux:*>` tags (reuse existing styling where present):
   - Search input with filter options
   - Message list/table with user info
   - Action buttons for each message
   - Loading states and empty states

**Verification:**
- Component mounts without errors
- Messages display correctly
- Search and filter functionality works

### Step 3.3: Implement Message Display
**Files to modify:**
- `resources/views/livewire/control/chat-feed.blade.php`

**Actions:**
1. Create message list using Flux UI components (`<flux:table>`, `<flux:button>`, etc.)
2. Display for each message:
   - Username and display name
   - Badges (moderator, subscriber, etc.)
   - Message text
   - Timestamp
   - Action buttons (Activate, Clear)
3. Style messages with proper spacing and typography
4. Handle long messages with text truncation

**Verification:**
- Messages display with all required information
- Badges render correctly
- Text truncation works for long messages
- Timestamps are formatted properly

### Step 3.4: Add Search and Filter Functionality
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `resources/views/livewire/control/chat-feed.blade.php`

**Actions:**
1. Add search input with Flux UI styling
2. Implement search by username functionality
3. Implement search by message content functionality
4. Add filter options (all, moderators, subscribers, etc.)
5. Implement real-time search with debouncing using `wire:model.live.debounce.300ms`
6. Update message list based on search/filter criteria

**Verification:**
- Search by username works correctly
- Search by message content works correctly
- Filters apply properly
- Real-time search updates without lag
- Empty state shows when no results

### Step 3.5: Implement Toast Activation
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `app/Http/Controllers/ToastController.php`

**Actions:**
1. Create `ToastController` with `activate` method
2. Add API route `POST /api/toasts/activate`
3. Implement activation logic:
   - Update `overlay_states` table
   - Broadcast `ToastShow` event
   - Handle duration and styling options
4. Add activation button to each message
5. Add duration input modal/form
6. Handle activation success/error states

**Verification:**
- Activation button works for individual messages
- Duration input accepts valid values
- API endpoint updates database correctly
- `ToastShow` event is broadcast
- Success/error feedback is shown

### Step 3.6: Implement Toast Clearing
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `app/Http/Controllers/ToastController.php`

**Actions:**
1. Add `clear` method to `ToastController`
2. Add API route `POST /api/toasts/clear`
3. Implement clear logic:
   - Update `overlay_states` table
   - Broadcast `ToastHide` event
4. Add clear button to each message
5. Add global clear button
6. Handle clear success/error states

**Verification:**
- Clear button works for individual messages
- Global clear button works
- API endpoint updates database correctly
- `ToastHide` event is broadcast
- Success/error feedback is shown

### Step 3.7: Create Toast Preview Panel
**Files to create:**
- `resources/views/livewire/control/toast-preview.blade.php` (Volt single-file component)

**Actions:**
1. Create Livewire component for toast preview
2. Subscribe to `overlay.{key}` channel
3. Display current active toast state
4. Show toast options (duration, theme, etc.)
5. Add manual clear button
6. Style preview to match overlay appearance

**Verification:**
- Preview panel shows current active toast
- Updates in real-time when toast changes
- Manual clear button works
- Preview styling matches overlay

### Step 3.8: Subscribe to Real-Time Events
**Files to modify:**
- `resources/views/livewire/control/chat-feed.blade.php`
- `resources/views/livewire/control/toast-preview.blade.php`

**Actions:**
1. Subscribe to `chat.messages` channel in ChatFeed component
2. Handle `NewChatMessage` events to add messages to feed
3. Subscribe to `overlay.{key}` channel in ToastPreview component
4. Handle `ToastShow` and `ToastHide` events in preview
5. Update component state in real-time
6. Handle connection errors gracefully

**Verification:**
- New messages appear in feed automatically
- Preview updates when toast state changes
- Real-time updates work without page refresh
- Connection errors are handled gracefully

### Step 3.9: Add Loading States and Error Handling
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `resources/views/livewire/control/chat-feed.blade.php`
- `resources/views/livewire/control/toast-preview.blade.php`

**Actions:**
1. Add loading states for message operations
2. Add error handling for API calls
3. Add connection status indicators
4. Add retry functionality for failed operations
5. Add empty states for no messages
6. Add loading spinners and progress indicators

**Verification:**
- Loading states show during operations
- Error messages are displayed clearly
- Connection status is visible
- Retry functionality works
- Empty states display properly

### Step 3.10: Implement Message Pagination/Virtualization
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `resources/views/livewire/control/chat-feed.blade.php`

**Actions:**
1. Implement message pagination or virtualization
2. Limit displayed messages to prevent performance issues
3. Add "load more" functionality if needed
4. Optimize for large message volumes
5. Add message count indicators

**Verification:**
- Large message volumes don't cause performance issues
- Pagination works correctly
- Message count is accurate
- Performance remains smooth

### Step 3.11: Add Keyboard Shortcuts
**Files to modify:**
- `app/Livewire/Control/ChatFeed.php`
- `resources/views/livewire/control/chat-feed.blade.php`

**Actions:**
1. Add keyboard shortcuts for common actions:
   - Space: Activate selected message
   - Escape: Clear current toast
   - Ctrl+F: Focus search input
2. Add visual indicators for keyboard shortcuts
3. Handle keyboard event conflicts
4. Add accessibility support

**Verification:**
- Keyboard shortcuts work correctly
- Visual indicators are clear
- No conflicts with browser shortcuts
- Accessibility features work

### Step 3.12: Test Control Panel Functionality
**Files to create:**
- `routes/web.php` (add test routes)
- `app/Http/Controllers/TestController.php` (optional)

**Actions:**
1. Create test routes to dispatch `NewChatMessage` events
2. Test message activation with different durations
3. Test message clearing functionality
4. Test search and filter functionality
5. Test real-time updates
6. Test keyboard shortcuts

**Verification:**
- All functionality works as expected
- Real-time updates work correctly
- Error handling works properly
- Performance is acceptable

## Testing Checklist
- [ ] Control panel route responds correctly
- [ ] Chat feed displays messages properly
- [ ] Search by username works
- [ ] Search by message content works
- [ ] Filters apply correctly
- [ ] Toast activation works for individual messages
- [ ] Toast activation works with duration input
- [ ] Toast clearing works for individual messages
- [ ] Global clear button works
- [ ] Preview panel shows current toast state
- [ ] Real-time updates work for new messages
- [ ] Real-time updates work for toast state changes
- [ ] Loading states display correctly
- [ ] Error handling works properly
- [ ] Keyboard shortcuts work
- [ ] Performance is acceptable with large message volumes
- [ ] Flux components use `<flux:*>` tags correctly and existing styling is preserved where already implemented

## Next Steps
Once this milestone is complete, proceed to **Milestone 4: IRC Relay** to implement the Twitch IRC connection and message processing.
