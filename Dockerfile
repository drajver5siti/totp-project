FROM php:8.2-fpm

RUN apt update && apt install -y \
    git \ 
    zip \
    libpng-dev

RUN docker-php-ext-install pdo_mysql gd

ENV TZ=Europe/Skopje

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN printf '[PHP]\ndate.timezone = "Europe/Skopje"\n' > /usr/local/etc/php/conf.d/tzone.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/ib-proekt.com