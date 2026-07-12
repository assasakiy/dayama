# Installation

## Requirements

- PHP 8.3+
- MySQL 8.0+
- Composer 2
- Node.js 20+ (build only)
- GD/Imagick PHP extension
- ZIP PHP extension

## Setup

```bash
git clone <repo> test-blog
cd test-blog

# Install PHP dependencies
composer install

# Copy environment
cp .env.example .env
php artisan key:generate

# Database
# Create MySQL database, then update .env:
# DB_DATABASE=modernblog
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seeders
php artisan migrate --seed

# Storage link
php artisan storage:link

# Build frontend
npm install
npm run build

# Serve
php artisan serve
```

## Media Library

Image optimizers require system binaries:

```bash
# Ubuntu/Debian
sudo apt install jpegoptim optipng pngquant gifsicle webp

# macOS
brew install jpegoptim optipng pngquant gifsicle webp
```

## Vite Dev Server

```bash
npm run dev
```
