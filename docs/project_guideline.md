# Twitch Chat Overlay – Project Guideline (Laravel 12, Reverb, Livewire 3, Flux Pro UI)

## 0) Summary / Goal

Build a two‑window tool:

1. **Overlay Page (OBS Browser Source)**\
   Displays a single “toast” of a selected chat message. The toast can be timed (auto‑dismiss) or persistent (manual clear).

2. **Control Panel (secondary window, not in OBS)**\
   Streams the live chat feed for a configured Twitch channel and lets the operator “Activate” a specific message as the on‑screen toast and later “Clear” it.

Realtime transport uses **Laravel Reverb**. UI uses **Livewire 3** and **Flux Pro UI**. No auth initially (local use).

A long‑running **Artisan command** connects to Twitch IRC for a given channel, relays messages into the Laravel app, and the app broadcasts them to the Control Panel (for selection) and to the Overlay (when activated).

---

## 1) Architecture & Data Flow

```
[Twitch IRC] --(TCP)--> [Artisan Command: twitch:relay]
      |                                    |
      |                       [MessageSaved Event -> DB]
      |                                    |
      v                                    v
                           [Broadcast: chat.messages (Reverb)]
                                         |
                                         v
           [Control Panel (Livewire + Flux UI): stream list, Activate]
                                         |
                               [POST /toasts/activate]
                                         |
                                 [Broadcast: overlay.{key}]
                                         |
                                         v
                     [Overlay Page (Livewire): show toast -> animate]
                                         |
                                 [Auto-dismiss or Clear]
```

**Realtime channels**

- `chat.messages` (public) — Control Panel subscribes; receives new chat messages in real time.
- `overlay.{key}` (public) — Overlay subscribes; receives `ToastShow` and `ToastHide` events.

**State machine (toast)**

- `idle` → `showing` → (`auto_dismiss` timeout) → `hiding` → `idle`\
  Or `showing` → `hiding` via manual Clear.

---

## 2) Core Features & Requirements

- **Overlay (OBS)**

  - Transparent background.
  - Single active toast at a time (queued later if needed; v1 keep it single).
  - Props configurable via query params: `?theme=dark&fontScale=1.0&safeMargin=24&animation=slide-up`.
  - Safe fonts + emoji support.

- **Control Panel**

  - Live chat stream with basic meta (display name, badges, message text, timestamps).
  - Search/filter (by username / contains).
  - “Activate toast” and “Clear toast” buttons per message / global.
  - Optional “duration” input (seconds) when activating.

- **Relay**

  - `php artisan twitch:relay --channel=<channel>`\
    Connect to Twitch IRC and `JOIN #<channel>`, parse `PRIVMSG`, normalize, store, broadcast.

- **No auth** (local)

  - One **overlay key** from `.env` to reduce accidental control: e.g. `OVERLAY_KEY=local`. Overlay route includes `{key}` segment.

---

## 3) Tech Stack & Packages

- **Laravel 12** (PHP 8.3+ assumed)
- **Laravel Reverb** (broadcast driver)
- **Livewire 3**
- **Flux Pro UI** (for Control Panel components)
- **DB**: SQLite for local simplicity
- **Front build**: Vite
- **Twitch IRC**: raw socket via PHP stream or ReactPHP; minimal dependency footprint

---

## 4) Environment / .env

```dotenv
APP_ENV=local
APP_URL=http://localhost:8000

# Reverb
REVERB_APP_ID=chat-overlay
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
BROADCAST_CONNECTION=reverb

# Overlay guard
OVERLAY_KEY=local

# Twitch IRC
TWITCH_NICK=your_twitch_username
TWITCH_OAUTH=oauth:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx   # from https://twitchapps.com/tmi/
TWITCH_CHANNEL=targetchannel                         # e.g. "mychannel"
TWITCH_IRC_HOST=irc.chat.twitch.tv
TWITCH_IRC_PORT=6667
```

---

## 5) Database Schema (SQLite)

**migrations**

- `chat_messages`

  - id (pk), `twitch_msg_id` (nullable string), `username`, `display_name`, `badges` (json), `message` (text), `received_at` (ts), `meta` (json, nullable)

- `overlay_states` (single row per overlay key)

  - id (pk), `overlay_key` (string unique), `active_message_id` (nullable fk), `dismiss_at` (nullable ts), `style` (json)

---

## 6) Events, Broadcasting & Payloads

**Events**

- `NewChatMessage` → channel: `chat.messages`

  - Payload:
    ```json
    {
      "id": 123,
      "display_name": "Alice",
      "username": "alice_",
      "badges": [{"name":"mod"}],
      "message": "Hello world!",
      "received_at": "2025-08-19T16:12:05Z"
    }
    ```

