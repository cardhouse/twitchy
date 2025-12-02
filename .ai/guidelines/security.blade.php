# Security Considerations

## Overlay Key Protection

- **Never commit** actual overlay keys to version control
- Use different keys per environment
- Validate overlay key on every request to `/overlay/{key}`
- Return 404 for invalid keys (not 403 to avoid leaking existence)

**Example in controller:**
```php
public function show(Request $request, string $key): Response
{
    if ($key !== config('overlay.key')) {
        abort(404);
    }

    // ... rest of method
}
```

## Webhook Security

- CSRF protection disabled for webhook routes (external APIs)
- Consider adding API key authentication for production
- Validate all incoming webhook data
- Rate limit webhook endpoints

**Disable CSRF for webhooks:**
```php
Route::post('/hooks/chat-message', [ChatHookController::class, 'handleChatMessage'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
```

## Production Hardening

- Use HTTPS in production
- Implement rate limiting on all public routes
- Validate and sanitize all user input
- Monitor for abuse patterns
- Use Redis for production cache/session driver

**Rate limiting example:**
```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/hooks/chat-message', ...);
});
```

## Important Security Notes

- Overlay keys provide **basic protection only**, not true security
- In production, consider adding API authentication for webhooks
- Monitor logs for suspicious activity
- Keep dependencies updated
- Use strong, unique overlay keys per environment
