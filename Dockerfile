FROM php:8.2-apache

# Instalar dependencias del sistema y Composer
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar extensiones de MySQL/PDO para PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Apuntar la raíz web (DocumentRoot) de Apache a la carpeta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Copiar el código del proyecto al contenedor
COPY . /var/www/html/

# Instalar dependencias de Composer
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos para Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
