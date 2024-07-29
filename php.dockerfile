FROM php:8-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN apk add --update linux-headers
RUN docker-php-ext-install sockets


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer