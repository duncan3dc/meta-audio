ARG PHP_VERSION=7.3
FROM php:${PHP_VERSION}-cli-alpine

RUN apk add --no-cache git zip $PHPIZE_DEPS

RUN pecl install pcov && docker-php-ext-enable pcov

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN git config --global --add safe.directory /app
