# Milestone 2: Overlay MVP

## Goal
Create a static toast component (as a Volt single-file component) that can be displayed in OBS as a browser source. The overlay should receive `ToastShow` and `ToastHide` events, support URL-based appearance control, and have a transparent background suitable for streaming.

## Success Criteria
- Overlay page accessible at `/overlay/{key}` with key validation
- Static toast component with configurable appearance via URL parameters
- Receives and displays `ToastShow` events from Reverb
- Responds to `ToastHide` events to clear the toast
- Transparent background suitable for OBS browser source
- URL parameters control theme, font scale, animation, and safe margin

## Implementation Steps

### Step 2.1: Create Overlay Route and Controller
**Files to create:**
- `routes/web.php` (add overlay route)
- `app/Http/Controllers/OverlayController.php`

**Actions:**
1. Add route `GET /overlay/{key}` to `routes/web.php`
2. Create `OverlayController` with `show` method
3. Validate overlay key against `config('overlay.key')` (do not call `env()` in code)
4. Return 404 if key doesn't match
5. Pass URL parameters to the view

**Verification:**
- Route responds correctly with valid key
- Returns 404 with invalid key
- URL parameters are passed to view

### Step 2.2: Create Overlay Layout
**Files to create:**
- `resources/views/layouts/overlay.blade.php`

**Actions:**
1. Create minimal layout with transparent background
2. Include necessary CSS for transparency: `body { background: transparent; }`
3. Include Echo bootstrap for real-time events
4. Include Vite assets for styling and JavaScript
5. Ensure no margins or padding that could interfere with OBS
6. Ensure Tailwind v4 is imported via `@import "tailwindcss";` and avoid deprecated utilities

**Verification:**
- Layout renders with transparent background
- Echo client initializes properly
- No unwanted background elements visible

### Step 2.3: Create Toast Display Volt Component
**Files to create:**
- `resources/views/livewire/overlay/toast-display.blade.php` (Volt single-file component: PHP class block + Blade template)

**Actions:**
1. Create Volt component with:
   - Properties for toast state (message, options, visibility)
   - Mount method to read URL parameters
   - Methods to handle `ToastShow` and `ToastHide` events
   - Auto-dismiss timer functionality
2. In the same file (below the class), create the Blade template with:
   - Toast container with proper positioning
   - Message display with username, badges, and text
   - CSS classes for animations and styling
   - Safe margin support

**Verification:**
- Component mounts without errors
- URL parameters are read correctly
- Component state updates properly

### Step 2.4: Implement URL Parameter Parsing
**Files to modify:**
- `resources/views/livewire/overlay/toast-display.blade.php` (Volt class block)

**Actions:**
1. Parse URL parameters in mount method:
   - `theme` (default: 'dark')
   - `fontScale` (default: 1.0)
   - `animation` (default: 'slide-up')
   - `safeMargin` (default: 24)
2. Validate parameter values
3. Store parsed options in component state

**Verification:**
- All URL parameters are parsed correctly
- Default values are applied when parameters are missing
- Invalid values are handled gracefully

### Step 2.5: Subscribe to Reverb Events
**Files to modify:**
- `resources/views/livewire/overlay/toast-display.blade.php` (Volt class block + template)

**Actions:**
1. Subscribe to `overlay.{key}` channel in component
2. Implement `onToastShow` method to handle incoming toast events
3. Implement `onToastHide` method to handle hide events
4. Update component state based on events
5. Trigger Livewire updates when events are received

**Verification:**
- Component subscribes to correct channel
- Events are received and processed
- Component state updates in real-time

### Step 2.6: Implement Toast Display Logic
**Files to modify:**
- `resources/views/livewire/overlay/toast-display.blade.php`

**Actions:**
1. Create toast container with proper styling
2. Display message structure:
   - Display name (bold)
   - Badges (inline icons)
   - Message text (wrapped to 2-3 lines)
3. Apply theme styling (dark/light)
4. Apply font scaling
5. Apply safe margin padding

**Verification:**
- Toast displays correctly with sample data
- Styling matches design requirements
- Text wrapping works properly

### Step 2.7: Implement Auto-Dismiss Timer
**Files to modify:**
- `resources/views/livewire/overlay/toast-display.blade.php` (Volt class block)

**Actions:**
1. Add timer property to component
2. Start timer when toast is shown (if duration provided)
3. Clear timer when toast is hidden
4. Auto-dismiss toast when timer expires
5. Handle timer cleanup on component destruction

**Verification:**
- Timer starts correctly when toast is shown
- Toast auto-dismisses after specified duration
- Timer is cleaned up properly

### Step 2.8: Add CSS Animations
**Files to create/modify:**
- `resources/css/app.css` (add animation classes)
- `resources/views/livewire/overlay/toast-display.blade.php`

**Actions:**
1. Create CSS classes for enter/exit animations:
   - Enter: translateY(20px) → 0, fade in 200-300ms
   - Exit: fade out 200ms
2. Add transition classes to toast container
3. Trigger animations via Livewire state changes
4. Ensure animations work with different animation types

**Verification:**
- Animations play smoothly
- Different animation types work correctly
- No layout shifts during animations

### Step 2.9: Implement Badge Display
**Files to modify:**
- `resources/views/livewire/overlay/toast-display.blade.php`

**Actions:**
1. Parse badges JSON from message data
2. Display badge icons inline with username
3. Style badges appropriately (small, aligned)
4. Handle missing or invalid badge data

**Verification:**
- Badges display correctly
- Missing badges are handled gracefully
- Badge styling is consistent

### Step 2.10: Test Overlay Functionality
**Files to create:**
- `routes/web.php` (add test route)
- `app/Http/Controllers/TestController.php` (optional)

**Actions:**
1. Create test route to dispatch `ToastShow` events
2. Test overlay with different URL parameters
3. Test auto-dismiss functionality
4. Test manual clear functionality
5. Verify overlay works in OBS browser source

**Verification:**
- Overlay responds to test events
- URL parameters control appearance correctly
- Auto-dismiss works as expected
- Overlay displays properly in OBS

## Testing Checklist
- [ ] Overlay route responds with valid key
- [ ] Invalid key returns 404
- [ ] URL parameters are parsed correctly
- [ ] Toast displays with sample data
- [ ] `ToastShow` events are received and displayed
- [ ] `ToastHide` events clear the toast
- [ ] Auto-dismiss timer works
- [ ] Animations play smoothly
- [ ] Badges display correctly
- [ ] Transparent background works in OBS
- [ ] Different themes and font scales work
- [ ] Safe margin is applied correctly

## Next Steps
Once this milestone is complete, proceed to **Milestone 3: Control Panel MVP** to build the live chat feed interface with activation and clear functionality.
