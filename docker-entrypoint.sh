#!/bin/bash

# Flush output immediately
exec 1>&1
exec 2>&2

echo "==> PORT is: ${PORT:-80}"

# Railway assigns a dynamic port
PORT="${PORT:-80}"

echo "==> Fixing Apache MPM..."
find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete 2>/dev/null || true
find /etc/apache2/mods-enabled/ -name "mpm_*.conf" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

echo "==> Writing ports.conf for port $PORT..."
echo "Listen $PORT" > /etc/apache2/ports.conf

echo "==> Updating VirtualHost port..."
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/*.conf

echo "==> Setting ServerName to suppress warning..."
echo "ServerName localhost" >> /etc/apache2/apache2.conf

echo "==> Clearing and recaching config..."
php artisan config:clear 2>&1 || echo "config:clear failed"
php artisan config:cache 2>&1 || echo "config:cache failed"

echo "==> Running migrations..."
php artisan migrate --force 2>&1 || echo "migrate failed"

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>&1 || echo "storage:link failed"

echo "==> Starting Apache + queue worker via supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
