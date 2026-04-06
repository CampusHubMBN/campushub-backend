FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev supervisor \
    && docker-php-ext-install pdo_mysql mbstring xml bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Raise PHP upload limits to match app constraints (5 MB CV/avatar)
RUN echo "upload_max_filesize = 6M\npost_max_size = 8M\nmemory_limit = 256M" \
    > /usr/local/etc/php/conf.d/uploads.ini

# Fix Apache MPM conflict — wipe all MPM modules, enable only prefork
RUN find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete \
    && find /etc/apache2/mods-enabled/ -name "mpm_*.conf" -delete \
    && a2enmod mpm_prefork

# Set Apache document root to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copy the rest of the application
COPY . .

# Finish composer setup
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE ${PORT:-80}

CMD ["/usr/local/bin/docker-entrypoint.sh"]
