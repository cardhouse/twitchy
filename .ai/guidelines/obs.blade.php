# OBS Integration

## Browser Source Setup

1. **Add Browser Source** in OBS
2. **URL**: `http://your-domain.test/overlay/local?theme=dark&fontScale=1.0`
3. **Width**: 1920, **Height**: 1080
4. **Custom CSS**: Leave blank (transparency handled in app)
5. **Refresh browser when scene becomes active**: Optional

## URL Parameters

- `theme`: `dark` or `light` (default: dark)
- `fontScale`: 0.5 to 3.0 (default: 1.0)
- `animation`: `slide-up`, `slide-down`, `slide-left`, `slide-right`, `fade`, `zoom` (default: slide-up)
- `safeMargin`: 0 to 100 pixels from edges (default: 24)

## Example URLs

```
# Dark theme with larger text
http://your-domain.test/overlay/local?theme=dark&fontScale=1.2

# Light theme with slide-down animation
http://your-domain.test/overlay/local?theme=light&animation=slide-down

# Custom margins for safe area
http://your-domain.test/overlay/local?safeMargin=50

# All customizations combined
http://your-domain.test/overlay/local?theme=dark&fontScale=1.5&animation=fade&safeMargin=40
```

## Important Notes

- **Transparency is critical**: The overlay page has a transparent background for OBS integration
- **Test in browser first**: Always test the overlay URL in a browser before adding to OBS
- **Refresh on scene activate**: Optional OBS setting to refresh the browser source when the scene becomes active
- **Resolution matters**: Default is 1920x1080, adjust based on your stream resolution
