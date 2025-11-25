# Use official PHP + Apache image
FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install common PHP extensions (optional)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files into Apache web directory
COPY . /var/www/html/

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
