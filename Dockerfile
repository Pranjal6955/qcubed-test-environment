FROM php:7.4-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Create a simple test profile.php file to demonstrate the vulnerability
RUN echo '<?php \
// Vulnerable profile.php file for CVE-2020-24914 testing \
if ($_POST["data"]) { \
    $data = $_POST["data"]; \
    // Vulnerable unserialize call \
    $obj = unserialize($data); \
    echo "Deserialized object: "; \
    var_dump($obj); \
} \
?>' > /var/www/html/profile.php

# Create a simple index.php to identify QCubed
RUN echo '<?php \
echo "<h1>QCubed Framework Test Environment</h1>"; \
echo "<p>This is a test environment for CVE-2020-24914</p>"; \
echo "<p>QCubed version: 3.1.1</p>"; \
?>' > /var/www/html/index.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 