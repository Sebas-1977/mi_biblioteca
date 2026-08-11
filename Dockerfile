FROM php:8.2-apache

# Instalar extensiones de base de datos MySQL/PDO
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Apuntar la raíz web (DocumentRoot) de Apache a la carpeta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Ajustar permisos para Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
