#!/bin/bash
set -e

echo "==> Fixing Apache MPM..."
find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete 2>/dev/null || true
find /etc/apache2/mods-enabled/ -name "mpm_*.conf" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

echo "==> Clearing config cache..."
php artisan config:clear

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Starting Apache..."
exec apache2-foreground
