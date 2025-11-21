# ===========================
# 1) BASE IMAGE PHP + APACHE
# ===========================
FROM php:8.2-apache AS production

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Remove default Apache files
RUN rm -rf /var/www/html/*

# Copy application code
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Apache port
EXPOSE 80

# Metadata (Jenkins injects BUILD_VERSION)
ARG BUILD_VERSION="dev"
ENV APP_VERSION=$BUILD_VERSION

CMD ["apache2-foreground"]