# syntax=docker/dockerfile:1
#
# The app has no runtime Composer dependencies (Composer/PHPUnit are
# dev-only, used in CI) - login.php/register.php/dashboard.php require
# src/Auth.php and src/Registration.php directly. So this is a single-stage
# build: just the PHP/Apache runtime plus the app's own files.
FROM php:8.2-apache

# pdo_mysql lets Auth.php / Registration.php talk to MySQL via PDO (see
# public/config.php, which reads DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASSWORD).
RUN docker-php-ext-install pdo_mysql

# The app's entry points live in public/, so Apache's document root needs to
# point there instead of the default /var/www/html.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY public/ ./public/
COPY src/ ./src/

# Apache runs as www-data; make sure it owns the files it serves.
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
