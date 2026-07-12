# Backup & Restore

## Database

```bash
# Backup
mysqldump -u root -p modernblog > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p modernblog < backup_20260705.sql
```

## Media Files

```bash
# Backup storage
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public

# Restore
tar -xzf storage_backup_20260705.tar.gz -C storage/app/public
```

## Full Project

```bash
# Backup entire project (excluding vendors)
tar -czf project_backup_$(date +%Y%m%d).tar.gz \
    --exclude=vendor \
    --exclude=node_modules \
    --exclude=.git \
    --exclude=storage/framework/cache/data \
    .
```

## Automated Backup (Laravel scheduler)

Add to `routes/console.php`:

```php
Schedule::command('backup:run')->daily()->at('03:00');
```
