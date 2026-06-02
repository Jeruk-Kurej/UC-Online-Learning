FROM php:8.4-cli

# Force rebuild - PHP 8.4 required
RUN php -v

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    webp

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (including zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configure PHP for file uploads
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/php.ini-production && \
    echo "post_max_size = 12M" >> /usr/local/etc/php/php.ini-production && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini-production && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini-production && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (production only) - skip scripts to avoid artisan errors
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci

# Copy application files
COPY . .

# Build assets with Vite (generates /public/build/)
RUN npm run build && \
    echo "=== Vite Build Complete ===" && \
    ls -la public/build/ && \
    cat public/build/manifest.json

# Now run composer scripts after files are copied
RUN composer dump-autoload --optimize

# Run Laravel package discovery
RUN php artisan package:discover --ansi

# Clear and cache Laravel config
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Set permissions
RUN chmod -R 755 /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 8000

# Start server - migrations optional, won't crash if DB unavailable
CMD echo "=== RAILWAY STARTUP ===" && \
    echo "PORT: ${PORT:-8000}" && \
    echo "APP_ENV: ${APP_ENV:-production}" && \
    echo "APP_DEBUG: ${APP_DEBUG:-false}" && \
    php artisan config:clear && \
    php artisan route:clear && \
    echo "=== Running migrations ===" && \
    (php artisan migrate --force 2>&1 || echo "Migration failed or skipped") && \
    echo "=== Creating storage symlink ===" && \
    (php artisan storage:link 2>&1 || echo "Storage link already exists") && \
    echo "=== Starting Laravel Server ===" && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000} --no-reload