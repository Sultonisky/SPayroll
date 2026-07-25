# --- Stage 1: Base PHP image with extensions ---
FROM php:8.3-fpm-alpine AS base

# Install system dependencies for PHP extensions
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    icu-libs \
    libxml2-dev \
    pkgconfig

# Install PHP extensions
# fileinfo  : required by intervention/image
# xml + xmlreader + xmlwriter : required by maatwebsite/excel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        intl \
        bcmath \
        fileinfo \
        xml \
        xmlreader \
        xmlwriter

# --- Stage 2: PHP Dependencies ---
FROM base AS vendor

WORKDIR /app

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies (no scripts — artisan not available yet)
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader


# --- Stage 3: Frontend Assets ---
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci && npm run build


# --- Stage 4: Final Production Image ---
FROM base

WORKDIR /var/www/html

# Install runtime dependencies only
RUN apk add --no-cache \
    nginx \
    supervisor

# Copy PHP and Nginx configurations
# Note: folder name is "Docker" (capital D) — matched exactly
COPY Docker/nginx.conf /etc/nginx/nginx.conf
COPY Docker/supervisord.conf /etc/supervisord.conf

# Copy application code first, then overlay build artifacts
# Order matters: vendor & public/build must come AFTER COPY . .
# so they are not overwritten
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Run post-install scripts now that the full app is present
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --no-interaction --optimize --no-dev \
    && rm /usr/bin/composer

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Entrypoint
COPY Docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
