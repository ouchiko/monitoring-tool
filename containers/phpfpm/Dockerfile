FROM php:7.0-fpm-alpine

RUN apk add --no-cache --virtual .persistent-deps icu-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql intl

COPY src /var/www/html

RUN rm -rf /var/www/html/logs/* \
    && chown -R www-data:www-data /var/www/html
