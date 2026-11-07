FROM php:8.2-apache

# PostgreSQL driver for PHP (needed for Supabase)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY . /var/www/html/

# Folders the site writes uploaded files into
RUN mkdir -p /var/www/html/uploads/receipts /var/www/html/modules /var/www/html/submissions \
    && chown -R www-data:www-data /var/www/html/uploads /var/www/html/modules /var/www/html/submissions

EXPOSE 80
