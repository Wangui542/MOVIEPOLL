# Use an official PHP image as a parent image
FROM php:8.2-apache

# Copy the custom Apache config file, which includes the DirectoryIndex fix
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# (Optional, but often needed for clean URLs) Enable the Apache rewrite module
RUN a2enmod rewrite

# Copy your project files into the web directory
COPY . /var/www/html/

# Fix permissions for the web server user
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (default for Apache)
EXPOSE 80
