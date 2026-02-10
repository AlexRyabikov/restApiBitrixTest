FROM php:8.2-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql mbstring

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
