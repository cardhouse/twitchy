# Troubleshooting

## Common Issues

**Toasts not appearing on overlay:**
- Verify overlay key matches `.env` setting
- Check browser console for JavaScript errors
- Test `/overlay/local/pending-toasts` endpoint
- Ensure Reverb is running: `php artisan reverb:start`

**Reverb connection issues:**
- Check `BROADCAST_CONNECTION=reverb` in `.env`
- Verify Reverb server is running
- Check port 8080 is not blocked
- Test WebSocket connection in browser console

**IRC relay not connecting:**
- Validate Twitch OAuth token (regenerate at https://twitchapps.com/tmi/)
- Ensure channel name has no `#` prefix
- Check network connectivity
- Review logs: `storage/logs/laravel.log`

**Performance issues:**
- Monitor message throughput
- Consider message rate limiting
- Check cache driver performance
- Review database queries (N+1 issues)

## Debug Commands

```bash
# Check overlay events
curl http://your-domain.test/overlay/local/pending-toasts

# Test broadcast
php artisan tinker
>>> broadcast(new App\Events\MessageReceived($message));

# View configuration
php artisan config:show overlay
php artisan config:show broadcasting

# Tail logs
tail -f storage/logs/laravel.log

# Check Reverb status
ps aux | grep reverb

# Test IRC connection (dry run)
php artisan twitch:relay --channel=yourchannel --dry-run
```

## Browser Console Debugging

Open browser console on overlay page to check:
- WebSocket connection status
- JavaScript errors
- Reverb event reception
- Toast rendering issues

## External Tools

- [Twitch OAuth Token Generator](https://twitchapps.com/tmi/)
- [OBS Studio](https://obsproject.com/)
- [Twitch IRC Documentation](https://dev.twitch.tv/docs/irc)
