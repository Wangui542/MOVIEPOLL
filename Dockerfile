# Use an official PHP image as the base
FROM php:8.2-apache

# Install mysqli and pdo_mysql extensions (Fixes the "Class mysqli not found" error)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module (optional but recommended)
RUN a2enmod rewrite

# Copy your custom Apache config (DirectoryIndex fix)
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy all your project files into the Apache web directory
COPY . /var/www/html/

# Fix permissions for the web server user
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
