# Dockerfile – App PHP IMS
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable mysqli

# Activar mod_rewrite por si tu app lo necesita
RUN a2enmod rewrite

# Copiar el código de la app
COPY . /var/www/html/

# Permisos recomendados
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]