- `ToastShow` → channel: `overlay.{key}`

  - Payload:
    ```json
    {
      "message": {
        "display_name": "Alice",
        "username": "alice_",
        "badges": [...],
        "message": "Hello world!"
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

- `ToastHide` → channel: `overlay.{key}`

  - Payload: `{ "reason": "expired" | "manual" }`

---

## 7) Routes & Controllers

**Web (public)**

- `GET /overlay/{key}` → Overlay page (Livewire component)\
  Validates `{key}` == `env('OVERLAY_KEY')` else 404.

- `GET /control` → Control panel (Livewire + Flux UI)

**API**

- `POST /api/toasts/activate`\
  Body:

  ```json
  { "message_id": 123, "duration_ms": 8000, "theme":"dark", "fontScale": 1.0, "animation":"slide-up", "safeMargin":24 }
  ```

  Action:

  - Update `overlay_states`.
  - Broadcast `ToastShow`.

- `POST /api/toasts/clear`\
  Action:

  - Null `active_message_id` + broadcast `ToastHide`.

---

## 8) Livewire Components

**Overlay**

- `App\Livewire\Overlay\ToastDisplay`
  - Mount: read URL params (`theme`, `fontScale`, `animation`, `safeMargin`).
  - Subscribe to `overlay.{key}` via Echo/Reverb.
  - `onToastShow(payload)`: set component state, trigger enter animation, start timer if `duration_ms`.
  - `onToastHide()`: trigger exit animation → clear.

**Control Panel**

- `App\Livewire\Control\ChatFeed`
  - UI: Flux Pro UI list/table + filters + buttons.
  - Subscribes to `chat.messages` to prepend entries.
  - Actions:
    - `activate(messageId)` → POST `/api/toasts/activate` (optionally pass duration).
    - `clear()` → POST `/api/toasts/clear`.
  - Include a small preview panel that mirrors current active toast state (subscribe to `overlay.{key}` to show what’s live).

---

## 9) Frontend (Echo + Reverb) wiring

- `resources/js/bootstrap.js`: configure `window.Echo` with Reverb (per Laravel docs).
- Ensure Vite builds include the Echo bootstrap for both Overlay and Control routes (or a shared layout).
- Overlay page CSS:
  - Transparent bg: `body{ background: transparent; }`
  - Large, legible text; clamp lines; emoji safe.
  - Animations (enter/exit): CSS classes or small Alpine/Livewire transitions.

---

## 10) Flux Pro UI Usage (Control Panel)

- Layout: header with channel name + status; main grid with:
  - Left: Live chat feed list (virtualized if needed).
  - Right: Active toast preview + “Clear” button + duration/theme controls.
- Use Flux components for:
  - Search input (filter by text or user).
  - Table/List rows (username, message, time, Activate button).
  - Non-blocking toasts/snackbars for operator feedback.

---

## 11) Artisan Command: `twitch:relay`

**Signature**

```
php artisan twitch:relay --channel={channel?}
```

If not provided, read `TWITCH_CHANNEL`.

**Responsibilities**

- Open TCP stream to `TWITCH_IRC_HOST:TWITCH_IRC_PORT`.
- Write:
  - `PASS {TWITCH_OAUTH}`
  - `NICK {TWITCH_NICK}`
  - `JOIN #{channel}`
- Loop lines:
  - Respond to `PING :tmi.twitch.tv` with `PONG :tmi.twitch.tv`.
  - Parse `PRIVMSG` lines → extract `display-name`, badges (from tags), message text, and username.
  - Persist to `chat_messages`.
  - Dispatch `NewChatMessage` event (broadcast).

**Resilience**

- Reconnect with exponential backoff on disconnect.
- Log minimal errors; keep running.
- Optional `--dry-run` to log only.

**(Optional) Simulator**

- `php artisan twitch:simulate` to generate fake messages for UI dev.

---

## 12) OBS Setup

- Add **Browser Source**:
  - URL: `http://localhost:8000/overlay/local?theme=dark&fontScale=1.0&safeMargin=24&animation=slide-up`
  - Width/Height: match canvas (e.g., 1920×1080).
  - **Custom CSS**: (leave blank) — page is transparent by default.
  - Refresh cache on scene activate (optional).

---

## 13) Animations & UX Details (Overlay)

- Enter: translateY(20px) → 0, fade in 200–300ms.
- Exit: fade out 200ms.
- Text layout:
  - `display_name` (bold), badges inline icons (small).
  - Message wraps to 2–3 lines, ellipsis after.
- “Safe margin” applied via container padding on all sides.
- Font scale multiplies base `rem`.

---

## 14) Testing & Dev Workflow

- **Happy path**

  - Start Reverb: `php artisan reverb:start`
  - Start app: `php artisan serve`
  - Start relay: `php artisan twitch:relay --channel=...`
  - Open `/control` and `/overlay/local` in browser. Activate a message → see toast in overlay and in OBS.

- **Feature tests**

  - `Toast activation clears previous`
  - `Auto-dismiss fires ToastHide`
  - `Control panel receives NewChatMessage broadcast`

- **Unit tests**

  - IRC line parsing (tags → model fields)
  - Options merge (URL params + activation payload → overlay state)

---

## 15) Security / Non‑Goals (v1)

- **No authentication** (local only).
  - Use `OVERLAY_KEY` gate and keep HTTP port bound to localhost for safety.
- \*\*No persistence of OAuth beyond \*\*\`\`.
- **No moderation actions** (ban, timeout, etc.).
- **No message queueing** (only one active toast at a time).

---

## 16) Milestones

1. **Scaffold & Reverb wired**

   - Laravel 12 + Reverb + Livewire 3 + Flux UI installed
   - Echo bootstrapped; dummy broadcasts round‑trip.

2. **Overlay MVP**

   - Static toast component with URL‑based appearance control
   - Receives `ToastShow/ToastHide` test events.

3. **Control Panel MVP**

   - Live feed UI, manual activation / clear
   - Shows current live preview.

4. **IRC Relay**

   - Command connects to Twitch, saves, broadcasts `NewChatMessage`
   - Control feed updates live.

5. **Polish**

   - Animations, font scaling, badge rendering, filtering, simulator.

---

## 17) Implementation Tasks (Agent‑Friendly Checklist)

-
