# Dockerfile for PHP 8.3 with Laravel
FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    sudo \
    supervisor \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG WWWUSER
ARG WWWGROUP

RUN groupadd --force -g $WWWGROUP laravel
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER laravel

COPY . .
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/www/html/storage /var/www/html/storage/logs /var/www/html/bootstrap/cache
RUN mkdir -p /etc/supervisor/conf.d

COPY ./supervisor/supervisor.conf /etc/supervisor/conf.d/

RUN chown -R laravel:laravel /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/storage/logs /var/www/html/bootstrap/cache
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN touch /var/www/html/storage/logs/worker.log
RUN chmod -R 775 /var/www/html/storage/logs/worker.log
RUN service  supervisor start

USER laravel

EXPOSE 80

CMD ["php-fpm"]

# Set the script as the entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
