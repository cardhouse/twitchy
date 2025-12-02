# Twitchy - Stream Overlay System

## Project Overview

Twitchy is a Laravel-based real-time overlay system for streamers to display chat messages on their streams. It provides a clean, customizable overlay system that displays Twitch chat messages as toasts on stream broadcasts.

### Core Purpose
- Display Twitch chat messages as customizable toasts on stream overlays
- Provide real-time updates via Laravel Reverb broadcasting
- Offer a control panel for managing which messages appear on the overlay
- IRC relay for connecting to Twitch chat

### Key Technologies
- **Backend**: Laravel 12, PHP 8.4+
- **Real-time**: Laravel Reverb (WebSocket broadcasting)
- **UI Components**: Livewire 3 + Volt (prefer Volt for single-file components)
- **UI Library**: Flux Pro UI (full Pro edition access)
- **Styling**: Tailwind CSS v4
- **Testing**: Pest v4 (including browser testing capabilities)
- **Database**: SQLite (development), MySQL/PostgreSQL (production)

### Testing URLs
- Overlay: `http://twitchy.test/overlay/local`
- Control Panel: `http://twitchy.test/control`

### Documentation References
- Project guidelines: `/docs/project_guideline.md`
- Implementation guide: `/docs/implementation-guide.md`
- Milestone documents: `/docs/milestone-*.md`
- Main README: `/README.md`
