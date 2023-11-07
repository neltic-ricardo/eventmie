FROM php:8.1-cli

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

COPY ./src /var/www/html

EXPOSE 80

CMD  php artisan serve --host=0.0.0.0 --port=80
