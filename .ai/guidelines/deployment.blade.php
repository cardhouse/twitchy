# Deployment Considerations

## Production Requirements

- PHP 8.4+ with required extensions
- MySQL or PostgreSQL (not SQLite)
- Redis for cache and session driver
- Supervisor for queue workers and Reverb
- Web server: Nginx or Apache with proper configuration

## Environment Setup

- Set appropriate `APP_ENV=production`
- Configure production database credentials
- Use Redis for broadcasting and cache
- Set up queue workers with Supervisor
- Configure HTTPS/SSL certificates

## Supervisor Configuration

```ini
[program:twitchy-reverb]
command=php /path/to/artisan reverb:start
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/twitchy-reverb.log
stderr_logfile=/var/log/twitchy-reverb-error.log

[program:twitchy-irc]
command=php /path/to/artisan twitch:relay --channel=yourchannel
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/twitchy-irc.log
stderr_logfile=/var/log/twitchy-irc-error.log

[program:twitchy-worker]
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
stdout_logfile=/var/log/twitchy-worker.log
stderr_logfile=/var/log/twitchy-worker-error.log
```

## Building for Production

```bash
# Build frontend assets
npm run build

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Format code
vendor/bin/pint --dirty

# Run tests
php artisan test
```

## Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/twitchy/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Performance Optimization

- Enable OPcache
- Use Redis for caching
- Implement database connection pooling
- Add CDN for static assets
- Monitor application performance
- Use queue workers for background processing

## Monitoring

- Set up application monitoring (e.g., Laravel Telescope, Sentry)
- Monitor Reverb WebSocket connections
- Track IRC relay uptime
- Monitor cache hit rates
- Set up alerts for errors and downtime
