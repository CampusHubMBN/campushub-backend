#!/bin/bash

echo "==> Fixing Apache MPM..."
find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete 2>/dev/null || true
find /etc/apache2/mods-enabled/ -name "mpm_*.conf" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# Railway assigns a dynamic port — configure Apache to listen on it
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port $PORT..."
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/*.conf

echo "==> Clearing config cache..."
php artisan config:clear || echo "config:clear failed, continuing..."

echo "==> Running migrations..."
php artisan migrate --force || echo "migrate failed, continuing..."

echo "==> Starting Apache on port $PORT..."
exec apache2-foreground
