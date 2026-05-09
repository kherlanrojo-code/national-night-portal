FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# 2. Install Node.js 22 (Vite requires 20+)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

# 3. Update Apache Config
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Set working directory
WORKDIR /var/www/html

# 7. Copy project files
COPY . .

# 8. Install Laravel (PHP) dependencies
RUN composer install --no-dev --optimize-autoloader

# 9. Clean install NPM and Build Assets
# We delete existing node_modules to fix that "native binding" error
RUN rm -rf node_modules package-lock.json && \
    npm install && \
    npm run build

# 10. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 11. Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]