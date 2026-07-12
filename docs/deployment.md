# Deployment

## Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Generate app key: `php artisan key:generate`
3. Cache config/routes/views:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. Build assets: `npm ci && npm run build`
5. Set up scheduler cron:
   ```
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```
6. Set up queue worker: `php artisan queue:work --daemon`

## Web Server

### Nginx

```nginx
server {
    listen 80;
    server_name test-blog.test;
    root /path/to/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache (Laragon)

`.htaccess` in public/ is pre-configured for Laravel.

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_URL` | Application URL | `http://localhost` |
| `MEDIA_DISK` | Media storage disk | `public` |
| `SANCTUM_STATEFUL_DOMAINS` | SPA domains for Sanctum | `` |
| `SESSION_DRIVER` | Session driver | `file` |
