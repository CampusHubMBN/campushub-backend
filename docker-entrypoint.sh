#!/bin/bash
set -e

# Fix MPM conflict at runtime — remove all MPM modules, enable only prefork
find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete 2>/dev/null || true
find /etc/apache2/mods-enabled/ -name "mpm_*.conf" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# Clear Laravel config/cache so env vars are always fresh
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Seed only if database is empty (checks users table)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ]; then
  echo "Database empty — running seeders..."
  php artisan db:seed --force
else
  echo "Database already seeded (${USER_COUNT} users) — skipping."
fi

exec apache2-foreground
