# Routes & Endpoints

## Web Routes

**Control Panel**
```
GET /control
```
Returns the control panel view for managing chat messages and overlay.

**Overlay Page**
```
GET /overlay/{key}
```
Returns the overlay page for the given key. The key is validated against `config('overlay.key')`.
Returns 404 if the key doesn't match.

Example: `/overlay/local`

## URL Parameters for Overlay

The overlay page accepts URL parameters for customization:

```
/overlay/local?theme=dark&fontScale=1.2&safeMargin=50&animation=slide-up
```

**Available Parameters:**
- `theme`: `dark` or `light` (default: dark)
- `fontScale`: Font size multiplier 0.5 to 3.0 (default: 1.0)
- `animation`: `slide-up`, `slide-down`, `slide-left`, `slide-right`, `fade`, `zoom` (default: slide-up)
- `safeMargin`: Margin from screen edges 0 to 100 pixels (default: 24)

## Broadcasting Channels

**MessageReceived Channel**
- Channel: `local`
- Event: `MessageReceived`
- Payload: `{ message: { id, display_name, username, message, badges, timestamp } }`

**Overlay Channel**
- Channel: `overlay.{key}`
- Events: `MessagePromoted`, `MessageDemoted`
- Promoted Payload: `{ message: Message }`
- Demoted Payload: `{ messageId: int }`
