# the official PHP image with Apache
FROM php:8.2-apache

# Install required PHP extensions and tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Enable Apache modules
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy the Symfony project files
COPY . /var/www/html

# Set permissions for the project
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy the custom Apache configuration
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable the site configuration
RUN a2ensite 000-default.conf

# Expose port 80
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]