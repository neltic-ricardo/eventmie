FROM composer:2.6.5 as builder

LABEL maintainer "Ricardo Negrete<ricardo@projectamp.io>"

WORKDIR /var/www/html

COPY ./src/ .

RUN composer install --no-dev --optimize-autoloader

FROM php:8.2.12-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    libgmp-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql gmp

WORKDIR /var/www/html

COPY --from=builder /var/www/html /var/www/html

EXPOSE 80

CMD  php artisan serve --host=0.0.0.0 --port=80
