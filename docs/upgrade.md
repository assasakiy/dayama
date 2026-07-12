# Upgrade Guide

## Laravel Framework

```bash
composer update laravel/framework
# Check upgrade guide: https://laravel.com/docs/upgrade
```

## Frontend Assets

```bash
npm update
npm run build
```

## Database Migrations

```bash
php artisan migrate
```

## Clearing Cache

```bash
php artisan optimize:clear
php artisan optimize
```
