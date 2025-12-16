FROM php:8.2-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html
