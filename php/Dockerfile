FROM php:8.2-fpm-alpine

# Install curl development libraries and the extension
RUN apk add --no-cache curl-dev \
    && docker-php-ext-install curl
