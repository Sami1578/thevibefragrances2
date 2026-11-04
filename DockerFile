FROM php:8.5.12-apache

# Install required database extensions for MySQLi and PDO
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite for clean URL handling
RUN a2enmod rewrite

# Copy project files into the container web root
COPY . /var/www/html/

# Set proper file permissions for Apache
RUN chown -R www-data:www-data /var/www/html