#local
#FROM php:8.2-fpm
#
## install packages
#RUN apt-get update && \
#    apt-get install -y \
#    libpq-dev \
#    libzip-dev \
#    unzip \
#    && docker-php-ext-install pdo pdo_pgsql
#
## install Composer
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
#
#WORKDIR /var/www/html
#
#COPY . .
#RUN composer install
#
#
#RUN php artisan key:generate
#
#EXPOSE 8000
#
#CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

#server
FROM php:8.2-fpm

# Install necessary packages
RUN apt-get update && \
    apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Run migrations
RUN php artisan migrate --force

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


