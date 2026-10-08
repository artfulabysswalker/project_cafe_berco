#!/bin/sh
set -e

cd /var/www/html

# Pastikan symlink public/storage -> storage/app/public (untuk foto menu, QR, logo)
php artisan storage:link >/dev/null 2>&1 || true

# Pastikan direktori runtime yang dibutuhkan ada (aman terhadap volume kosong)
mkdir -p storage/logs storage/framework/sessions storage/framework/cache/data storage/framework/views

# Jangan menaruh cache config hasil build lama di dalam image
php artisan config:clear >/dev/null 2>&1 || true

# PHP-FPM di background, Nginx di foreground (container tetap hidup)
php-fpm &

exec nginx -g 'daemon off;'