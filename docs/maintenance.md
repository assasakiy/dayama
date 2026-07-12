# Maintenance

## Daily

- Check `storage/logs/laravel.log` for errors
- Monitor queue worker if using queues
- Check disk space for media uploads

## Weekly

- Rotate logs: `php artisan log:clear`
- Clean expired cache: `php artisan cache:clear`

## Monthly

- Audit user accounts and permissions
- Check for orphaned media files
- Review and clean spam comments

## Commands

| Command | Description |
|---------|-------------|
| `php artisan optimize` | Cache routes, config, views |
| `php artisan optimize:clear` | Clear all cache |
| `php artisan storage:link` | Create storage symlink |
| `php artisan cache:clear` | Clear application cache |
| `php artisan config:clear` | Clear config cache |
| `php artisan route:clear` | Clear route cache |
| `php artisan view:clear` | Clear compiled views |
